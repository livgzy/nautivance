<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\ArticleCareer;
use App\Models\ArticleEducation;
use App\Models\ArticleGuide;
use App\Models\ArticleJob;
use App\Models\ArticleResource;
use App\Models\Category;
use App\Models\User;

class ArticleSeeder extends Seeder
{

    private function richContent(int $imageSeed, string $docLabel): string
    {
        return <<<HTML
        <p>Lorem ipsum dolor sit amet, <strong>consectetur adipiscing elit</strong>, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>

        <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. <em>Excepteur sint occaecat cupidatat non proident</em>, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

        <img src="https://picsum.photos/800/450?random={$imageSeed}" alt="Ilustrasi artikel">

        <p>Beberapa poin penting:</p>
        <ul>
            <li>Lorem ipsum dolor sit amet consectetur adipiscing elit</li>
            <li>Sed do eiusmod tempor incididunt ut labore et dolore magna</li>
            <li>Ut enim ad minim veniam quis nostrud exercitation ullamco</li>
        </ul>

        <p>Untuk referensi lebih lanjut, kunjungi <a href="https://www.imo.org" target="_blank" rel="noopener">situs resmi IMO</a>, atau unduh dokumen pendukungnya di bawah ini:</p>

        <p><a href="/storage/documents/contoh-dokumen.pdf" target="_blank" rel="noopener" class="doc-link">📄 {$docLabel}</a></p>
        HTML;
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $author = User::where('is_admin', 1)->first() ?? User::first();

        // --- Career ---
        $career = Article::updateOrCreate(
            ['slug' => 'cara-menyusun-cv-pelaut'],
            [
                'category_id' => Category::where('slug', 'career')->value('id'),
                'user_id' => $author->id,
                'title' => 'Cara Menyusun CV Pelaut yang Menarik Perekrut',
                'excerpt' => 'Panduan singkat menyusun CV pelaut supaya lebih dilirik perusahaan pelayaran.',
                'thumbnail' => 'https://picsum.photos/600/400?random=1',
                'content' => $this->richContent(1, 'Unduh Template CV Pelaut (PDF)'),
                'status' => 'published',
                'published_at' => now(),
            ]
        );
        ArticleCareer::updateOrCreate(
            ['article_id' => $career->id],
            ['career_stage' => 'cadet', 'topic' => 'CV Tips']
        );

        $career2 = Article::updateOrCreate(
            ['slug' => 'lorem-ipsum-dolor'],
            [
                'category_id' => Category::where('slug', 'career')->value('id'),
                'user_id' => $author->id,
                'title' => 'Do sint sit ad officia ullamco amet Lorem velit officia sint eiusmod minim. ',
                'excerpt' => 'Do eiusmod labore sunt sit fugiat exercitation nostrud nostrud sint ullamco. Laborum in eiusmod sunt ut fugiat officia. Do velit minim eu mollit duis. Nostrud laboris occaecat ea culpa Lorem fugiat culpa nostrud eiusmod reprehenderit. Laboris cupidatat duis aliqua duis ipsum amet ipsum dolor culpa.',
                'thumbnail' => 'https://picsum.photos/600/400?random=1',
                'content' => $this->richContent(1, 'Quis officia officia laborum aliquip ipsum sint officia consectetur aliquip.'),
                'status' => 'published',
                'published_at' => now(),
            ]
        );
        ArticleCareer::updateOrCreate(
            ['article_id' => $career2->id],
            ['career_stage' => 'cadet', 'topic' => 'Cadet Tips']
        );

        // --- Seafarer Guide ---
        $guide = Article::updateOrCreate(
            ['slug' => 'mengenal-sertifikat-stcw'],
            [
                'category_id' => Category::where('slug', 'guide')->value('id'),
                'user_id' => $author->id,
                'title' => 'Mengenal Sertifikat STCW untuk Pelaut',
                'excerpt' => 'Penjelasan dasar tentang sertifikat STCW dan kenapa penting buat pelaut.',
                'thumbnail' => 'https://picsum.photos/600/400?random=2',
                'content' => $this->richContent(2, 'Unduh Ringkasan Ketentuan STCW (PDF)'),
                'status' => 'published',
                'published_at' => now(),
            ]
        );
        ArticleGuide::updateOrCreate(
            ['article_id' => $guide->id],
            ['certificate_code' => 'STCW', 'applicable_rank' => 'Semua Rank']
        );

        // --- Education ---
        $education = Article::updateOrCreate(
            ['slug' => 'beasiswa-pelayaran-2026'],
            [
                'category_id' => Category::where('slug', 'education')->value('id'),
                'user_id' => $author->id,
                'title' => 'Beasiswa Pelayaran 2026 dari Kemenhub',
                'excerpt' => 'Informasi pendaftaran beasiswa pelayaran tahun 2026.',
                'thumbnail' => 'https://picsum.photos/600/400?random=3',
                'content' => $this->richContent(3, 'Unduh Formulir Pendaftaran Beasiswa (PDF)'),
                'status' => 'published',
                'published_at' => now(),
            ]
        );
        ArticleEducation::updateOrCreate(
            ['article_id' => $education->id],
            [
                'provider' => 'Kementerian Perhubungan',
                'deadline' => now()->addMonths(2),
                'funding_amount' => 'Penuh (SPP + biaya hidup)',
                'application_url' => 'https://beasiswa.dephub.go.id',
            ]
        );

        // --- Jobs ---
        $job = Article::updateOrCreate(
            ['slug' => 'lowongan-deck-officer-pt-pelni'],
            [
                'category_id' => Category::where('slug', 'jobs')->value('id'),
                'user_id' => $author->id,
                'title' => 'Lowongan Deck Officer — PT Pelni',
                'excerpt' => 'PT Pelni membuka lowongan Deck Officer untuk armada domestik.',
                'thumbnail' => 'https://picsum.photos/600/400?random=4',
                'content' => $this->richContent(4, 'Unduh Deskripsi Pekerjaan Lengkap (PDF)'),
                'status' => 'published',
                'published_at' => now(),
            ]
        );
        ArticleJob::updateOrCreate(
            ['article_id' => $job->id],
            [
                'company_name' => 'PT Pelni',
                'location' => 'Jakarta',
                'apply_url' => 'https://karir.pelni.co.id',
                'salary_range' => 'Rp8.000.000 - Rp12.000.000',
                'job_type' => 'full_time',
            ]
        );

        // --- Resources ---
        $resource = Article::updateOrCreate(
            ['slug' => 'template-cv-pelaut-siap-pakai'],
            [
                'category_id' => Category::where('slug', 'resources')->value('id'),
                'user_id' => $author->id,
                'title' => 'Template CV Pelaut Siap Pakai',
                'excerpt' => 'Unduh template CV pelaut dalam format Word, tinggal isi.',
                'thumbnail' => 'https://picsum.photos/600/400?random=5',
                'content' => $this->richContent(5, 'Unduh Template CV Pelaut (DOCX)'),
                'status' => 'published',
                'published_at' => now(),
            ]
        );
        ArticleResource::updateOrCreate(
            ['article_id' => $resource->id],
            ['file_path' => 'resources/template-cv-pelaut.docx', 'file_type' => 'docx']
        );
    }
}
