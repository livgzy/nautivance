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
            ->paginate(10);
    }

    public function render()
    {
        return $this->view([])->title($this->category->name . ' Archives - Nautivance');
    }
};
?>

<div>
    <section class="py-10 sm:py-12 md:py-15">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-8 sm:mb-10 max-w-2xl">
                <div class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#f1c84a]">
                    Kategori
                </div>

                <h2 class="mt-2 mb-3 font-serif text-3xl font-bold leading-tight text-[#10243a] sm:text-4xl">
                    {{ $category->name }}
                </h2>

                @if($category->description)
                    <p class="text-sm leading-relaxed text-slate-500 sm:text-base">
                        {{ $category->description }}
                    </p>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-10">

                {{-- List artikel --}}
                <div class="lg:col-span-2">
                    @if($this->articles->isNotEmpty())
                        <div class="space-y-4 sm:space-y-5">
                            @foreach($this->articles as $article)
                                <a
                                    href="/article/{{ $article->slug }}"
                                    wire:navigate
                                    class="group flex flex-col gap-4 rounded-xl bg-slate-50 p-3.5 transition duration-300 hover:shadow-md sm:flex-row sm:gap-5 sm:p-4"
                                >
                                    {{-- Thumbnail --}}
                                    <div class="h-48 w-full flex-shrink-0 overflow-hidden rounded-lg sm:h-32 sm:w-44">
                                        @if($article->thumbnail)
                                            <img
                                                src="{{ $article->thumbnail }}"
                                                alt="{{ $article->title }}"
                                                class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                            >
                                        @else
                                            <div class="h-full w-full bg-gradient-to-br from-[#0a3768] to-[#1474bd]"></div>
                                        @endif
                                    </div>

                                    {{-- Content --}}
                                    <div class="min-w-0 flex-1 py-1">
                                        <h3 class="mb-2 line-clamp-2 text-base font-bold leading-snug text-[#10243a] sm:text-lg">
                                            {{ $article->title }}
                                        </h3>

                                        <p class="mb-3 line-clamp-2 text-sm leading-relaxed text-slate-700">
                                            {{ $article->excerpt }}
                                        </p>

                                        <span class="text-sm font-semibold text-[#10243a]">
                                            Read More &rarr;
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-8 overflow-x-auto sm:mt-10">
                            {{ $this->articles->links() }}
                        </div>
                    @else
                        <p class="py-12 text-center text-sm text-slate-400 sm:py-16">
                            Belum ada artikel di kategori ini.
                        </p>
                    @endif
                </div>
                {{-- Sidebar --}}
                <aside class="hidden lg:block"></aside>

            </div>
        </div>
    </section>
</div>