<?php

namespace App\Modules\Subscription\Policies;

use App\Modules\Subscription\Models\Section;
use App\Modules\User\Models\User;
use Illuminate\Auth\Access\Response;

class SectionPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user): Response
    {
        return $user->plan_id && now()->lte($user->plan_expires_at)
            ? Response::allow()
            : Response::deny(__('subscription::messages.not_subscribed'));
    }

    public function view(User $user, Section $section): Response
    {
        return $user->plan_id && now()->lte($user->plan_expires_at) && $user->plan->level >= $section->required_level
            ? Response::allow()
            : Response::deny(__('subscription::messages.unauthorized_access'));
    }
}
