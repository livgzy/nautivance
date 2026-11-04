<?php

use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::admin')] class extends Component
{
    use WithPagination;

    private const STATUSES = ['draft', 'published', 'archived'];
    private const SORTABLE = ['title', 'status', 'published_at', 'created_at'];

    #[Url(as: 'q')]
    public string $query = '';

    #[Url(as: 'status')]
    public string $status = '';

    #[Url(as: 'kategori')]
    public string $categorySlug = '';

    // Locked: nilai ini cuma bisa diubah dari server (lewat sortBy), bukan dari browser.
    #[Locked]
    public string $sortField = 'created_at';

    #[Locked]
    public string $sortDirection = 'desc';

    #[Locked]
    public ?int $confirmingDeleteId = null;

    /**
     * boot() jalan di SETIAP request Livewire (render awal, klik tombol, paginasi, dst),
     * jadi sesi yang sudah kedaluwarsa / bukan admin langsung ditolak. Ini penting karena
     * request aksi masuk lewat /livewire/update, bukan lewat route halaman ini.
     */
    public function boot(): void
    {
        $this->authorizeAdmin();
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->is_admin, 403);
    }

    public function search(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset('query', 'status', 'categorySlug');
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, self::SORTABLE, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function setStatus(int $id, string $status): void
    {
        $this->authorizeAdmin();

        // Whitelist: nilai status dari browser tidak dipercaya begitu saja.
        abort_unless(in_array($status, self::STATUSES, true), 422);

        $article = Article::findOrFail($id);
        $article->status = $status;

        if ($status === 'published' && ! $article->published_at) {
            $article->published_at = now();
        }

        $article->save();

        Log::info('Admin mengubah status artikel', [
            'admin_id' => auth()->id(),
            'article_id' => $article->id,
            'status' => $status,
            'ip' => request()->ip(),
        ]);

        $this->dispatch('notify', message: 'Status artikel diperbarui.', type: 'success');
    }

    public function confirmDelete(int $id): void
    {
        $this->authorizeAdmin();

        // null kalau artikelnya tidak ada, jadi modal tidak terbuka untuk ID asal-asalan.
        $this->confirmingDeleteId = Article::whereKey($id)->value('id');
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function delete(): void
    {
        $this->authorizeAdmin();

        $id = $this->confirmingDeleteId;

        if (! $id) {
            return;
        }

        // Pembatas hapus massal kalau sesi admin sampai dibajak: maks 20 hapus / menit.
        $key = 'admin-delete-article:' . auth()->id();

        if (RateLimiter::tooManyAttempts($key, 20)) {
            $this->confirmingDeleteId = null;
            $this->dispatch('notify', message: 'Terlalu banyak penghapusan. Coba lagi sebentar lagi.', type: 'error');

            return;
        }

        RateLimiter::hit($key, 60);

        $article = Article::with('resource')->find($id);
        $this->confirmingDeleteId = null;

        if (! $article) {
            $this->dispatch('notify', message: 'Artikel sudah tidak ada.', type: 'error');

            return;
        }

        $title = $article->title;
        $thumbnail = $article->thumbnail;
        $resourcePath = $article->resource?->file_path;

        // Baris di tabel subtype (article_jobs, dst) ikut terhapus lewat cascadeOnDelete.
        $article->delete();

        // File fisik dihapus SETELAH baris database berhasil terhapus.
        $this->deleteStoredFile($thumbnail);
        $this->deleteStoredFile($resourcePath);

        Log::info('Admin menghapus artikel', [
            'admin_id' => auth()->id(),
            'article_id' => $id,
            'title' => $title,
            'ip' => request()->ip(),
        ]);

        $this->dispatch('notify', message: 'Artikel berhasil dihapus.', type: 'success');
    }

    /** Hanya hapus file di storage publik. URL eksternal dan path mencurigakan diabaikan. */
    private function deleteStoredFile(?string $path): void
    {
        if (blank($path)
            || preg_match('#^[a-z][a-z0-9+.-]*:#i', $path)
            || str_contains($path, '..')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    /** URL gambar yang aman: http(s) atau path storage. Skema lain (javascript:, data:) ditolak. */
    private function thumbUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        if (str_contains($path, '..') || preg_match('#^[a-z][a-z0-9+.-]*:#i', $path)) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }

    public function with(): array
    {
        $term = Str::limit(trim($this->query), 100, '');
        $status = in_array($this->status, self::STATUSES, true) ? $this->status : '';

        $query = Article::query()
            // Kolom `content` (longText) sengaja tidak diambil, daftar tidak membutuhkannya.
            ->select(['id', 'category_id', 'user_id', 'title', 'slug', 'thumbnail', 'status', 'published_at', 'created_at'])
            ->with(['category:id,name,slug', 'author:id,name'])
            ->when($term !== '', function ($q) use ($term) {
                // Karakter wildcard LIKE (% dan _) di-escape supaya dicari apa adanya.
                $like = '%' . addcslashes($term, '\\%_') . '%';

                $q->where(fn ($q) => $q
                    ->where('title', 'like', $like)
                    ->orWhere('slug', 'like', $like)
                    ->orWhere('excerpt', 'like', $like));
            })
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->when($this->categorySlug !== '', fn ($q) => $q->whereHas(
                'category',
                fn ($q) => $q->where('slug', $this->categorySlug)
            ))
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderByDesc('id');

        $articles = (clone $query)->paginate(10);

        // Habis menghapus item terakhir di halaman terakhir: mundur ke halaman yang masih ada.
        if ($articles->isEmpty() && $articles->currentPage() > 1) {
            $this->setPage($articles->lastPage());
            $articles = (clone $query)->paginate(10);
        }

        $articles->getCollection()->transform(function ($article) {
            $article->setAttribute('thumb_url', $this->thumbUrl($article->thumbnail));

            return $article;
        });

        return [
            'articles' => $articles,
            'categories' => Category::orderBy('name')->get(['id', 'name', 'slug']),
            'counts' => Article::query()
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
            'deleteTitle' => $this->confirmingDeleteId
                ? Article::whereKey($this->confirmingDeleteId)->value('title')
                : null,
        ];
    }

    public function render()
    {
        return $this->view([])->title('Artikel');
    }
};
?>

<div>
    {{-- Header + tombol tambah --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Kelola semua artikel Nautivance: edit, terbitkan, atau hapus.</p>
        <a href="{{ route('admin.articles.add') }}" wire:navigate
           class="rounded-lg bg-[#d7a72b] px-5 py-2.5 text-sm font-extrabold text-[#10243a] transition hover:brightness-95">
            + Tambah Artikel
        </a>
    </div>

    {{-- Ringkasan --}}
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach([
            ['Total', $counts->sum(), 'text-[#10243a]'],
            ['Published', $counts['published'] ?? 0, 'text-emerald-600'],
            ['Draft', $counts['draft'] ?? 0, 'text-amber-600'],
            ['Archived', $counts['archived'] ?? 0, 'text-slate-500'],
        ] as [$label, $value, $color])
            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</div>
                <div class="mt-1 text-2xl font-bold {{ $color }}">{{ number_format($value) }}</div>
            </div>
        @endforeach
    </div>

    {{-- Pencarian + filter (submit, bukan live) --}}
    <form wire:submit="search" class="mb-6 flex flex-col gap-3 lg:flex-row">
        <input
            type="search"
            wire:model="query"
            maxlength="100"
            placeholder="Cari judul, slug, atau ringkasan..."
            class="flex-1 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm focus:border-[#d7a72b] focus:outline-none focus:ring focus:ring-[#d7a72b]/40"
        >

        <select wire:model="status" class="rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm focus:border-[#d7a72b] focus:outline-none focus:ring focus:ring-[#d7a72b]/40">
            <option value="">Semua Status</option>
            <option value="published">Published</option>
            <option value="draft">Draft</option>
            <option value="archived">Archived</option>
        </select>

        <select wire:model="categorySlug" class="rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm focus:border-[#d7a72b] focus:outline-none focus:ring focus:ring-[#d7a72b]/40">
            <option value="">Semua Kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category->slug }}">{{ $category->name }}</option>
            @endforeach
        </select>

        <button type="submit" class="rounded-lg bg-[#0a3768] px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-[#06254a]">
            Cari
        </button>

        @if($query !== '' || $status !== '' || $categorySlug !== '')
            <button type="button" wire:click="resetFilters" class="px-2 py-2.5 text-sm font-semibold text-[#33465c] hover:text-[#1474bd]">
                Reset
            </button>
        @endif
    </form>

    {{-- Tabel --}}
    <div
        wire:loading.class="opacity-50"
        wire:target="search,resetFilters,sortBy,gotoPage,nextPage,previousPage,setStatus,delete"
        class="overflow-hidden rounded-xl border border-slate-200 bg-white transition-opacity"
    >
        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th scope="col" aria-sort="{{ $sortField === 'title' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}" class="px-4 py-3">
                            <button type="button" wire:click="sortBy('title')" class="inline-flex items-center gap-1 font-semibold uppercase hover:text-[#1474bd]">
                                Artikel
                                @if($sortField === 'title')<span aria-hidden="true">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>@endif
                            </button>
                        </th>
                        <th scope="col" class="px-4 py-3 font-semibold">Kategori</th>
                        <th scope="col" aria-sort="{{ $sortField === 'status' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}" class="px-4 py-3">
                            <button type="button" wire:click="sortBy('status')" class="inline-flex items-center gap-1 font-semibold uppercase hover:text-[#1474bd]">
                                Status
                                @if($sortField === 'status')<span aria-hidden="true">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>@endif
                            </button>
                        </th>
                        <th scope="col" aria-sort="{{ $sortField === 'published_at' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}" class="px-4 py-3">
                            <button type="button" wire:click="sortBy('published_at')" class="inline-flex items-center gap-1 font-semibold uppercase hover:text-[#1474bd]">
                                Terbit
                                @if($sortField === 'published_at')<span aria-hidden="true">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>@endif
                            </button>
                        </th>
                        <th scope="col" class="px-4 py-3 font-semibold">Penulis</th>
                        <th scope="col" class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($articles as $article)
                        @php
                            $badge = match ($article->status) {
                                'published' => 'bg-emerald-100 text-emerald-700',
                                'draft' => 'bg-amber-100 text-amber-700',
                                default => 'bg-slate-200 text-slate-600',
                            };
                        @endphp
                        <tr wire:key="article-{{ $article->id }}" class="align-middle hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($article->thumb_url)
                                        <img src="{{ $article->thumb_url }}" alt="" loading="lazy" referrerpolicy="no-referrer"
                                             class="h-12 w-16 flex-shrink-0 rounded object-cover">
                                    @else
                                        <div class="h-12 w-16 flex-shrink-0 rounded bg-gradient-to-br from-[#0a3768] to-[#1474bd]"></div>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="line-clamp-2 font-semibold text-[#10243a]">{{ $article->title }}</div>
                                        <div class="truncate text-xs text-slate-400">/{{ $article->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $article->category?->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $badge }}">{{ Str::ucfirst($article->status) }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $article->published_at?->format('d M Y') ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $article->author?->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center justify-end gap-x-3 gap-y-1 text-xs font-semibold">
                                    <a href="{{ '#' }}" wire:navigate class="text-[#1474bd] hover:underline">Edit</a>

                                    @if($article->status === 'published' && Route::has('article.show'))
                                        <a href="{{ route('article.show', $article->slug) }}" target="_blank" rel="noopener noreferrer" class="text-slate-600 hover:underline">Lihat</a>
                                    @endif

                                    @if($article->status === 'published')
                                        <button type="button" wire:click="setStatus({{ $article->id }}, 'draft')" class="text-amber-600 hover:underline">Jadikan Draft</button>
                                    @else
                                        <button type="button" wire:click="setStatus({{ $article->id }}, 'published')" class="text-emerald-600 hover:underline">Terbitkan</button>
                                    @endif

                                    <button type="button" wire:click="confirmDelete({{ $article->id }})" class="text-red-600 hover:underline">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                @if($query !== '' || $status !== '' || $categorySlug !== '')
                                    Tidak ada artikel yang cocok dengan filter.
                                @else
                                    Belum ada artikel.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $articles->links(data: ['scrollTo' => false]) }}
    </div>

    {{-- Modal konfirmasi hapus --}}
    @if($confirmingDeleteId)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="delete-title"
            wire:keydown.escape.window="cancelDelete"
        >
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <h2 id="delete-title" class="font-serif text-xl font-bold text-[#10243a]">Hapus artikel ini?</h2>
                <p class="mt-2 text-sm text-slate-600">
                    Artikel <strong class="text-[#10243a]">{{ $deleteTitle }}</strong> akan dihapus permanen beserta data
                    tambahannya. Tindakan ini tidak bisa dibatalkan.
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" wire:click="cancelDelete" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                    <button
                        type="button"
                        wire:click="delete"
                        wire:loading.attr="disabled"
                        wire:target="delete"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="delete">Ya, Hapus</span>
                        <span wire:loading wire:target="delete">Menghapus...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>