<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Career', 'slug' => 'career', 'description' => 'Career paths, roles, salaries, CV guidance and interview preparation.'],
            ['name' => 'Seafarer Guide', 'slug' => 'guide', 'description' => 'Practical explanations of certificates, regulations, navigation and ship operations.'],
            ['name' => 'Education', 'slug' => 'education', 'description' => 'Study opportunities, scholarships and resources for maritime professionals.'],
            ['name' => 'Jobs', 'slug' => 'jobs', 'description' => 'Discover opportunities across shipping, ports, offshore and maritime services.'],
            ['name' => 'Resources', 'slug' => 'resources', 'description' => 'Templates, checklists, trackers and tools designed to save maritime professionals time.'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
