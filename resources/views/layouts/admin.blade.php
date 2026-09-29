<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow, noarchive">
        <meta name="referrer" content="same-origin">

        <title>{{ isset($title) ? $title . ' · Admin' : 'Admin' }} · {{ config('app.name') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('nautivance-favicon.png') }}">

        <style>
            [x-cloak] { display: none !important; }
            #nprogress .bar { background: #d7a72b !important; }
        </style>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-slate-100 text-[#10243a] antialiased">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen">
            {{-- Overlay --}}
            <div
                x-show="sidebarOpen"
                x-cloak
                @click="sidebarOpen = false"
                class="fixed inset-0 z-30 bg-black/50 lg:hidden"
            ></div>

            {{-- Sidebar --}}
            <aside
                class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-[#06254a] text-white transition-transform duration-200 lg:translate-x-0"
                :class="{ '-translate-x-full': !sidebarOpen }"
                aria-label="Navigasi admin"
            >
                <div class="flex h-16 items-center border-b border-white/10 px-6">
                    <span class="font-serif text-xl font-bold tracking-wide">NAUTIVANCE</span>
                </div>
                <nav class="flex-1 py-4">
                    <a
                        href="{{ '#' }}"
                        wire:navigate
                        @click="sidebarOpen = false"
                        @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif
                        class="flex border-l-4 px-6 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'border-[#d7a72b] bg-white/10 text-white' : 'border-transparent text-slate-300 hover:bg-white/5 hover:text-white' }}"
                    >
                        Dashboard
                    </a>
                    <a
                        href="{{ '#' }}"
                        wire:navigate
                        @click="sidebarOpen = false"
                        @if(request()->routeIs('admin.articles.*')) aria-current="page" @endif
                        class="flex border-l-4 px-6 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.articles.*') ? 'border-[#d7a72b] bg-white/10 text-white' : 'border-transparent text-slate-300 hover:bg-white/5 hover:text-white' }}"
                    >
                        Artikel
                    </a>
                    <a
                        href="{{ '#' }}"
                        wire:navigate
                        @click="sidebarOpen = false"
                        @if(request()->routeIs('admin.categories.*')) aria-current="page" @endif
                        class="flex border-l-4 px-6 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.categories.*') ? 'border-[#d7a72b] bg-white/10 text-white' : 'border-transparent text-slate-300 hover:bg-white/5 hover:text-white' }}"
                    >
                        Kategori
                    </a>
                    <a
                        href="{{ '#' }}"
                        wire:navigate
                        @click="sidebarOpen = false"
                        @if(request()->routeIs('admin.subscribers.*')) aria-current="page" @endif
                        class="flex border-l-4 px-6 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.subscribers.*') ? 'border-[#d7a72b] bg-white/10 text-white' : 'border-transparent text-slate-300 hover:bg-white/5 hover:text-white' }}"
                    >
                        Subscriber
                    </a>
                </nav>
                <div class="border-t border-white/10 p-4 text-sm">
                    <a
                        href="{{ route('home') }}"
                        target="_blank"
                        rel="noopener"
                        class="flex px-2 py-2 text-slate-300 hover:text-white"
                    >
                        <span>Lihat Situs </span><x-lucide-move-up-right class="size-3"/>
                    </a>
                    <div class="mt-2 px-2">
                        <div class="truncate font-semibold">{{ auth()->user()->name ?? 'Admin' }}</div>
                        <div class="truncate text-xs text-slate-400">{{ auth()->user()->email ?? '' }}</div>
                    </div>
                </div>
            </aside>
            {{-- Content --}}
            <div class="lg:pl-64">
                <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-slate-200 bg-white px-4 sm:px-6">
                    <button
                        type="button"
                        @click="sidebarOpen = true"
                        class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden"
                        aria-label="Buka menu"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <h1 class="font-serif text-xl font-bold">
                        {{ $title ?? 'Dashboard' }}
                    </h1>
                    {{-- Profile --}}
                    <div x-data="{ open: false }" class="relative ml-auto">
                        <button
                            @click="open = !open"
                            @click.outside="open = false"
                            @keydown.escape.window="open = false"
                            :aria-expanded="open"
                            aria-haspopup="true"
                            class="flex items-center gap-2 rounded-lg p-1.5 hover:bg-slate-100"
                        >
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-[#0a3768] text-sm font-bold text-white">
                                {{ Str::upper(Str::substr(auth()->user()->name ?? 'Admin', 0, 1)) }}
                            </div>

                            <span class="hidden max-w-32 truncate text-sm font-semibold sm:block">
                                {{ auth()->user()->name ?? 'Admin' }}
                            </span>

                            <svg class="hidden h-4 w-4 text-slate-500 transition sm:block" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
                            </svg>
                        </button>
                        <div
                            x-show="open"
                            x-cloak
                            x-transition
                            class="absolute right-0 top-full z-50 mt-2 w-64 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg"
                        >
                            <div class="border-b border-slate-100 px-4 py-3">
                                <div class="font-semibold">{{ auth()->user()->name ?? 'Admin' }}</div>
                                <div class="truncate text-xs text-slate-500">{{ auth()->user()->email ?? '' }}</div>
                            </div>
                            <div class="p-2">
                                <a
                                    href="{{ route('home') }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="flex rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100"
                                >
                                    <span>Lihat Situs </span><x-lucide-move-up-right class="size-3"/>
                                </a>
                                <form method="POST" action="{{ '#' }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="w-full rounded-lg px-3 py-2.5 text-left text-sm font-semibold text-red-600 hover:bg-red-50"
                                    >
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>
                <main class="p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
        {{-- Notification --}}
        <div
            x-data="{
                show: {{ session()->hasAny(['success', 'error']) ? 'true' : 'false' }},
                message: @js(session('error') ?? session('success')),
                type: @js(session()->has('error') ? 'error' : 'success'),
                timer: null,
                open(message, type = 'success') {
                    this.message = message;
                    this.type = type;
                    this.show = true;
                    clearTimeout(this.timer);
                    this.timer = setTimeout(() => this.show = false, 4000);
                }
            }"
            x-init="if (show) timer = setTimeout(() => show = false, 4000)"
            x-on:notify.window="open($event.detail.message, $event.detail.type ?? 'success')"
            x-show="show"
            x-cloak
            x-transition
            role="status"
            aria-live="polite"
            class="fixed right-4 top-4 z-50 max-w-sm rounded-lg bg-[#0a3768] px-4 py-3 text-sm font-semibold text-white shadow-lg"
            :class="{ 'bg-red-600': type === 'error' }"
        >
            <span x-text="message"></span>
        </div>
        @livewireScripts
    </body>
</html>
