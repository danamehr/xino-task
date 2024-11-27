<?php

namespace App\Modules\Invoice\Enums;

use App\Modules\Shared\Traits\EnumTrait;

enum InvoiceStatus: int
{
    use EnumTrait;

    case Paid = 1;

    case UnPaid = 2;
}
