<?php

namespace App\Modules\Payment\Services;

use App\Modules\Invoice\DTOs\InvoiceDTO;
use App\Modules\Payment\DTOs\PaymentVerificationDTO;
use App\Modules\Payment\Enums\PaymentStatus;

class PaymentService implements PaymentServiceInterface
{
    public function verifyPayment(InvoiceDTO $invoiceDTO): PaymentVerificationDTO
    {
        // Checking with the PSP happens here.
        return new PaymentVerificationDTO(
            PaymentStatus::Successful,
            str()->uuid(),
        );
    }
}
