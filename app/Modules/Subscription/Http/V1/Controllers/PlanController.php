<?php

namespace App\Modules\Subscription\Http\V1\Controllers;

use App\Modules\Shared\Http\V1\Controller;
use App\Modules\Subscription\Http\V1\Resources\PlanResource;
use App\Modules\Subscription\Models\Plan;
use App\Modules\Subscription\Services\PlanServiceInterface;
use App\Modules\User\Http\V1\Resources\UserResource;

class PlanController extends Controller
{
    public function __construct(protected PlanServiceInterface $planService)
    {
    }

    public function index()
    {
        return PlanResource::collection($this->planService->getPlans());
    }

    public function subscribe(Plan $plan)
    {
        $this->authorize('subscribe', $plan);

        try {
            return UserResource::make($this->planService->subscribe(auth()->user(), $plan));
        } catch (\App\Modules\Payment\Exceptions\UnsuccessfulPaymentException $unsuccessfulPaymentException) {
            // The status code could be different based on the failure reason. Here we assume it's because of the user.
            return response(['message' => __('payment::messages.unsuccessful_payment')], status: 400);
        }
    }
}
