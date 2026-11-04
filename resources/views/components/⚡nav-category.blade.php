<?php

use Livewire\Component;
use App\Models\ArticleCustom;

new class extends Component
{
    public bool $mobile = false;

    public function getCustomCategoriesProperty()
    {
        return ArticleCustom::query()
            ->whereHas('article', fn ($query) => $query->where('status', 'published'))
            ->orderBy('name')
            ->limit(6)
            ->get();
    }
};
?>

<div x-data="{ moreOpen: false }">
    <nav @class([
        'flex flex-col gap-1' => $mobile,
        'hidden lg:flex items-center gap-[25px]' => !$mobile,
        'text-sm font-semibold text-slate-600',
    ])>
        <a href="/category/career" @click="moreOpen = false" @class(['rounded-lg px-3 py-3 hover:text-ocean' => $mobile, 'hover:text-ocean' => !$mobile])>Career</a>
        <a href="/category/guide" @click="moreOpen = false" @class(['rounded-lg px-3 py-3 hover:text-ocean' => $mobile, 'hover:text-ocean' => !$mobile])>Seafarer Guide</a>
        <a href="/category/education" @click="moreOpen = false" @class(['rounded-lg px-3 py-3 hover:text-ocean' => $mobile, 'hover:text-ocean' => !$mobile])>Education</a>
        <a href="/category/jobs" @click="moreOpen = false" @class(['rounded-lg px-3 py-3 hover:text-ocean' => $mobile, 'hover:text-ocean' => !$mobile])>Jobs</a>
        <a href="/category/resources" @click="moreOpen = false" @class(['rounded-lg px-3 py-3 hover:text-ocean' => $mobile, 'hover:text-ocean' => !$mobile])>Resources</a>

        @if ($this->customCategories->isNotEmpty())
            <div class="relative" @click.outside="moreOpen = false">
                <button type="button" @click="moreOpen = !moreOpen" :aria-expanded="moreOpen" class="flex items-center gap-1 rounded-lg hover:text-ocean" @class(['w-full px-3 py-3 justify-between' => $mobile])>
                    More
                    <x-lucide-chevron-down class="size-5 transition-transform" x-bind:class="moreOpen && 'rotate-180'"/>
                </button>

                <div x-cloak x-show="moreOpen" x-transition @class([
                    'flex flex-col gap-1 pl-4' => $mobile,
                    'absolute right-0 top-full z-50 mt-2 min-w-48 rounded-lg border border-slate-200 bg-white py-2 shadow-lg' => !$mobile,
                ])>
                    @foreach ($this->customCategories as $category)
                        <a href="{{ url('/category/' . $category->slug) }}" @click="moreOpen = false" class="block rounded-lg px-4 py-2 hover:bg-slate-50 hover:text-ocean">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </nav>
</div>