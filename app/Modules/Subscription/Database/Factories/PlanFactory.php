<?php

namespace App\Modules\Subscription\Database\Factories;

use App\Modules\Subscription\Enums\PlanStatus;
use App\Modules\Subscription\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Subscription\Models\Plan>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'price' => $this->faker->randomFloat(2, 10, 99),
            'level' => rand(1, 3),
            'status' => array_rand(PlanStatus::getValues()),
            'duration_days' => 30,
        ];
    }
}
