<?php

namespace App\Modules\Subscription\Enums;

use App\Modules\Shared\Traits\EnumTrait;

enum PlanStatus: int
{
    use EnumTrait;

    case Active = 1;

    case InActive = 2;
}
