<?php

namespace App\Modules\Payment\Services;

use App\Modules\Invoice\DTOs\InvoiceDTO;
use App\Modules\Payment\DTOs\PaymentVerificationDTO;

interface PaymentServiceInterface
{
    public function verifyPayment(InvoiceDTO $invoiceDTO): PaymentVerificationDTO;
}
