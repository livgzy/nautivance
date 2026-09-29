<?php

use App\Models\Article;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

new class extends Component
{
    #[Locked]
    public Article $article;

    public function mount(Article $article): void
    {
        // Draft/archived cuma boleh dilihat admin (preview). Selain itu 404,
        // bukan 403, supaya orang luar nggak bisa tahu artikel itu ada.
        $canPreview = (bool) auth()->user()?->is_admin;
        abort_unless($article->status === 'published' || $canPreview, 404);

        $article->loadMissing('category', 'author');
        $this->article = $article;
    }

    /**
     * Hanya izinkan URL http/https (dan path relatif kalau diminta). Menolak
     * javascript:, data:, vbscript:, dst yang bisa dipakai buat XSS lewat href/src.
     */
    private function safeUrl(?string $url, bool $allowRelative = false): ?string
    {
        if (blank($url)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        if (in_array($scheme, ['http', 'https'], true)) {
            return $url;
        }

        if ($allowRelative && $scheme === '' && str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return $url;
        }

        return null;
    }

    /**
     * Bersihkan HTML isi artikel (allowlist): script, iframe, onclick=, style=,
     * javascript: dll otomatis dibuang. Yang lolos cuma tag pemformatan biasa.
     */
    private function sanitize(string $html): string
    {
        $config = (new HtmlSanitizerConfig())
            ->allowSafeElements()
            ->allowElement('a', ['href', 'title', 'target', 'class'])
            ->allowElement('img', ['src', 'alt', 'title'])
            ->allowLinkSchemes(['https', 'http', 'mailto'])
            ->allowMediaSchemes(['https', 'http'])
            ->allowRelativeLinks()
            ->allowRelativeMedias()
            ->forceAttribute('a', 'rel', 'noopener noreferrer');

        return (new HtmlSanitizer($config))->sanitize($html);
    }

    public function with(): array
    {
        $article = $this->article;
        $slug = $article->category->slug;
        $detail = $article->detail();

        $applyUrl = match ($slug) {
            'jobs' => $this->safeUrl($detail?->apply_url),
            'education' => $this->safeUrl($detail?->application_url),
            default => null,
        };

        $fileUrl = ($slug === 'resources' && $detail?->file_path)
            ? Storage::disk('public')->url($detail->file_path)
            : null;

        return [
            'contentHtml' => $this->sanitize($article->content),
            'thumbnailUrl' => $this->safeUrl($article->thumbnail, allowRelative: true),
            'detail' => $detail,
            'applyUrl' => $applyUrl,
            'fileUrl' => $fileUrl,
            'related' => Article::query()
                ->where('category_id', $article->category_id)
                ->where('status', 'published')
                ->whereKeyNot($article->id)
                ->latest('published_at')
                ->limit(3)
                ->get(),
        ];
    }

    public function render()
    {
        return $this->view([])->title($this->article->title . ' - Nautivance');
    }
};
?>

<div>
    {{-- Breadcrumb --}}
    <div class="bg-slate-100 py-8 mb-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 text-sm text-slate-500">
            <a href="{{ route('home') }}" class="text-blue-600 hover:underline">Home</a>
            <span class="mx-1">&rsaquo;</span>
            <a href="{{ route('articles', $article->category->slug) }}" class="text-blue-600 hover:underline">{{ $article->category->name }}</a>
        </div>
    </div>

    <article class="max-w-3xl mx-auto px-4 sm:px-6 pb-16">
        <small class="uppercase tracking-[0.18em] text-xs font-extrabold text-amber-500">{{ $article->category->name }}</small>
        <h1 class="font-serif text-3xl md:text-4xl font-bold text-[#10243a] mt-2 mb-4 leading-tight">{{ $article->title }}</h1>

        <div class="text-sm text-slate-500 mb-6">
            @if($article->published_at)
                <span>{{ $article->published_at->format('d M Y') }}</span>
            @endif
            @if($article->author)
                <span class="mx-1">&bull;</span>
                <span>{{ $article->author->name }}</span>
            @endif
            @if($article->status !== 'published')
                <span class="ml-2 rounded bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">Preview ({{ $article->status }})</span>
            @endif
        </div>

        @if($thumbnailUrl)
            <img src="{{ $thumbnailUrl }}" alt="{{ $article->title }}" class="w-full h-64 md:h-80 object-cover rounded-2xl mb-8">
        @endif

        {{-- Info khusus per kategori --}}
        @if($detail)
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 mb-8">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    @switch($article->category->slug)
                        @case('jobs')
                            <div><dt class="text-slate-500">Perusahaan</dt><dd class="font-semibold text-[#10243a]">{{ $detail->company_name }}</dd></div>
                            @if($detail->location)<div><dt class="text-slate-500">Lokasi</dt><dd class="font-semibold text-[#10243a]">{{ $detail->location }}</dd></div>@endif
                            @if($detail->salary_range)<div><dt class="text-slate-500">Kisaran Gaji</dt><dd class="font-semibold text-[#10243a]">{{ $detail->salary_range }}</dd></div>@endif
                            @if($detail->job_type)<div><dt class="text-slate-500">Tipe</dt><dd class="font-semibold text-[#10243a]">{{ Str::headline($detail->job_type) }}</dd></div>@endif
                            @break

                        @case('education')
                            <div><dt class="text-slate-500">Penyelenggara</dt><dd class="font-semibold text-[#10243a]">{{ $detail->provider }}</dd></div>
                            <div><dt class="text-slate-500">Batas Pendaftaran</dt><dd class="font-semibold text-[#10243a]">{{ $detail->deadline->format('d M Y') }}</dd></div>
                            @if($detail->funding_amount)<div><dt class="text-slate-500">Pendanaan</dt><dd class="font-semibold text-[#10243a]">{{ $detail->funding_amount }}</dd></div>@endif
                            @break

                        @case('guide')
                            @if($detail->certificate_code)<div><dt class="text-slate-500">Sertifikat</dt><dd class="font-semibold text-[#10243a]">{{ $detail->certificate_code }}</dd></div>@endif
                            @if($detail->applicable_rank)<div><dt class="text-slate-500">Berlaku untuk</dt><dd class="font-semibold text-[#10243a]">{{ $detail->applicable_rank }}</dd></div>@endif
                            @break

                        @case('career')
                            @if($detail->career_stage)<div><dt class="text-slate-500">Tahap Karier</dt><dd class="font-semibold text-[#10243a]">{{ Str::upper(str_replace('_', ' ', $detail->career_stage)) }}</dd></div>@endif
                            @if($detail->topic)<div><dt class="text-slate-500">Topik</dt><dd class="font-semibold text-[#10243a]">{{ $detail->topic }}</dd></div>@endif
                            @break

                        @case('resources')
                            @if($detail->file_type)<div><dt class="text-slate-500">Format File</dt><dd class="font-semibold text-[#10243a]">{{ Str::upper($detail->file_type) }}</dd></div>@endif
                            @break
                    @endswitch
                </dl>

                @if($applyUrl)
                    <a href="{{ $applyUrl }}" target="_blank" rel="noopener noreferrer nofollow"
                       class="mt-5 inline-block rounded-lg bg-[#d7a72b] px-5 py-3 text-sm font-extrabold text-[#10243a] hover:brightness-95 transition">
                        {{ $article->category->slug === 'jobs' ? 'Lamar Sekarang' : 'Daftar / Info Resmi' }} &rarr;
                    </a>
                @endif

                @if($fileUrl)
                    <a href="{{ $fileUrl }}" download
                       class="mt-5 inline-block rounded-lg bg-[#0a3768] px-5 py-3 text-sm font-bold text-white hover:bg-[#06254a] transition">
                        Unduh File
                    </a>
                @endif
            </div>
        @endif

        {{-- Isi artikel: sudah lewat sanitizer di with(), baru aman dirender unescaped --}}
        <div class="text-[17px] leading-relaxed text-[#10243a]
            [&_p]:mb-5 [&_h2]:font-serif [&_h2]:text-2xl [&_h2]:font-bold [&_h2]:mt-8 [&_h2]:mb-3
            [&_h3]:font-serif [&_h3]:text-xl [&_h3]:font-bold [&_h3]:mt-6 [&_h3]:mb-2
            [&_strong]:text-[#0a3768] [&_em]:text-slate-600
            [&_a]:text-[#1474bd] [&_a]:underline
            [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:mb-5 [&_ol]:list-decimal [&_ol]:pl-6 [&_ol]:mb-5 [&_li]:mb-1
            [&_img]:rounded-xl [&_img]:my-6 [&_img]:max-w-full [&_img]:h-auto
            [&_blockquote]:border-l-4 [&_blockquote]:border-[#d7a72b] [&_blockquote]:pl-4 [&_blockquote]:italic [&_blockquote]:text-slate-600 [&_blockquote]:my-6
            [&_.doc-link]:inline-block [&_.doc-link]:bg-slate-100 [&_.doc-link]:text-[#0a3768] [&_.doc-link]:no-underline [&_.doc-link]:font-bold [&_.doc-link]:px-4 [&_.doc-link]:py-2 [&_.doc-link]:rounded-lg">
            {!! $contentHtml !!}
        </div>
    </article>

    {{-- Artikel terkait --}}
    @if($related->isNotEmpty())
        <section class="bg-slate-50 py-12">
            <div class="max-w-3xl mx-auto px-4 sm:px-6">
                <h2 class="font-serif text-2xl font-bold text-[#10243a] mb-5">Artikel Terkait</h2>
                <div class="space-y-4">
                    @foreach($related as $item)
                        <a href="{{ route('article.show', $item->slug) }}" wire:navigate class="block rounded-xl bg-white border border-slate-200 p-4 hover:shadow-md transition">
                            <h3 class="font-bold text-[#10243a] leading-snug">{{ $item->title }}</h3>
                            <p class="text-sm text-slate-500 mt-1">{{ $item->excerpt }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>