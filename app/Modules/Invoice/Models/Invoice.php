<?php

namespace App\Modules\Invoice\Models;

use App\Modules\Invoice\Database\Factories\InvoiceFactory;
use App\Modules\Subscription\Models\Traits\BelongsToPlan;
use App\Modules\User\Models\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory, HasUlids, BelongsToUser, BelongsToPlan;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [];

    protected function casts()
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    protected static function newFactory()
    {
        return InvoiceFactory::new();
    }
}
