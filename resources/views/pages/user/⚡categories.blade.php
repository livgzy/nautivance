<?php

use App\Models\Category;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Kategori - Nautivance')] class extends Component
{
    // Hanya artikel berstatus published yang dihitung, jadi draft tidak bocor lewat angka.
    // Kategori baru buatan admin otomatis ikut tampil.
    #[Computed]
    public function categories()
    {
        return Category::query()
            ->withCount(['articles as published_count' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'description']);
    }
};
?>

<div>
    <section class="py-16 md:py-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mb-10">
                <div class="uppercase tracking-[0.18em] text-xs font-extrabold text-amber-500">Jelajahi</div>
                <h1 class="font-serif text-3xl md:text-4xl font-bold text-[#10243a] mt-2 mb-3">Semua Kategori</h1>
                <p class="text-slate-500">Pilih topik yang kamu butuhkan, mulai dari karier, panduan pelaut, pendidikan, sampai lowongan.</p>
            </div>

            @if($this->categories->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($this->categories as $category)
                        <a href="{{ '#' }}" wire:navigate wire:key="cat-{{ $category->id }}"
                            class="group block overflow-hidden rounded-2xl border border-slate-200 bg-white transition hover:-translate-y-1 hover:shadow-lg">
                            <div class="flex h-28 items-end justify-between bg-gradient-to-br from-[#0a3768] to-[#1474bd] p-5">
                                <h2 class="font-serif text-xl font-bold text-white">{{ $category->name }}</h2>
                                <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-extrabold text-white">
                                    {{ $category->published_count }} artikel
                                </span>
                            </div>
                            <div class="p-5">
                                <p class="text-sm text-slate-500">{{ $category->description }}</p>
                                <span class="mt-4 inline-block text-sm font-semibold text-[#1474bd] group-hover:underline">
                                    {{ $category->published_count > 0 ? 'Lihat artikel' : 'Segera hadir' }} &rarr;
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="py-12 text-center text-slate-400">Belum ada kategori.</p>
            @endif
        </div>
    </section>
</div>