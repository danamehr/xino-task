<?php

namespace App\Modules\Subscription\Database\Factories;

use App\Modules\Subscription\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Subscription\Models\Section>
 */
class SectionFactory extends Factory
{
    protected $model = Section::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->words(2, true),
            'slug' => $this->faker->unique()->slug(3),
            'required_level' => rand(1, 3),
            'description' => null,
        ];
    }
}
