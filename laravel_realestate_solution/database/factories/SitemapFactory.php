<?php

namespace Database\Factories;

use App\Models\Sitemap;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sitemap>
 */
class SitemapFactory extends Factory
{
    protected $model = Sitemap::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'url' => $this->faker->randomNumber($nbDigits = NULL, $strict = false),
            'description' => $this->faker->text
        ];
    }
}
