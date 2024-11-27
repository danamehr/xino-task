<?php

namespace App\Modules\Subscription\Models;

use App\Modules\Subscription\Database\Factories\PlanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return PlanFactory::new();
    }
}
