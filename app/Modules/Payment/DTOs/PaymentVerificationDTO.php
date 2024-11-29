<?php

namespace App\Modules\Payment\DTOs;

use App\Modules\Payment\Enums\PaymentStatus;

class PaymentVerificationDTO
{
    public function __construct(
        public PaymentStatus $status,
        public ?string $transactionId = null,
        public ?string $message = null,
    ) {
    }
}
