<?php

namespace App\Modules\Subscription\Services;

use App\Modules\Subscription\Models\Plan;
use App\Modules\User\Models\User;
use Illuminate\Support\Collection;

interface PlanServiceInterface
{
    public function getPlans(): Collection;

    public function subscribe(User $user, Plan $plan): User;

    public function renew(User $user, Plan $plan): true;
}
