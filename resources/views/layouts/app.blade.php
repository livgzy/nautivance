<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('nautivance-favicon.png') }}">
        <style>[x-cloak] { display: none !important; }</style>
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body>
        {{-- HEADER --}}
        <header
            x-data="{ open: false }"
            @keydown.escape.window="open = false"
            @click.outside="open = false"
            class="sticky top-0 z-50 border-b border-slate-200 bg-white/95"
        >
            <div class="mx-auto flex h-[68px] w-[min(1120px,92%)] items-center justify-between gap-3 sm:h-[78px] sm:gap-6">
                <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-2 font-extrabold tracking-wide text-navy sm:gap-3">
                    <img src="{{ asset('assets/images/nautivance-logo.png') }}" alt="Nautivance logo" class="size-11 shrink-0 object-contain sm:size-14 lg:size-[68px]">
                    <span class="truncate font-serif text-lg sm:text-[22px]">NAUTIVANCE</span>
                </a>

                {{-- Navigasi desktop (tampil mulai lg) --}}
                <nav class="hidden gap-[25px] text-sm font-semibold text-slate-600 lg:flex">
                    <a href="#career" class="hover:text-ocean">Career</a>
                    <a href="#guide" class="hover:text-ocean">Seafarer Guide</a>
                    <a href="#education" class="hover:text-ocean">Education</a>
                    <a href="#jobs" class="hover:text-ocean">Jobs</a>
                    <a href="#resources" class="hover:text-ocean">Resources</a>
                </nav>

                <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                    {{-- Tombol CTA (tampil mulai sm) --}}
                    <a href="#resources" class="hidden rounded-lg bg-gold px-5 py-[13px] font-extrabold text-ink transition hover:brightness-95 sm:inline-block">
                        Free Resources
                    </a>

                    {{-- Tombol hamburger (hilang di lg) --}}
                    <button
                        type="button"
                        @click="open = !open"
                        :aria-expanded="open.toString()"
                        aria-controls="mobile-menu"
                        aria-label="Buka menu navigasi"
                        class="inline-flex size-11 items-center justify-center rounded-lg text-navy transition hover:bg-slate-100 lg:hidden"
                    >
                        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="open" x-cloak xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Menu HP & tablet (hilang di lg) --}}
            <div
                id="mobile-menu"
                x-show="open"
                x-cloak
                class="absolute inset-x-0 top-full max-h-[80dvh] overflow-y-auto border-b border-slate-200 bg-white shadow-lg lg:hidden"
            >
                <nav class="mx-auto flex w-[min(1120px,92%)] flex-col gap-1 py-3">
                    <a href="#career" @click="open = false" class="block rounded-lg px-3 py-3 text-base font-semibold text-slate-700 hover:bg-slate-50 hover:text-ocean">Career</a>
                    <a href="#guide" @click="open = false" class="block rounded-lg px-3 py-3 text-base font-semibold text-slate-700 hover:bg-slate-50 hover:text-ocean">Seafarer Guide</a>
                    <a href="#education" @click="open = false" class="block rounded-lg px-3 py-3 text-base font-semibold text-slate-700 hover:bg-slate-50 hover:text-ocean">Education</a>
                    <a href="#jobs" @click="open = false" class="block rounded-lg px-3 py-3 text-base font-semibold text-slate-700 hover:bg-slate-50 hover:text-ocean">Jobs</a>
                    <a href="#resources" @click="open = false" class="block rounded-lg px-3 py-3 text-base font-semibold text-slate-700 hover:bg-slate-50 hover:text-ocean">Resources</a>

                    {{-- CTA di dalam menu hanya untuk HP (di bawah sm) --}}
                    <a href="#resources" @click="open = false" class="mt-2 block rounded-lg bg-gold px-5 py-[13px] text-center font-extrabold text-ink transition hover:brightness-95 sm:hidden">
                        Free Resources
                    </a>
                </nav>
            </div>
        </header>

        {{ $slot }}

        {{-- FOOTER --}}
        <footer class="bg-[#061b33] py-10 text-sm text-[#c6d4e2] sm:py-[45px] sm:text-base">
            <div class="mx-auto flex w-[min(1120px,92%)] flex-col gap-4 text-center md:flex-row md:flex-wrap md:justify-between md:text-left">
                <div><strong class="text-white">NAUTIVANCE</strong><br>Advance Your Maritime Career.</div>
                <div>Career • Education • Opportunities • Resources</div>
                <div>© {{ date('Y') }} Nautivance</div>
            </div>
        </footer>
        @livewireScripts
    </body>
</html>