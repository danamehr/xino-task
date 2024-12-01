<?php

namespace App\Modules\Invoice\Database\Factories;

use App\Modules\Invoice\Enums\InvoiceStatus;
use App\Modules\Invoice\Enums\InvoiceType;
use App\Modules\Invoice\Models\Invoice;
use App\Modules\Subscription\Models\Plan;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Invoice\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => $this->faker->uuid(),
            'user_id' => User::factory(),
            'plan_id' => Plan::factory(),
            'type' => array_rand(InvoiceType::getValues()),
            'status' => array_rand(InvoiceStatus::getValues()),
            'amount' => $this->faker->randomFloat(2, 10, 99),
            'duration_days' => 30,
            'verified_at' => now(),
        ];
    }
}
