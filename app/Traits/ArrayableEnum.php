<?php

namespace App\Traits;

trait ArrayableEnum
{
    public static function names(): array
    {
        return array_column(static::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(static::cases(), 'value');
    }

    public static function toArray(): array
    {
        return array_combine(static::values(), static::names());
    }

    public static function options(): array
    {
        if (method_exists(static::class, 'getLabel')) {
            return collect(static::cases())
                ->mapWithKeys(fn ($enum) => [
                    $enum->value => $enum->getLabel(),
                ])
                ->toArray();
        }

        return [];
    }
}
