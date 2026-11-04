<?php

use App\Models\Category;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::admin')] #[Title('Dashboard Admin — Tambah Kategori')] class extends Component
{
    public string $name = '';
    public string $slug = '';
    public string $description = '';
    public bool $slugTouched = false;

    // Dicek di setiap request Livewire, bukan hanya saat halaman dibuka.
    public function boot(): void
    {
        abort_unless(auth()->check() && auth()->user()->is_admin, 403);
    }

    // Slug otomatis dari nama, sampai admin mengetik slug sendiri.
    public function updatedName(): void
    {
        if (! $this->slugTouched) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function updatedSlug(): void
    {
        $this->slugTouched = true;
    }

    public function save(): void
    {
        // Rate limit: 30 aksi tulis/menit per admin.
        $key = 'admin-category-mutate|' . auth()->id();

        if (RateLimiter::tooManyAttempts($key, 30)) {
            $this->addError('name', 'Terlalu banyak aksi. Coba lagi sebentar lagi.');

            return;
        }

        RateLimiter::hit($key, 60);

        $this->name = trim($this->name);
        $this->slug = Str::lower(trim($this->slug));
        $this->description = trim($this->description);

        $this->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')],
            'slug' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('categories', 'slug')],
            'description' => ['required', 'string', 'max:500'],
        ], [
            'name.unique' => 'Nama kategori sudah dipakai.',
            'slug.unique' => 'Slug sudah dipakai kategori lain.',
            'slug.regex' => 'Slug hanya boleh huruf kecil, angka, dan tanda hubung (contoh: seafarer-guide).',
        ]);

        try {
            $category = Category::create([
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
            ]);
        } catch (UniqueConstraintViolationException) {
            // Cadangan kalau dua admin menyimpan slug yang sama bersamaan.
            $this->addError('slug', 'Slug sudah dipakai kategori lain.');

            return;
        }

        Log::info('admin.category.created', [
            'admin_id' => auth()->id(),
            'category_id' => $category->id,
            'slug' => $category->slug,
            'ip' => request()->ip(),
        ]);

        session()->flash('status', "Kategori '{$category->name}' ditambahkan.");

        $this->redirectRoute('admin.categories.index', navigate: true);
    }
};
?>

<div class="max-w-2xl space-y-6">
    <div>
        <a href="{{ route('admin.categories.index') }}" wire:navigate class="text-sm font-semibold text-[#1474bd] hover:underline">&larr; Kembali ke daftar kategori</a>
        <h1 class="mt-2 font-serif text-2xl font-bold text-[#10243a]">Tambah Kategori</h1>
    </div>

    <form wire:submit="save" class="rounded-2xl border border-slate-200 bg-white p-6 space-y-5" novalidate>
        <div>
            <label for="cat-name" class="block text-sm font-semibold text-[#33465c] mb-1">Nama</label>
            <input id="cat-name" type="text" wire:model.live.debounce.300ms="name" maxlength="100" autofocus autocomplete="off"
                class="w-full px-4 py-2.5 border rounded-lg focus:outline-none focus:ring focus:ring-[#d7a72b]/40 focus:border-[#d7a72b] @error('name') border-red-400 @else border-slate-200 @enderror">
            @error('name') <p role="alert" class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="cat-slug" class="block text-sm font-semibold text-[#33465c] mb-1">Slug (bagian URL)</label>
            <input id="cat-slug" type="text" wire:model.live.debounce.300ms="slug" maxlength="100" autocomplete="off"
                class="w-full px-4 py-2.5 border rounded-lg font-mono text-sm focus:outline-none focus:ring focus:ring-[#d7a72b]/40 focus:border-[#d7a72b] @error('slug') border-red-400 @else border-slate-200 @enderror">
            <p class="mt-1 text-xs text-slate-500">Alamat: nautivance.com/<span class="font-mono">{{ $slug !== '' ? $slug : 'slug-kategori' }}</span></p>
            @error('slug') <p role="alert" class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="cat-desc" class="block text-sm font-semibold text-[#33465c] mb-1">Deskripsi</label>
            <textarea id="cat-desc" wire:model="description" rows="3" maxlength="500"
                class="w-full px-4 py-2.5 border rounded-lg focus:outline-none focus:ring focus:ring-[#d7a72b]/40 focus:border-[#d7a72b] @error('description') border-red-400 @else border-slate-200 @enderror"></textarea>
            @error('description') <p role="alert" class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" wire:loading.attr="disabled" wire:target="save"
                class="rounded-lg bg-[#0a3768] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#06254a] transition disabled:opacity-60">
                <span wire:loading.remove wire:target="save">Simpan Kategori</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
            <a href="{{ route('admin.categories.index') }}" wire:navigate class="text-sm font-semibold text-slate-500 hover:underline">Batal</a>
        </div>
    </form>
</div>