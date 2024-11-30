<?php

namespace App\Modules\Subscription\Services;

use App\Modules\Invoice\Enums\InvoiceStatus;
use App\Modules\Invoice\Enums\InvoiceType;
use App\Modules\Invoice\Services\InvoiceServiceInterface;
use App\Modules\Payment\Enums\PaymentStatus;
use App\Modules\Payment\Exceptions\InvalidPaymentException;
use App\Modules\Payment\Exceptions\UnsuccessfulPaymentException;
use App\Modules\Payment\Services\PaymentServiceInterface;
use App\Modules\Subscription\Events\SubscriptionActivatedEvent;
use App\Modules\Subscription\Events\SubscriptionRenewedEvent;
use App\Modules\Subscription\Models\Plan;
use App\Modules\User\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PlanService implements PlanServiceInterface
{
    public function __construct(
        private PaymentServiceInterface $paymentService,
        private InvoiceServiceInterface $invoiceService,
    ) {
    }

    public function getPlans(): Collection
    {
        // Like sections, plans could also be cached or paginated, but we keep it simple.
        return Plan::all();
    }

    public function subscribe(User $user, Plan $plan): User
    {
        $invoiceDto = $this->invoiceService->makeInvoice($user, $plan, InvoiceType::Initial);

        // In a real world scenario, the payment process is more than one step. But in this case we
        // process all steps at once to keep it short and simple.
        $paymentVerificationDto = $this->paymentService->verifyPayment($invoiceDto);

        if ($paymentVerificationDto->status == PaymentStatus::Unsuccessful) {
            // We can also handle multiple exception types based on the PSP response code and message.
            throw new UnsuccessfulPaymentException();
        }

        $invoiceDto->verifiedAt = now();
        $invoiceDto->reference = $paymentVerificationDto->transactionId;
        $invoiceDto->status = InvoiceStatus::Paid;

        DB::transaction(function () use ($invoiceDto) {
            $this->invoiceService->createInvoice($invoiceDto);

            $user = User::query()
                ->where('id', $invoiceDto->userId)
                ->lockForUpdate()
                ->first();

            $user->update([
                'plan_id' => $invoiceDto->planId,
                'plan_expires_at' => now()->addDays($invoiceDto->durationDays),
            ]);
        });

        // We can listen for the event to send a notification to the user.
        $user->refresh();
        event(new SubscriptionActivatedEvent($user));

        return $user;
    }

    public function renew(User $user, Plan $plan): true
    {
        $invoiceDto = $this->invoiceService->makeInvoice($user, $plan, InvoiceType::Renewal);

        $paymentVerificationDto = $this->paymentService->verifyPayment($invoiceDto);

        if ($paymentVerificationDto->status == PaymentStatus::Unsuccessful) {
            // We can also handle multiple exception types based on the PSP response code and message.
            throw new InvalidPaymentException();
        }

        $invoiceDto->verifiedAt = now();
        $invoiceDto->reference = $paymentVerificationDto->transactionId;
        $invoiceDto->status = InvoiceStatus::Paid;

        DB::transaction(function () use ($invoiceDto) {
            $this->invoiceService->createInvoice($invoiceDto);

            $user = User::query()
                ->where('id', $invoiceDto->userId)
                ->lockForUpdate()
                ->first();

            $user->update([
                'plan_expires_at' => now()->lt($user->plan_expires_at)
                    ? $user->plan_expires_at->addDays($invoiceDto->durationDays)
                    : now()->addDays($invoiceDto->durationDays),
            ]);
        });

        // We can listen for the event to send a notification to the user.
        $user->refresh();
        event(new SubscriptionRenewedEvent($user));

        return true;
    }
}
