<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Translation;
use App\Models\Tag;

class TranslationSeeder extends Seeder
{
    public function run()
    {
        Translation::factory(1000)->create()->each(function ($t) {
            $tags = Tag::inRandomOrder()->take(rand(1, 3))->pluck('id');
            $t->tags()->attach($tags);
        });
    }
}