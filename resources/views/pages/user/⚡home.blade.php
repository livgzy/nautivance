<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Nautivance — Advance Your Maritime Career')] class extends Component {
    public string $email = '';

    public bool $subscribed = false;

    public function subscribe(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
        ]);

        // TODO: simpan email ke database (mis. model Subscriber)

        $this->reset('email');
        $this->subscribed = true;
    }
};
?>

<div class="bg-mist leading-[1.6] text-ink">
    <main>
        {{-- HERO --}}
        <section class="flex min-h-[520px] items-center bg-cover bg-center text-white bg-[linear-gradient(115deg,rgba(4,27,54,.95),rgba(7,57,101,.86)),url('/images/nautivance-logo.png')] md:min-h-[560px] lg:min-h-[600px]">
            <div class="mx-auto w-[min(1120px,92%)] py-14 md:py-[75px]">
                <div class="max-w-[680px]">
                    <div class="text-[11px] font-extrabold uppercase tracking-[.14em] text-[#f1c84a] sm:text-xs sm:tracking-[.18em]">
                        Maritime Career • Education • Opportunities
                    </div>
                    <h1 class="my-4 font-serif text-[length:clamp(34px,7vw,70px)] font-bold leading-[1.05]">
                        Advance Your Maritime Career.
                    </h1>
                    <p class="max-w-[620px] text-base text-[#e8eef5] sm:text-[19px]">
                        Practical guides, career advice, education opportunities and professional resources for the next generation of maritime professionals.
                    </p>
                    <div class="mt-7 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                        <a href="#career" class="inline-block rounded-lg bg-gold px-5 py-[13px] text-center font-extrabold text-ink transition hover:brightness-95">
                            Explore Careers →
                        </a>
                        <a href="#resources" class="inline-block rounded-lg border border-white/55 px-5 py-[13px] text-center font-extrabold text-white transition hover:bg-white/10">
                            Get Free Resources
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- CAREER --}}
        <section id="career" class="scroll-mt-24 py-14 sm:py-[72px]">
            <div class="mx-auto w-[min(1120px,92%)]">
                <div class="mb-6 max-w-[680px] sm:mb-[30px]">
                    <div class="text-[11px] font-extrabold uppercase tracking-[.14em] text-[#f1c84a] sm:text-xs sm:tracking-[.18em]">Start your journey</div>
                    <h2 class="my-2 font-serif text-[28px] font-bold leading-[1.15] sm:text-[32px] lg:text-[38px]">Everything you need to move forward.</h2>
                    <p class="text-muted">From your first step as a cadet to your next professional opportunity, Nautivance brings practical maritime information into one place.</p>
                </div>

                @php
                    $categories = [
                        ['icon' => '⚓', 'title' => 'Maritime Careers', 'desc' => 'Career paths, roles, salaries, CV guidance and interview preparation.', 'href' => 'category/'],
                        ['icon' => '🚢', 'title' => 'Seafarer Guide', 'desc' => 'Practical explanations of certificates, regulations, navigation and ship operations.', 'href' => 'category/', 'id' => 'guide'],
                        ['icon' => '🎓', 'title' => 'Education & Scholarships', 'desc' => 'Study opportunities, scholarships and resources for maritime professionals.', 'href' => 'category/', 'id' => 'education'],
                        ['icon' => '💼', 'title' => 'Maritime Jobs', 'desc' => 'Discover opportunities across shipping, ports, offshore and maritime services.', 'href' => 'category/', 'id' => 'jobs'],
                        ['icon' => '📚', 'title' => 'Maritime Knowledge', 'desc' => 'Clear, useful category/ covering the concepts professionals encounter at sea and ashore.', 'href' => 'category/'],
                        ['icon' => '🧰', 'title' => 'Professional Resources', 'desc' => 'Templates, checklists, trackers and tools designed to save maritime professionals time.', 'href' => 'category/'],
                    ];
                @endphp

                {{-- HP: 1 kolom • Tablet: 2 kolom • Desktop: 3 kolom --}}
                <div class="grid gap-4 sm:grid-cols-2 sm:gap-[18px] lg:grid-cols-3">
                    @foreach ($categories as $category)
                        <a
                            href="{{ $category['href'] }}"
                            @isset($category['id']) id="{{ $category['id'] }}" @endisset
                            class="block scroll-mt-24 transition duration-300 ease-out hover:-translate-y-2"
                        >
                            <div class="h-full rounded-[14px] border border-slate-200 bg-white p-5 shadow-[0_8px_25px_rgba(11,35,60,.05)] transition-shadow duration-300 hover:shadow-[0_16px_35px_rgba(11,35,60,.12)] sm:p-[26px]">
                                <div class="text-[30px]">{{ $category['icon'] }}</div>
                                <h3 class="mb-[7px] mt-2.5 text-lg font-bold sm:text-xl">{{ $category['title'] }}</h3>
                                <p class="text-sm text-muted">{{ $category['desc'] }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- RESOURCES --}}
        <section id="resources" class="scroll-mt-24 py-14 sm:py-[72px]">
            <div class="mx-auto w-[min(1120px,92%)]">
                <div class="rounded-2xl bg-navy p-6 text-white sm:p-9 lg:rounded-[20px] lg:p-[45px]">
                    {{-- HP & Tablet: bertumpuk • Desktop: 2 kolom --}}
                    <div class="grid items-center gap-8 lg:grid-cols-[1.1fr_.9fr] lg:gap-[35px]">
                        <div class="max-w-[680px]">
                            <div class="text-[11px] font-extrabold uppercase tracking-[.14em] text-[#f1c84a] sm:text-xs sm:tracking-[.18em]">Free download</div>
                            <h2 class="my-2 font-serif text-[28px] font-bold leading-[1.15] sm:text-[32px] lg:text-[38px]">Get the Seafarer Career Checklist.</h2>
                            <p class="mb-6 text-[#c8d7e7] sm:mb-[30px]">A practical starting checklist for documents, CV preparation, applications and interview readiness.</p>
                            <a href="#newsletter" class="block rounded-lg bg-gold px-5 py-[13px] text-center font-extrabold text-ink transition hover:brightness-95 sm:inline-block">
                                Get the Checklist →
                            </a>
                        </div>

                        <div class="rounded-[14px] bg-white p-5 text-ink sm:p-[25px]">
                            <strong>Inside the checklist</strong>
                            <ul class="mt-3 space-y-[9px]">
                                <li>✓ Essential document checklist</li>
                                <li>✓ CV preparation checklist</li>
                                <li>✓ Job application tracker</li>
                                <li>✓ Interview preparation points</li>
                                <li>✓ Career planning prompts</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ARTICLES --}}
        <section class="py-14 sm:py-[72px]">
            <div class="mx-auto w-[min(1120px,92%)]">
                <div class="mb-6 max-w-[680px] sm:mb-[30px]">
                    <div class="text-[11px] font-extrabold uppercase tracking-[.14em] text-[#f1c84a] sm:text-xs sm:tracking-[.18em]">From the journal</div>
                    <h2 class="my-2 font-serif text-[28px] font-bold leading-[1.15] sm:text-[32px] lg:text-[38px]">Practical maritime guides.</h2>
                    <p class="text-muted">Our first content pillars are designed around questions maritime professionals actually search for.</p>
                </div>

                {{-- HP: 1 kolom • Tablet: 2 kolom (kartu ke-3 melebar) • Desktop: 3 kolom --}}
                <div class="grid gap-4 sm:grid-cols-2 sm:gap-[18px] lg:grid-cols-3">
                    <article class="overflow-hidden rounded-[14px] border border-slate-200 bg-white">
                        <div class="flex h-[110px] items-end bg-[linear-gradient(135deg,#0a3768,#0e78b8)] p-[18px] font-extrabold text-white sm:h-[130px]">
                            How to Become a Deck Officer
                        </div>
                        <div class="p-5">
                            <small class="text-[13px] font-extrabold uppercase tracking-[.1em] text-gold">Career Guide</small>
                            <p class="mt-4">Understand the career path, certificates and practical steps from cadet to officer.</p>
                        </div>
                    </article>

                    <article class="overflow-hidden rounded-[14px] border border-slate-200 bg-white">
                        <div class="flex h-[110px] items-end bg-[linear-gradient(135deg,#0a3768,#0e78b8)] p-[18px] font-extrabold text-white sm:h-[130px]">
                            The Seafarer CV Guide
                        </div>
                        <div class="p-5">
                            <small class="text-[13px] font-extrabold uppercase tracking-[.1em] text-gold">Career Tools</small>
                            <p class="mt-4">What to include, what to remove and how to make your maritime experience stand out.</p>
                        </div>
                    </article>

                    <article class="overflow-hidden rounded-[14px] border border-slate-200 bg-white sm:col-span-2 lg:col-span-1">
                        <div class="flex h-[110px] items-end bg-[linear-gradient(135deg,#0a3768,#0e78b8)] p-[18px] font-extrabold text-white sm:h-[130px]">
                            STCW Explained
                        </div>
                        <div class="p-5">
                            <small class="text-[13px] font-extrabold uppercase tracking-[.1em] text-gold">Seafarer Guide</small>
                            <p class="mt-4">A simple guide to the certification framework every seafarer should understand.</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        {{-- NEWSLETTER --}}
        <section id="newsletter" class="scroll-mt-24 bg-[#edf3f8] py-14 sm:py-[72px]">
            <div class="mx-auto w-[min(1120px,92%)]">
                <div class="mb-6 max-w-[680px] sm:mb-[30px]">
                    <div class="text-[11px] font-extrabold uppercase tracking-[.14em] text-[#f1c84a] sm:text-xs sm:tracking-[.18em]">Stay ahead</div>
                    <h2 class="my-2 font-serif text-[28px] font-bold leading-[1.15] sm:text-[32px] lg:text-[38px]">Join the Nautivance community.</h2>
                    <p class="text-muted">Get useful maritime career resources, scholarship opportunities and new guides by email.</p>
                </div>

                <form wire:submit="subscribe" class="flex max-w-[620px] flex-col gap-2.5 sm:flex-row">
                    <input
                        type="email"
                        wire:model="email"
                        placeholder="Your email address"
                        aria-label="Email address"
                        autocomplete="email"
                        class="min-w-0 flex-1 rounded-lg border border-[#cbd6e2] bg-white p-[15px] text-base focus:border-ocean focus:outline-none focus:ring-2 focus:ring-ocean/25"
                    >
                    <button type="submit" class="w-full cursor-pointer whitespace-nowrap rounded-lg bg-gold px-5 py-[13px] font-extrabold text-ink transition hover:brightness-95 sm:w-auto">
                        Subscribe
                    </button>
                </form>

                @error('email')
                    <p class="mt-2.5 text-sm font-semibold text-red-700">{{ $message }}</p>
                @enderror

                @if ($subscribed)
                    <p class="mt-2.5 text-sm font-semibold text-green-700">Terima kasih! Kamu sudah terdaftar.</p>
                @endif
            </div>
        </section>
    </main>
</div>