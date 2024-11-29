<?php

namespace App\Modules\Invoice\DTOs;

use App\Modules\Invoice\Enums\InvoiceStatus;
use App\Modules\Invoice\Enums\InvoiceType;
use Carbon\Carbon;

class InvoiceDTO
{
    public function __construct(
        public int $userId,
        public int $planId,
        public InvoiceType $type,
        public InvoiceStatus $status,
        public float $amount,
        public int $durationDays,
        public ?string $reference = null,
        public ?Carbon $verifiedAt = null,
        public ?string $id = null,
    ) {
    }
}
