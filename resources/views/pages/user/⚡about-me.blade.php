<?php

use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Tentang Kami - Nautivance')] class extends Component
{
    // Angka ringkas, di-cache 10 menit supaya halaman statis ini tidak query tiap kunjungan.
    #[Computed]
    public function stats(): array
    {
        return Cache::remember('about.stats', 600, fn () => [
            'articles' => Article::where('status', 'published')->count(),
            'categories' => Category::count(),
        ]);
    }

    // Kategori diambil dari DB, jadi kategori baru buatan admin ikut muncul otomatis.
    // Yang di-cache ARRAY biasa, bukan Eloquent Collection: Laravel 13 menolak membaca
    // objek dari cache (cache.serializable_classes = false).
    #[Computed]
    public function categories(): array
    {
        return Cache::remember('about.categories.v2', 600, fn () => Category::orderBy('name')
            ->get(['id', 'name', 'slug', 'description'])
            ->toArray());
    }
};
?>

<div>
    {{-- ===== HERO ===== --}}
    <section class="bg-gradient-to-br from-[#06254a] to-[#0a3768] text-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
            <div class="max-w-3xl">
                <div class="uppercase tracking-[0.18em] text-xs font-extrabold text-[#d7a72b]">Tentang Kami</div>
                <h1 class="font-serif text-4xl md:text-5xl font-bold mt-3 leading-tight">
                    Informasi pelayaran yang jelas, untuk langkah karier yang pasti.
                </h1>
                <p class="mt-5 text-lg text-white/80">
                    Nautivance adalah pusat informasi dunia maritim Indonesia: panduan karier, sertifikasi, pendidikan,
                    lowongan, dan bahan belajar untuk calon pelaut maupun pelaut yang sudah berlayar.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ url('/') }}" wire:navigate class="rounded-lg bg-[#d7a72b] px-6 py-3 font-semibold text-[#10243a] hover:brightness-110 transition">Mulai Menjelajah</a>
                    <a href="#misi" class="rounded-lg border border-white/30 px-6 py-3 font-semibold text-white hover:bg-white/10 transition">Misi Kami</a>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== ANGKA ===== --}}
    <section class="border-b border-slate-200 bg-white">
        <dl class="max-w-6xl mx-auto grid grid-cols-2 gap-6 px-4 sm:px-6 lg:px-8 py-8 text-center md:grid-cols-3">
            <div>
                <dt class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Artikel terbit</dt>
                <dd class="mt-1 font-serif text-3xl font-bold text-[#0a3768]">{{ number_format($this->stats['articles']) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Kategori</dt>
                <dd class="mt-1 font-serif text-3xl font-bold text-[#0a3768]">{{ number_format($this->stats['categories']) }}</dd>
            </div>
            <div class="col-span-2 md:col-span-1">
                <dt class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Biaya mengakses</dt>
                <dd class="mt-1 font-serif text-3xl font-bold text-[#0a3768]">Gratis</dd>
            </div>
        </dl>
    </section>

    {{-- ===== MISI & VISI ===== --}}
    <section id="misi" class="py-16 md:py-20 scroll-mt-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mb-10">
                <div class="uppercase tracking-[0.18em] text-xs font-extrabold text-amber-500">Misi &amp; Visi</div>
                <h2 class="font-serif text-3xl md:text-4xl font-bold text-[#10243a] mt-2">Mengapa Nautivance ada</h2>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <article class="rounded-2xl border border-slate-200 bg-white p-7">
                    <h3 class="font-serif text-xl font-bold text-[#10243a]">Misi</h3>
                    <p class="mt-3 text-slate-600">
                        Menyediakan informasi maritim yang akurat, mudah dipahami, dan mudah dicari, supaya
                        setiap orang yang ingin berkarier di laut tahu langkah apa yang harus diambil berikutnya.
                    </p>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-white p-7">
                    <h3 class="font-serif text-xl font-bold text-[#10243a]">Visi</h3>
                    <p class="mt-3 text-slate-600">
                        Menjadi rujukan pertama bagi calon pelaut dan pelaut Indonesia dalam mencari
                        panduan karier, pendidikan, dan peluang kerja.
                    </p>
                </article>
            </div>
        </div>
    </section>

    {{-- ===== NILAI ===== --}}
    <section class="bg-slate-50 py-16 md:py-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mb-10">
                <div class="uppercase tracking-[0.18em] text-xs font-extrabold text-amber-500">Prinsip Kami</div>
                <h2 class="font-serif text-3xl md:text-4xl font-bold text-[#10243a] mt-2">Cara kami bekerja</h2>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach([
                    ['Akurat', 'Informasi ditulis dengan rujukan yang jelas dan diperbarui bila aturan berubah.'],
                    ['Mudah dipahami', 'Bahasa lugas, tanpa istilah yang membingungkan bagi pemula.'],
                    ['Terstruktur', 'Setiap topik punya kategori dan detail yang konsisten sehingga cepat dicari.'],
                    ['Terbuka', 'Dapat diakses siapa saja tanpa biaya dan tanpa perlu mendaftar.'],
                ] as [$title, $text])
                    <div class="rounded-2xl border border-slate-200 bg-white p-6">
                        <div class="mb-4 h-1 w-10 rounded bg-[#d7a72b]"></div>
                        <h3 class="font-bold text-[#10243a]">{{ $title }}</h3>
                        <p class="mt-2 text-sm text-slate-500">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== YANG KAMI SAJIKAN (dari DB) ===== --}}
    @if($this->categories !== [])
        <section class="py-16 md:py-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-2xl mb-10">
                    <div class="uppercase tracking-[0.18em] text-xs font-extrabold text-amber-500">Yang Kami Sajikan</div>
                    <h2 class="font-serif text-3xl md:text-4xl font-bold text-[#10243a] mt-2">Semua yang kamu butuhkan untuk melangkah</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($this->categories as $category)
                        <a href="{{ route('articles', $category['slug']) }}" wire:navigate wire:key="about-cat-{{ $category['id'] }}"
                            class="block rounded-2xl border border-slate-200 bg-white p-6 transition hover:-translate-y-1 hover:shadow-lg">
                            <h3 class="font-serif text-lg font-bold text-[#10243a]">{{ $category['name'] }}</h3>
                            <p class="mt-2 text-sm text-slate-500">{{ $category['description'] }}</p>
                            <span class="mt-4 inline-block text-sm font-semibold text-[#1474bd]">Lihat artikel &rarr;</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ===== DISCLAIMER ===== --}}
    <section class="pb-16 md:pb-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-[#d7a72b]/40 bg-[#d7a72b]/10 p-6 md:p-8">
                <h2 class="font-serif text-xl font-bold text-[#10243a]">Catatan penting</h2>
                <p class="mt-2 text-slate-600">
                    Nautivance adalah media informasi dan bukan lembaga pemerintah, lembaga pendidikan, atau agen
                    penyalur tenaga kerja. Untuk keputusan resmi seperti sertifikasi, dokumen pelaut, dan kontrak kerja,
                    selalu rujuk instansi atau perusahaan terkait.
                </p>
            </div>
        </div>
    </section>
</div>