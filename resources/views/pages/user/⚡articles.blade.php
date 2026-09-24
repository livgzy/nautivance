<?php

use App\Models\Article;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;


new class extends Component
{
    use WithPagination;

    public Category $category;

    #[Computed]
    public function articles()
    {
        return Article::query()
            ->where('category_id', $this->category->id)
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(1);
    }

    public function render()
    {
        return $this->view([])->title($this->category->name . ' Archives - Nautivance');
    }
};
?>
<div>
    <section class="py-16 md:py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 max-w-2xl">
                <div class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#f1c84a]">Kategori</div>
                <h2 class="mt-2 mb-3 font-serif text-3xl font-bold text-[#10243a] md:text-4xl">{{ $category->name }}</h2>
                @if($category->description)
                    <p class="text-slate-500">{{ $category->description }}</p>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3">
                {{-- List artikel --}}
                <div class="lg:col-span-2">
                    @if($this->articles->isNotEmpty())
                        <div class="space-y-5">
                            @foreach($this->articles as $article)
                                <a href="/{{ $article->slug }}" wire:navigate class="flex gap-5 rounded-xl bg-slate-50 p-4 transition hover:shadow-md">
                                    <div class="h-32 w-44 flex-shrink-0 overflow-hidden rounded-lg">
                                        @if($article->thumbnail)
                                            <img src="{{ $article->thumbnail }}" alt="{{ $article->title }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full bg-gradient-to-br from-[#0a3768] to-[#1474bd]"></div>
                                        @endif
                                    </div>

                                    <div class="flex-1 py-1">
                                        <h3 class="mb-2 text-lg font-bold leading-snug text-[#10243a]">{{ $article->title }}</h3>
                                        <p class="mb-3 text-sm text-slate-700">{{ $article->excerpt }}</p>
                                        <span class="text-sm font-semibold text-[#10243a]">Read More &rarr;</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <div class="mt-10">
                            {{ $this->articles->links() }}
                        </div>
                    @else
                        <p class="py-12 text-center text-slate-400">Belum ada artikel di kategori ini.</p>
                    @endif
                </div>
                {{-- Sidebar --}}
                <aside class="hidden lg:block"></aside>
            </div>
        </div>
    </section>
</div>
