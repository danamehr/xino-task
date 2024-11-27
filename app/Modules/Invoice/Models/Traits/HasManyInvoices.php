<?php

namespace App\Modules\Invoice\Models\Traits;

use App\Modules\Invoice\Models\Invoice;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasManyInvoices
{
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
