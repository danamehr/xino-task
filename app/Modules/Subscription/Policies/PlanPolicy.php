<?php

namespace App\Modules\Subscription\Policies;

use App\Modules\Subscription\Enums\PlanStatus;
use App\Modules\Subscription\Models\Plan;
use App\Modules\User\Models\User;
use Illuminate\Auth\Access\Response;

class PlanPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function subscribe(User $user, Plan $plan): Response
    {
        if ($plan->status == PlanStatus::InActive->value) {
            return Response::denyAsNotFound(__('subscription::messages.plan_not_found'));
        }

        // Based on the product's business logic, we can consider different scenarios for users subscription, like letting
        // a user upgrade his current plan with a higher level plan. But for now users can have only one plan and just renew it.
        // No upgrading is available at this time.
        return ! $user->plan_expires_at || now()->gt($user->plan_expires_at)
            ? Response::allow()
            : Response::deny(__('subscription::messages.already_subscribed'));
    }

    public function renewPlan(User $user, Plan $plan): Response
    {
        if ($plan->status == PlanStatus::InActive->value) {
            return Response::denyAsNotFound(__('subscription::messages.plan_not_found'));
        }

        return $user->plan_id === $plan->id && $user->plan_expires_at
            ? Response::allow()
            : Response::deny(__('subscription::messages.not_subscribed_to_the_plan'));
    }
}
