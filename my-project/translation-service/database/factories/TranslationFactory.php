<?php

namespace Database\Factories;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    public function definition()
    {
        $locales = ['en', 'fr', 'es'];
        $value = [];
        foreach ($locales as $locale) {
            $value[$locale] = $this->faker->sentence(3);
        }

        return [
            'key' => $this->faker->word,
            'value' => $value,
        ];
    }
}