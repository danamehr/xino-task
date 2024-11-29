<?php

namespace App\Modules\Invoice\Services;

use App\Modules\Invoice\DTOs\InvoiceDTO;
use App\Modules\Invoice\Enums\InvoiceStatus;
use App\Modules\Invoice\Enums\InvoiceType;
use App\Modules\Invoice\Models\Invoice;
use App\Modules\Subscription\Models\Plan;
use App\Modules\User\Models\User;
use Illuminate\Support\Collection;

class InvoiceService implements InvoiceServiceInterface
{
    public function makeInvoice(User $user, Plan $plan, InvoiceType $invoiceType): InvoiceDTO
    {
        return new InvoiceDTO(
            $user->id,
            $plan->id,
            $invoiceType,
            InvoiceStatus::UnPaid,
            $plan->price,
            $plan->duration_days,
        );
    }

    public function createInvoice(InvoiceDTO $invoiceDTO): Invoice
    {
        return Invoice::query()
            ->create([
                'reference' => $invoiceDTO->reference,
                'user_id' => $invoiceDTO->userId,
                'plan_id' => $invoiceDTO->planId,
                'type' => $invoiceDTO->type->value,
                'status' => $invoiceDTO->status->value,
                'amount' => $invoiceDTO->amount,
                'duration_days' => $invoiceDTO->durationDays,
                'verified_at' => $invoiceDTO->verifiedAt,
            ]);
    }

    public function getInvoices(User $user): Collection
    {
        // We can also use caching or pagination here.
        return Invoice::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->with('plan')
            ->get();
    }
}
