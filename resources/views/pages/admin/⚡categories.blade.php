<?php

use App\Models\Category;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::admin')] #[Title('Dashboard Admin — Tambah Kategori')] class extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public bool $showForm = false;

    // Locked: ID yang sedang diedit tidak bisa dipalsukan dari browser.
    #[Locked]
    public ?int $editingId = null;

    public string $name = '';
    public string $slug = '';
    public string $description = '';
    public bool $slugTouched = false;

    // Dicek di SETIAP request Livewire (render pertama maupun update/aksi),
    // bukan cuma saat halaman dibuka.
    public function boot(): void
    {
        $this->authorizeAdmin();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedName(): void
    {
        if ($this->editingId === null && ! $this->slugTouched) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function updatedSlug(): void
    {
        $this->slugTouched = true;
    }

    #[Computed]
    public function categories()
    {
        $term = trim($this->search);
        // Escape wildcard LIKE supaya input "%" atau "_" tidak jadi pola.
        $like = '%' . addcslashes($term, '\\%_') . '%';

        return Category::query()
            ->withCount('articles')
            ->when($term !== '', fn ($q) => $q->where(
                fn ($q) => $q->where('name', 'like', $like)->orWhere('slug', 'like', $like)
            ))
            ->orderBy('name')
            ->paginate(10);
    }

    public function create(): void
    {
        $this->authorizeAdmin();
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $this->authorizeAdmin();
        $category = Category::findOrFail($id);

        $this->resetValidation();
        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description;
        $this->slugTouched = true;
        $this->showForm = true;
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    public function save(): void
    {
        if (! $this->allowMutation()) {
            return;
        }

        $editing = $this->editingId ? Category::findOrFail($this->editingId) : null;

        $this->name = trim($this->name);
        $this->slug = Str::lower(trim($this->slug));
        $this->description = trim($this->description);

        $this->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($editing?->id)],
            'slug' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('categories', 'slug')->ignore($editing?->id)],
            'description' => ['required', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah dipakai.',
            'slug.required' => 'Slug wajib diisi.',
            'slug.regex' => 'Slug hanya boleh huruf kecil, angka, dan tanda hubung (contoh: maritime-events).',
            'slug.unique' => 'Slug sudah dipakai kategori lain.',
            'description.required' => 'Deskripsi wajib diisi.',
            'description.max' => 'Deskripsi maksimal 500 karakter.',
        ]);

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
        ];

        try {
            if ($editing) {
                $editing->update($data);
                $action = 'updated';
            } else {
                $editing = Category::create($data);
                $action = 'created';
            }
        } catch (UniqueConstraintViolationException) {
            // Cadangan kalau dua admin menyimpan slug yang sama di waktu bersamaan.
            $this->addError('slug', 'Slug sudah dipakai kategori lain.');

            return;
        }

        Log::info("admin.category.{$action}", [
            'admin_id' => auth()->id(),
            'category_id' => $editing->id,
            'slug' => $editing->slug,
            'ip' => request()->ip(),
        ]);

        $this->dispatch('notify', message: $action === 'created' ? 'Kategori ditambahkan.' : 'Kategori diperbarui.', type: 'success');
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        if (! $this->allowMutation()) {
            return;
        }

        $category = Category::withCount('articles')->findOrFail($id);

        // FK articles.category_id memakai cascadeOnDelete: tanpa blokir ini,
        // menghapus kategori ikut menghapus SEMUA artikelnya.
        if ($category->articles_count > 0) {
            $this->dispatch('notify', message: "Masih ada {$category->articles_count} artikel di kategori ini. Pindahkan atau hapus dulu.", type: 'error');

            return;
        }

        $category->delete();

        Log::warning('admin.category.deleted', [
            'admin_id' => auth()->id(),
            'category_id' => $id,
            'slug' => $category->slug,
            'ip' => request()->ip(),
        ]);

        if ($this->editingId === $id) {
            $this->resetForm();
        }

        $this->dispatch('notify', message: 'Kategori dihapus.', type: 'success');
    }

    private function resetForm(): void
    {
        $this->reset('showForm', 'editingId', 'name', 'slug', 'description', 'slugTouched');
        $this->resetValidation();
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->is_admin, 403);
    }

    // Otorisasi + pembatasan laju untuk aksi yang mengubah data (30 aksi/menit/admin).
    private function allowMutation(): bool
    {
        $this->authorizeAdmin();

        $key = 'admin-category-mutate|' . auth()->id();

        if (RateLimiter::tooManyAttempts($key, 30)) {
            $this->dispatch('notify', message: 'Terlalu banyak aksi. Coba lagi sebentar lagi.', type: 'error');

            return false;
        }

        RateLimiter::hit($key, 60);

        return true;
    }

    public function render()
    {
        return $this->view([]);
    }
};
?>

