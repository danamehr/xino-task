<?php

namespace App\Modules\Payment\Enums;

use App\Modules\Shared\Traits\EnumTrait;

enum PaymentStatus: int
{
    use EnumTrait;

    case Successful = 1;

    case Unsuccessful = 2;
}
