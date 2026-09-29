<?php

use App\Models\Article;
use App\Models\Category;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $query = '';

    #[Url(as: 'kategori')]
    public string $categorySlug = '';

    public function search(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset('query', 'categorySlug');
        $this->resetPage();
    }

    public function with(): array
    {
        $articles = Article::query()
            ->with('category')
            ->where('status', 'published')
            ->when($this->query, function ($q) {
                $q->where(function ($q) {
                    $q->where('title', 'like', "%{$this->query}%")
                        ->orWhere('excerpt', 'like', "%{$this->query}%");
                });
            })
            ->when($this->categorySlug, function ($q) {
                $q->whereHas('category', fn ($q) => $q->where('slug', $this->categorySlug));
            })
            ->latest('published_at')
            ->paginate(12);

        return [
            'articles' => $articles,
            'categories' => Category::orderBy('name')->get(),
        ];
    }

    public function render()
    {
        return $this->view([])->title('Search Article - Nautivance');
    }
};
?>

<div>
    {{-- Header --}}
    <div class="bg-slate-100 py-8 sm:py-10 mb-8 sm:mb-10 text-center px-4">
        <h1 class="font-serif text-3xl sm:text-4xl text-[#10243a] mb-2">
            Search Article
        </h1>
        <p class="text-sm sm:text-base text-slate-500">
            Temukan panduan, lowongan, dan info karier maritim di Nautivance
        </p>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-12 sm:pb-16">

        {{-- Search & Filter --}}
        <form
            wire:submit="search"
            class="grid grid-cols-1 sm:grid-cols-[1fr_auto] lg:flex gap-3 mb-8"
        >
            <input
                type="text"
                wire:model="query"
                placeholder="Cari judul, kata kunci..."
                class="w-full lg:flex-1 px-4 py-3 border border-slate-200 rounded-lg focus:outline-none focus:ring focus:ring-[#d7a72b]/40 focus:border-[#d7a72b]"
            >

            <select
                wire:model="categorySlug"
                class="w-full lg:w-auto px-4 py-3 border border-slate-200 rounded-lg focus:outline-none focus:ring focus:ring-[#d7a72b]/40 focus:border-[#d7a72b] bg-white"
            >
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->slug }}">
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <button
                type="submit"
                class="w-full sm:w-auto px-6 py-3 bg-[#0a3768] text-white font-semibold rounded-lg hover:bg-[#06254a] transition flex items-center justify-center"
            >
                <x-lucide-search class="size-5"/>
            </button>

            @if($query || $categorySlug)
                <button
                    type="button"
                    wire:click="resetFilters"
                    class="w-full sm:w-auto px-4 py-3 text-sm font-semibold text-[#33465c] hover:text-[#1474bd] whitespace-nowrap"
                >
                    Reset Filter
                </button>
            @endif
        </form>

        {{-- Skeleton --}}
        <div wire:loading class="w-full space-y-5">

            <div class="h-4 sm:h-5 w-40 sm:w-48 bg-slate-200 rounded animate-pulse mb-6"></div>

            @for ($i = 0; $i < 3; $i++)
                <div class="flex flex-col sm:flex-row gap-4 sm:gap-5 bg-slate-50 rounded-xl p-3 sm:p-4 w-full animate-pulse">

                    {{-- Thumbnail --}}
                    <div class="w-full h-48 sm:w-44 sm:h-32 flex-shrink-0 rounded-lg bg-slate-200"></div>

                    {{-- Content --}}
                    <div class="flex-1 py-1 min-w-0">

                        {{-- Category --}}
                        <div class="h-3 w-20 bg-slate-200 rounded mb-2"></div>

                        {{-- Title --}}
                        <div class="h-5 sm:h-6 w-3/4 bg-slate-200 rounded mb-3"></div>

                        {{-- Excerpt --}}
                        <div class="space-y-2 mb-4">
                            <div class="h-3.5 w-full bg-slate-200 rounded"></div>
                            <div class="h-3.5 w-4/5 bg-slate-200 rounded"></div>
                        </div>

                        {{-- Read More --}}
                        <div class="h-3.5 sm:h-4 w-24 bg-slate-200 rounded"></div>

                    </div>
                </div>
            @endfor
        </div>

        {{-- Results --}}
        <div wire:loading.remove>

            {{-- Result Counter --}}
            <p class="text-sm text-slate-500 mb-6">
                @if($query || $categorySlug)
                    Menampilkan {{ $articles->total() }} hasil

                    @if($query)
                        untuk
                        "<span class="font-semibold text-[#10243a]">{{ $query }}</span>"
                    @endif

                    @if($categorySlug)
                        di kategori
                        <span class="font-semibold text-[#10243a]">
                            {{ $categories->firstWhere('slug', $categorySlug)?->name }}
                        </span>
                    @endif
                @else
                    Menampilkan {{ $articles->total() }} artikel terbaru
                @endif
            </p>

            @if($articles->isNotEmpty())

                {{-- Articles --}}
                <div class="space-y-5">

                    @foreach($articles as $article)

                        <a
                            href=""
                            wire:navigate
                            class="group flex flex-col sm:flex-row gap-4 sm:gap-5 bg-slate-50 rounded-xl p-3 sm:p-4 hover:shadow-md transition"
                        >

                            {{-- Thumbnail --}}
                            <div class="w-full h-48 sm:w-44 sm:h-32 flex-shrink-0 rounded-lg overflow-hidden">

                                @if($article->thumbnail)

                                    <img
                                        src="{{ $article->thumbnail }}"
                                        alt="{{ $article->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                    >

                                @else

                                    <div class="w-full h-full bg-gradient-to-br from-[#0a3768] to-[#1474bd]"></div>

                                @endif

                            </div>

                            {{-- Content --}}
                            <div class="flex-1 py-1 min-w-0">

                                <small class="uppercase tracking-wide text-xs font-extrabold text-amber-500">
                                    {{ $article->category->name }}
                                </small>

                                <h3 class="font-bold text-[#10243a] text-base sm:text-lg mt-1 mb-2 leading-snug line-clamp-2">
                                    {{ $article->title }}
                                </h3>

                                <p class="text-slate-500 text-sm mb-3 line-clamp-2">
                                    {{ $article->excerpt }}
                                </p>

                                <span class="text-[#10243a] font-semibold text-sm">
                                    Read More &rarr;
                                </span>

                            </div>

                        </a>

                    @endforeach

                </div>

                {{-- Pagination --}}
                <div class="mt-8 sm:mt-10 overflow-x-auto">
                    {{ $articles->links(data: ['scrollTo' => false]) }}
                </div>

            @else

                {{-- Empty State --}}
                <div class="text-center py-12 sm:py-16 px-4">
                    <p class="text-slate-400 mb-3">
                        Tidak ada artikel yang cocok dengan pencarian kamu.
                    </p>

                    @if($query || $categorySlug)
                        <button
                            type="button"
                            wire:click="resetFilters"
                            class="text-[#1474bd] font-semibold hover:underline"
                        >
                            Reset pencarian
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