<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <input
            type="search"
            wire:model.live.debounce.400ms="search"
            placeholder="Cari nama atau slug..."
            aria-label="Cari kategori"
            class="w-full sm:max-w-xs px-4 py-2.5 border border-slate-200 rounded-lg bg-white focus:outline-none focus:ring focus:ring-[#d7a72b]/40 focus:border-[#d7a72b]"
        >
        <button type="button" wire:click="create" class="cursor-pointer rounded-lg bg-[#0a3768] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#06254a] transition">
            + Tambah Kategori
        </button>
    </div>

    @if($showForm)
        <form wire:submit="save" wire:keydown.escape="cancel" class="rounded-2xl border border-slate-200 bg-white p-6 space-y-5" novalidate>
            <h2 class="font-serif text-xl font-bold text-[#10243a]">{{ $editingId ? 'Edit Kategori' : 'Kategori Baru' }}</h2>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="cat-name" class="block text-sm font-semibold text-[#33465c] mb-1">Nama</label>
                    <input id="cat-name" type="text" wire:model.live.debounce.300ms="name" maxlength="100" autofocus
                        class="w-full px-4 py-2.5 border rounded-lg focus:outline-none focus:ring focus:ring-[#d7a72b]/40 focus:border-[#d7a72b] @error('name') border-red-400 @else border-slate-200 @enderror">
                    @error('name') <p role="alert" class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="cat-slug" class="block text-sm font-semibold text-[#33465c] mb-1">Slug (bagian URL)</label>
                    <input id="cat-slug" type="text" wire:model="slug" maxlength="100"
                        class="w-full px-4 py-2.5 border rounded-lg focus:outline-none focus:ring focus:ring-[#d7a72b]/40 focus:border-[#d7a72b] @error('slug') border-red-400 @else border-slate-200 @enderror">
                    @error('slug') <p role="alert" class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="cat-desc" class="block text-sm font-semibold text-[#33465c] mb-1">Deskripsi</label>
                <textarea id="cat-desc" wire:model="description" rows="3" maxlength="500"
                    class="w-full px-4 py-2.5 border rounded-lg focus:outline-none focus:ring focus:ring-[#d7a72b]/40 focus:border-[#d7a72b] @error('description') border-red-400 @else border-slate-200 @enderror"></textarea>
                @error('description') <p role="alert" class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" wire:loading.attr="disabled" wire:target="save"
                    class="rounded-lg bg-[#d7a72b] px-5 py-2.5 text-sm font-extrabold text-[#10243a] hover:brightness-95 transition disabled:opacity-60">
                    <span wire:loading.remove wire:target="save">Simpan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
                <button type="button" wire:click="cancel" class="px-4 py-2.5 text-sm font-semibold text-slate-600 hover:text-[#1474bd]">Batal</button>
            </div>
        </form>
    @endif

    <div wire:loading.class="opacity-60" wire:target="search,gotoPage,nextPage,previousPage" class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                <tr>
                    <th scope="col" class="px-5 py-3">Kategori</th>
                    <th scope="col" class="px-5 py-3">Slug</th>
                    <th scope="col" class="px-5 py-3 text-right">Artikel</th>
                    <th scope="col" class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($this->categories as $category)
                    <tr wire:key="category-{{ $category->id }}">
                        <td class="px-5 py-4">
                            <div class="font-semibold text-[#10243a]">
                                {{ $category->name }}
                            </div>
                            <div class="mt-0.5 max-w-md truncate text-slate-500">{{ $category->description }}</div>
                        </td>
                        <td class="px-5 py-4 font-mono text-xs text-slate-600">{{ $category->slug }}</td>
                        <td class="px-5 py-4 text-right tabular-nums">{{ $category->articles_count }}</td>
                        <td class="px-5 py-4 text-right whitespace-nowrap">
                            <button type="button" wire:click="edit({{ $category->id }})" class="font-semibold text-[#1474bd] hover:underline">Edit</button>
                            <button type="button"
                                wire:click="delete({{ $category->id }})"
                                wire:confirm="Hapus kategori '{{ $category->name }}'? Tindakan ini tidak bisa dibatalkan."
                                class="ml-4 font-semibold text-red-600 hover:underline">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center text-slate-400">Belum ada kategori yang cocok.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $this->categories->links() }}</div>
</div>