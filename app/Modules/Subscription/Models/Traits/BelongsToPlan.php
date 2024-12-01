<?php

namespace App\Modules\Subscription\Models\Traits;

use App\Modules\Subscription\Models\Plan;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToPlan
{
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
