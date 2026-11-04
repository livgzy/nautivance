<?php

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $q = '';

    // Daftar kategori dari DB (di-cache) sebagai jalan keluar dari halaman 404.
    // Di-cache sebagai array biasa (Laravel 13 tidak membaca objek dari cache).
    #[Computed]
    public function categories(): array
    {
        return Cache::remember('error404.categories.v2', 600, fn () => Category::orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'slug'])
            ->toArray());
    }

    public function search()
    {
        $q = trim(mb_substr($this->q, 0, 100));

        // Query kosong -> ke halaman pencarian tanpa parameter.
        return $this->redirect($q === '' ? route('search') : route('search', ['q' => $q]), navigate: true);
    }
};
?>

<div class="mt-8 space-y-8">
    @if($this->categories !== [])
        <div>
            <p class="mb-3 text-xs font-extrabold uppercase tracking-wide text-slate-500">Atau jelajahi kategori</p>
            <div class="flex flex-wrap justify-center gap-2">
                @foreach($this->categories as $category)
                    <a href="{{ route('articles', $category['slug']) }}" wire:navigate wire:key="e404-{{ $category['id'] }}"
                        class="rounded-full border border-slate-200 bg-white px-4 py-1.5 text-sm font-semibold text-[#0a3768] transition hover:border-[#d7a72b] hover:bg-[#d7a72b]/10">{{ $category['name'] }}</a>
                @endforeach
            </div>
        </div>
    @endif
</div>