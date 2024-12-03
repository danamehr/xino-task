<?php

namespace App\Modules\Subscription\Models;

use App\Modules\Subscription\Database\Factories\SectionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    protected static function newFactory()
    {
        return SectionFactory::new();
    }
}
