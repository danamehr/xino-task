<?php

namespace App\Modules\Invoice\Enums;

use App\Modules\Shared\Traits\EnumTrait;

enum InvoiceType: int
{
    use EnumTrait;

    case Initial = 1;

    case Renewal = 2;
}
