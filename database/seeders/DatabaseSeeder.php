<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Modules\Subscription\Enums\PlanStatus;
use App\Modules\Subscription\Models\Plan;
use App\Modules\Subscription\Models\Section;
use App\Modules\User\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()
            ->create([
                'email' => 'firstuser@gmail.com',
            ]);

        Plan::factory()
            ->create([
                'name' => 'basic',
                'level' => 1,
                'status' => PlanStatus::Active->value,
            ]);

        Plan::factory()
            ->create([
                'name' => 'professional',
                'level' => 2,
                'status' => PlanStatus::Active->value,
            ]);

        Plan::factory()
            ->create([
                'name' => 'premium',
                'level' => 3,
                'status' => PlanStatus::Active->value,
            ]);

        Section::factory()
            ->count(3)
            ->create(['required_level' => 1]);

        Section::factory()
            ->count(3)
            ->create(['required_level' => 2]);

        Section::factory()
            ->count(3)
            ->create(['required_level' => 3]);
    }
}
