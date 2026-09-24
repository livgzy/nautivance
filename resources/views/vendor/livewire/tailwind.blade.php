@php
    if (!isset($scrollTo)) {
        $scrollTo = 'body';
    }

    $scrollIntoViewJsSnippet = ($scrollTo !== false)
        ? <<<JS
            (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
        JS
        : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
            <div class="flex flex-1 justify-between sm:hidden">
                <span>
                    @if ($paginator->onFirstPage())
                        <span class="relative inline-flex items-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium leading-5 text-slate-400 cursor-default">
                            {!! __('pagination.previous') !!}
                        </span>
                    @else
                        <button
                            type="button"
                            wire:click="previousPage('{{ $paginator->getPageName() }}')"
                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                            wire:loading.attr="disabled"
                            class="relative inline-flex items-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium leading-5 text-navy transition duration-150 hover:border-ocean hover:text-ocean focus:outline-none focus:ring-2 focus:ring-ocean/20 active:bg-mist"
                        >
                            {!! __('pagination.previous') !!}
                        </button>
                    @endif
                </span>

                <span>
                    @if ($paginator->hasMorePages())
                        <button
                            type="button"
                            wire:click="nextPage('{{ $paginator->getPageName() }}')"
                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                            wire:loading.attr="disabled"
                            class="relative ml-3 inline-flex items-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium leading-5 text-navy transition duration-150 hover:border-ocean hover:text-ocean focus:outline-none focus:ring-2 focus:ring-ocean/20 active:bg-mist"
                        >
                            {!! __('pagination.next') !!}
                        </button>
                    @else
                        <span class="relative ml-3 inline-flex items-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium leading-5 text-slate-400 cursor-default">
                            {!! __('pagination.next') !!}
                        </span>
                    @endif
                </span>
            </div>

            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm leading-5 text-muted">
                        <span>Showing</span>
                        <span class="font-semibold text-navy">{{ $paginator->firstItem() }}</span>
                        <span>to</span>
                        <span class="font-semibold text-navy">{{ $paginator->lastItem() }}</span>
                        <span>of</span>
                        <span class="font-semibold text-navy">{{ $paginator->total() }}</span>
                        <span>results</span>
                    </p>
                </div>

                <div>
                    <span class="relative z-0 inline-flex rounded-lg shadow-sm">
                        <span>
                            @if ($paginator->onFirstPage())
                                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                                    <span class="relative inline-flex items-center rounded-l-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium leading-5 text-slate-300 cursor-default" aria-hidden="true">
                                        <x-lucide-chevron-left class="size-5" />
                                    </span>
                                </span>
                            @else
                                <button
                                    type="button"
                                    wire:click="previousPage('{{ $paginator->getPageName() }}')"
                                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                    class="relative inline-flex items-center rounded-l-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium leading-5 text-navy transition duration-150 hover:bg-mist hover:text-ocean focus:z-10 focus:outline-none focus:ring-2 focus:ring-ocean/20"
                                    aria-label="{{ __('pagination.previous') }}"
                                >
                                    <x-lucide-chevron-left class="size-5" />
                                </button>
                            @endif
                        </span>

                        @foreach ($elements as $element)
                            @if (is_string($element))
                                <span aria-disabled="true">
                                    <span class="relative -ml-px inline-flex items-center border border-slate-200 bg-white px-4 py-2 text-sm font-medium leading-5 text-muted cursor-default">
                                        {{ $element }}
                                    </span>
                                </span>
                            @endif

                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                                        @if ($page == $paginator->currentPage())
                                            <span aria-current="page">
                                                <span class="relative -ml-px inline-flex items-center border border-navy bg-navy px-4 py-2 text-sm font-semibold leading-5 text-white">
                                                    {{ $page }}
                                                </span>
                                            </span>
                                        @else
                                            <button
                                                type="button"
                                                wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                                class="relative -ml-px inline-flex items-center border border-slate-200 bg-white px-4 py-2 text-sm font-medium leading-5 text-navy transition duration-150 hover:bg-mist hover:text-ocean focus:z-10 focus:outline-none focus:ring-2 focus:ring-ocean/20 active:bg-mist"
                                                aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                                            >
                                                {{ $page }}
                                            </button>
                                        @endif
                                    </span>
                                @endforeach
                            @endif
                        @endforeach

                        <span>
                            @if ($paginator->hasMorePages())
                                <button
                                    type="button"
                                    wire:click="nextPage('{{ $paginator->getPageName() }}')"
                                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                    class="relative -ml-px inline-flex items-center rounded-r-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium leading-5 text-navy transition duration-150 hover:bg-mist hover:text-ocean focus:z-10 focus:outline-none focus:ring-2 focus:ring-ocean/20"
                                    aria-label="{{ __('pagination.next') }}"
                                >
                                    <x-lucide-chevron-right class="size-5" />
                                </button>
                            @else
                                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                                    <span class="relative -ml-px inline-flex items-center rounded-r-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium leading-5 text-slate-300 cursor-default" aria-hidden="true">
                                        <x-lucide-chevron-right class="size-5" />
                                    </span>
                                </span>
                            @endif
                        </span>
                    </span>
                </div>
            </div>
        </nav>
    @endif
</div>
