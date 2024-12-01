<?php

namespace App\Modules\Invoice\Services;

use App\Modules\Invoice\DTOs\InvoiceDTO;
use App\Modules\Invoice\Enums\InvoiceType;
use App\Modules\Invoice\Models\Invoice;
use App\Modules\Subscription\Models\Plan;
use App\Modules\User\Models\User;
use Illuminate\Support\Collection;

interface InvoiceServiceInterface
{
    public function makeInvoice(User $user, Plan $plan, InvoiceType $invoiceType): InvoiceDTO;

    public function createInvoice(InvoiceDTO $invoiceDTO): Invoice;

    public function getInvoices(User $user): Collection;
}
