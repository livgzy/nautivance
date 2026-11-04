<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\ArticleCustom;
use App\Models\Category;
use App\Models\User;

class ArticleCustomSeeder extends Seeder
{
    private function richContent(int $imageSeed, string $label): string
    {
        return <<<HTML
        <p>Lorem ipsum dolor sit amet, <strong>consectetur adipiscing elit</strong>, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
 
        <img src="https://picsum.photos/800/450?random={$imageSeed}" alt="Ilustrasi artikel">
 
        <p>Beberapa poin penting:</p>
        <ul>
            <li>Lorem ipsum dolor sit amet consectetur adipiscing elit</li>
            <li>Sed do eiusmod tempor incididunt ut labore et dolore magna</li>
            <li>Ut enim ad minim veniam quis nostrud exercitation ullamco</li>
        </ul>
 
        <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. <em>{$label}</em></p>
        HTML;
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $author = User::where('is_admin', 1)->first() ?? User::first();
 
        // 'category' = slug kategori di CategorySeeder. article_customs.name selalu
        // diambil dari nama kategori itu, jadi keduanya pasti sama.
        $items = [
            [
                'category' => 'webinar',
                'slug' => 'webinar-persiapan-sign-on-pertama',
                'title' => 'Webinar: Persiapan Sign On Pertama untuk Kadet',
                'excerpt' => 'Webinar gratis tentang dokumen, mental, dan perlengkapan yang perlu disiapkan sebelum naik kapal pertama kali.',
                'custom_slug' => 'webinar-sign-on-pertama',
                'custom_fields' => [
                    'platform' => 'Zoom',
                    'schedule' => now()->addWeeks(2)->setTime(19, 0)->toDateTimeString(),
                    'duration_minutes' => 90,
                    'speaker' => 'Capt. Andi Prasetyo',
                    'is_free' => true,
                    'registration_url' => 'https://example.com/webinar/sign-on-pertama',
                ],
            ],
            [
                'category' => 'event',
                'slug' => 'job-fair-maritim-jakarta-2026',
                'title' => 'Job Fair Maritim Jakarta 2026',
                'excerpt' => 'Pameran lowongan kerja maritim dengan puluhan perusahaan pelayaran, sesi CV clinic, dan wawancara langsung di tempat.',
                'custom_slug' => 'event-job-fair-maritim-2026',
                'custom_fields' => [
                    'venue' => 'Jakarta Convention Center',
                    'city' => 'Jakarta',
                    'start_date' => now()->addMonth()->toDateString(),
                    'end_date' => now()->addMonth()->addDays(2)->toDateString(),
                    'ticket' => 'Gratis dengan registrasi online',
                    'participating_companies' => 45,
                    'website' => 'https://example.com/jobfair-maritim',
                ],
            ],
            [
                'category' => 'podcast',
                'slug' => 'podcast-cerita-kapten-34-tahun-di-laut',
                'title' => 'Podcast: Cerita Kapten, 34 Tahun Berlayar di Laut',
                'excerpt' => 'Obrolan santai bersama kapten senior tentang suka duka karier pelayaran, keluarga, dan pesan untuk generasi muda.',
                'custom_slug' => 'podcast-episode-12-cerita-kapten',
                'custom_fields' => [
                    'episode' => 12,
                    'host' => 'Tim Nautivance',
                    'guest' => 'Capt. Hendra Wijaya',
                    'duration_minutes' => 48,
                    'audio_url' => 'https://example.com/podcast/episode-12.mp3',
                    'topics' => ['Karier', 'Kehidupan di Kapal', 'Tips Kadet'],
                ],
            ],
        ];
 
        foreach ($items as $i => $item) {
            $seed = 60 + $i;
            $category = Category::where('slug', $item['category'])->firstOrFail();
 
            $article = Article::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'category_id' => $category->id,
                    'user_id' => $author->id,
                    'title' => $item['title'],
                    'excerpt' => $item['excerpt'],
                    'thumbnail' => "https://picsum.photos/600/400?random={$seed}",
                    'content' => $this->richContent($seed, $item['title']),
                    'status' => 'published',
                    'published_at' => now()->subDays($i + 1),
                ]
            );
 
            ArticleCustom::updateOrCreate(
                ['article_id' => $article->id],
                [
                    'name' => $category->name,
                    'slug' => $item['custom_slug'],
                    'custom_fields' => $item['custom_fields'],
                ]
            );
        }
    }
}
