<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tag;
use App\Models\Language;
use App\Models\Translation;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create Tags
        $tags = ['mobile', 'desktop', 'web', 'api', 'admin', 'frontend', 'backend'];
        foreach ($tags as $tagName) {
            Tag::create(['name' => $tagName]);
        }

        // Create Languages
        $languages = [
            ['code' => 'en', 'name' => 'English'],
            ['code' => 'fr', 'name' => 'French'],
            ['code' => 'es', 'name' => 'Spanish'],
            ['code' => 'de', 'name' => 'German'],
        ];
        foreach ($languages as $lang) {
            Language::create($lang);
        }

        // Create 100k Translations with random tags
        $batchSize = 5000; // Chunking to avoid memory issues
        $total = 100000;
        for ($i = 0; $i < $total; $i += $batchSize) {
            Translation::factory($batchSize)->create()->each(function ($t) {
                // Attach 1-3 random tags
                $t->tags()->attach(Tag::inRandomOrder()->take(rand(1, 3))->pluck('id'));
            });
        }
    }
}