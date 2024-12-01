<?php

namespace App\Modules\Payment\Http\V1\Controllers;

use App\Modules\Payment\Http\V1\Requests\RenewalWebhookRequest;
use App\Modules\Shared\Http\V1\Controllers\Controller;
use App\Modules\Subscription\Models\Plan;
use App\Modules\Subscription\Services\PlanServiceInterface;
use App\Modules\User\Models\User;

class PaymentController extends Controller
{
    public function __construct(protected PlanServiceInterface $planService) {}

    public function renewal(RenewalWebhookRequest $request)
    {
        $user = User::query()->find($request->user_id);
        $plan = Plan::query()->find($request->plan_id);

        $this->authorizeForUser($user, 'renewPlan', $plan);

        try {
            $this->planService->renew($user, $plan);

            return response(['message' => __('subscription::messages.successful_renewal')]);
        } catch (\App\Modules\Payment\Exceptions\InvalidPaymentException $invalidPaymentException) {
            // The status code could be different based on the failure reason.
            return response(['message' => __('payment::messages.invalid_payment')], status: 400);
        }
    }
}
