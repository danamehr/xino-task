<?php

namespace App\Modules\Shared\Traits;

trait EnumTrait
{
    public static function toArray(): array
    {
        return array_map(function ($value) {
            return is_object($value) ? $value->value : $value;
        }, (new \ReflectionClass(static::class))->getConstants());
    }

    public static function getValues(): array
    {
        return array_values(static::toArray());
    }

    public static function toFlippedSelectArrayWithKeys(): array
    {
        return array_flip(self::toArray());
    }
}
