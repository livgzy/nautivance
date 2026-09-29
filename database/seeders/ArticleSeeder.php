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

        // --- Career (2 artikel awal, dibiarkan seperti semula) ---
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

        // --- Seafarer Guide (1 artikel awal, dibiarkan seperti semula) ---
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

        // --- Education (1 artikel awal, dibiarkan seperti semula) ---
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

        // --- Jobs (1 artikel awal, dibiarkan seperti semula) ---
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

        // --- Resources (1 artikel awal, dibiarkan seperti semula) ---
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

        // --- Tambahan artikel biar lebih banyak buat tes pagination & search ---
        $this->seedMoreCareerArticles($author);
        $this->seedMoreGuideArticles($author);
        $this->seedMoreEducationArticles($author);
        $this->seedMoreJobArticles($author);
        $this->seedMoreResourceArticles($author);
    }

    private function seedMoreCareerArticles(User $author): void
    {
        $categoryId = Category::where('slug', 'career')->value('id');

        $items = [
            ['5-kesalahan-wawancara-kerja-pelayaran', '5 Kesalahan Umum Saat Wawancara Kerja di Perusahaan Pelayaran', 'Hindari kesalahan-kesalahan ini supaya peluang diterima kerja makin besar.', 'cadet', 'Interview Tips'],
            ['jenjang-karier-di-kapal', 'Jenjang Karier di Kapal: Dari Kadet Sampai Kapten', 'Memahami tahapan karier pelaut dari awal sampai posisi tertinggi.', 'mualim_iii', 'Career Path'],
            ['tips-negosiasi-gaji-perwira-pemula', 'Tips Negosiasi Gaji untuk Perwira Pelayaran Pemula', 'Cara menegosiasikan gaji dengan percaya diri tanpa terkesan berlebihan.', 'mualim_ii', 'Salary Guide'],
            ['personal-branding-pelaut-profesional', 'Cara Membangun Personal Branding sebagai Pelaut Profesional', 'Manfaatkan media sosial dan portofolio buat menonjol di industri pelayaran.', 'mualim_i', 'Personal Branding'],
            ['kapal-niaga-vs-kapal-pesiar', 'Perbedaan Kerja di Kapal Niaga vs Kapal Pesiar', 'Kenali plus minus masing-masing sebelum menentukan jalur karier.', 'captain', 'Career Path'],
        ];

        foreach ($items as $i => [$slug, $title, $excerpt, $stage, $topic]) {
            $seed = 10 + $i;
            $article = Article::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $categoryId,
                    'user_id' => $author->id,
                    'title' => $title,
                    'excerpt' => $excerpt,
                    'thumbnail' => "https://picsum.photos/600/400?random={$seed}",
                    'content' => $this->richContent($seed, 'Unduh Panduan Terkait (PDF)'),
                    'status' => 'published',
                    'published_at' => now()->subDays($i + 1),
                ]
            );
            ArticleCareer::updateOrCreate(
                ['article_id' => $article->id],
                ['career_stage' => $stage, 'topic' => $topic]
            );
        }
    }

    private function seedMoreGuideArticles(User $author): void
    {
        $categoryId = Category::where('slug', 'guide')->value('id');

        $items = [
            ['panduan-sertifikat-bst', 'Panduan Lengkap Sertifikat BST (Basic Safety Training)', 'Apa saja materi BST dan kenapa wajib dimiliki sebelum naik kapal pertama kali.', 'BST', 'Kadet'],
            ['memahami-sistem-navigasi-ecdis', 'Memahami Sistem Navigasi ECDIS untuk Perwira Dek', 'Dasar-dasar penggunaan ECDIS dalam operasi navigasi modern.', 'ECDIS', 'Deck Officer'],
            ['prosedur-keselamatan-di-atas-kapal', 'Prosedur Keselamatan di Atas Kapal yang Wajib Diketahui', 'Rangkuman SOP keselamatan dasar untuk seluruh awak kapal.', 'ISM Code', 'Semua Rank'],
            ['mengenal-coc-certificate-of-competency', 'Apa Itu COC (Certificate of Competency) dan Cara Mendapatkannya', 'Syarat dan tahapan memperoleh sertifikat kompetensi pelaut.', 'COC', 'Perwira'],
            ['panduan-dasar-bongkar-muat-kontainer', 'Panduan Dasar Bongkar Muat Kapal Kontainer', 'Proses dan istilah penting dalam operasi bongkar muat peti kemas.', 'IMDG Code', 'Deck Officer'],
        ];

        foreach ($items as $i => [$slug, $title, $excerpt, $certCode, $rank]) {
            $seed = 20 + $i;
            $article = Article::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $categoryId,
                    'user_id' => $author->id,
                    'title' => $title,
                    'excerpt' => $excerpt,
                    'thumbnail' => "https://picsum.photos/600/400?random={$seed}",
                    'content' => $this->richContent($seed, 'Unduh Ringkasan Terkait (PDF)'),
                    'status' => 'published',
                    'published_at' => now()->subDays($i + 1),
                ]
            );
            ArticleGuide::updateOrCreate(
                ['article_id' => $article->id],
                ['certificate_code' => $certCode, 'applicable_rank' => $rank]
            );
        }
    }

    private function seedMoreEducationArticles(User $author): void
    {
        $categoryId = Category::where('slug', 'education')->value('id');

        // Semua entri di sini butuh 'deadline' beneran (kolomnya wajib diisi
        // di skema), jadi saya pilih topik yang memang punya batas waktu
        // pendaftaran, bukan artikel listicle/perbandingan yang nggak punya deadline.
        $items = [
            ['beasiswa-pip-semarang-2026', 'Beasiswa Pelayaran dari PIP Semarang 2026', 'PIP Semarang membuka pendaftaran beasiswa untuk calon taruna baru.', 'PIP Semarang', 1, 'Sebagian (SPP)', 'https://pip-semarang.ac.id'],
            ['beasiswa-ikatan-dinas-kemenhub', 'Beasiswa Ikatan Dinas Kemenhub untuk Taruna Pelayaran', 'Program beasiswa penuh dengan ikatan dinas setelah lulus.', 'Kementerian Perhubungan', 3, 'Penuh + Ikatan Dinas', 'https://beasiswa.dephub.go.id/ikatan-dinas'],
            ['pendaftaran-stip-jakarta', 'Pendaftaran STIP Jakarta Tahun Ajaran Baru', 'Syarat dan jadwal pendaftaran STIP Jakarta tahun ini.', 'STIP Jakarta', 2, null, 'https://stipjakarta.ac.id'],
            ['beasiswa-lpdp-kemaritiman', 'Beasiswa LPDP untuk Studi Kemaritiman di Luar Negeri', 'Daftar program beasiswa internasional untuk studi kemaritiman.', 'LPDP', 5, 'Penuh', 'https://lpdp.kemenkeu.go.id'],
            ['beasiswa-yayasan-sampoerna-anak-pelaut', 'Beasiswa Yayasan Sampoerna untuk Anak Pelaut', 'Program beasiswa pendidikan khusus untuk anak dari keluarga pelaut.', 'Yayasan Sampoerna', 4, 'Sebagian', 'https://sampoernafoundation.org'],
        ];

        foreach ($items as $i => [$slug, $title, $excerpt, $provider, $deadlineMonths, $funding, $applyUrl]) {
            $seed = 30 + $i;
            $article = Article::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $categoryId,
                    'user_id' => $author->id,
                    'title' => $title,
                    'excerpt' => $excerpt,
                    'thumbnail' => "https://picsum.photos/600/400?random={$seed}",
                    'content' => $this->richContent($seed, 'Unduh Info Selengkapnya (PDF)'),
                    'status' => 'published',
                    'published_at' => now()->subDays($i + 1),
                ]
            );
            ArticleEducation::updateOrCreate(
                ['article_id' => $article->id],
                [
                    'provider' => $provider,
                    'deadline' => now()->addMonths($deadlineMonths),
                    'funding_amount' => $funding,
                    'application_url' => $applyUrl,
                ]
            );
        }
    }

    private function seedMoreJobArticles(User $author): void
    {
        $categoryId = Category::where('slug', 'jobs')->value('id');

        $items = [
            ['lowongan-chief-engineer-meratus', 'Lowongan Chief Engineer — PT Meratus Line', 'Meratus Line mencari Chief Engineer berpengalaman untuk kapal kontainer.', 'PT Meratus Line', 'Surabaya', 'https://karir.meratusline.com', 'Rp15.000.000 - Rp20.000.000', 'full_time'],
            ['lowongan-kadet-samudera-indonesia', 'Lowongan Kadet Pelayaran — PT Samudera Indonesia', 'Program kadet untuk lulusan baru taruna pelayaran.', 'PT Samudera Indonesia', 'Jakarta', 'https://karir.samudera.id', 'Sesuai ketentuan program kadet', 'internship'],
            ['lowongan-second-officer-maersk', 'Lowongan Second Officer — Maersk Indonesia', 'Maersk membuka posisi Second Officer untuk armada internasional.', 'Maersk Indonesia', 'Jakarta', 'https://careers.maersk.com', 'USD 2.500 - USD 3.200', 'contract'],
            ['lowongan-bosun-djakarta-lloyd', 'Lowongan Bosun — PT Djakarta Lloyd', 'Dibutuhkan Bosun berpengalaman untuk kapal kargo domestik.', 'PT Djakarta Lloyd', 'Jakarta', 'https://djakartalloyd.co.id/karir', 'Rp6.000.000 - Rp8.000.000', 'contract'],
            ['lowongan-crewing-officer-pelindo', 'Lowongan Crewing Officer — PT Pelindo', 'Posisi darat untuk mengelola penempatan awak kapal.', 'PT Pelindo', 'Jakarta', 'https://karir.pelindo.co.id', 'Rp7.000.000 - Rp10.000.000', 'full_time'],
        ];

        foreach ($items as $i => [$slug, $title, $excerpt, $company, $location, $applyUrl, $salary, $jobType]) {
            $seed = 40 + $i;
            $article = Article::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $categoryId,
                    'user_id' => $author->id,
                    'title' => $title,
                    'excerpt' => $excerpt,
                    'thumbnail' => "https://picsum.photos/600/400?random={$seed}",
                    'content' => $this->richContent($seed, 'Unduh Deskripsi Pekerjaan Lengkap (PDF)'),
                    'status' => 'published',
                    'published_at' => now()->subDays($i + 1),
                ]
            );
            ArticleJob::updateOrCreate(
                ['article_id' => $article->id],
                [
                    'company_name' => $company,
                    'location' => $location,
                    'apply_url' => $applyUrl,
                    'salary_range' => $salary,
                    'job_type' => $jobType,
                ]
            );
        }
    }

    private function seedMoreResourceArticles(User $author): void
    {
        $categoryId = Category::where('slug', 'resources')->value('id');

        $items = [
            ['checklist-dokumen-sebelum-naik-kapal', 'Checklist Dokumen Wajib Sebelum Naik Kapal', 'Daftar dokumen yang wajib disiapkan sebelum sign on.', 'resources/checklist-dokumen-naik-kapal.pdf', 'pdf'],
            ['template-surat-lamaran-pelaut', 'Template Surat Lamaran Kerja Pelaut', 'Contoh surat lamaran yang bisa langsung disesuaikan.', 'resources/template-surat-lamaran-pelaut.docx', 'docx'],
            ['contoh-logbook-pelaut', 'Contoh Logbook Pelaut yang Benar', 'Panduan dan contoh pengisian logbook sesuai standar.', 'resources/contoh-logbook-pelaut.pdf', 'pdf'],
            ['template-laporan-praktek-laut', 'Template Laporan Praktek Laut (Prala)', 'Format laporan Prala siap pakai untuk taruna.', 'resources/template-laporan-prala.docx', 'docx'],
            ['panduan-cv-ats-friendly-pelaut', 'Panduan Format CV ATS-Friendly untuk Pelaut', 'Tips format CV supaya lolos sistem seleksi otomatis (ATS).', 'resources/panduan-cv-ats-pelaut.pdf', 'pdf'],
        ];

        foreach ($items as $i => [$slug, $title, $excerpt, $filePath, $fileType]) {
            $seed = 50 + $i;
            $article = Article::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $categoryId,
                    'user_id' => $author->id,
                    'title' => $title,
                    'excerpt' => $excerpt,
                    'thumbnail' => "https://picsum.photos/600/400?random={$seed}",
                    'content' => $this->richContent($seed, 'Unduh ' . $title),
                    'status' => 'published',
                    'published_at' => now()->subDays($i + 1),
                ]
            );
            ArticleResource::updateOrCreate(
                ['article_id' => $article->id],
                ['file_path' => $filePath, 'file_type' => $fileType]
            );
        }
    }
}