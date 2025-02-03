<?php

namespace App\Traits;

use App\Exceptions\UndefinedEnumCaseError;
use BackedEnum;

/**
 * @mixin BackedEnum
 */
trait HasEnumStaticMethods
{
    public function __invoke(): int|string
    {
        return $this->value;
    }

    public static function __callStatic(string $name, mixed $args): bool|int|string
    {
        $case = collect(static::cases())->firstWhere('name', $name);

        if (! $case) {
            throw new UndefinedEnumCaseError(
                enum: static::class,
                case: $name,
            );
        }

        if (array_key_exists('case', $args)) {
            return $case === $args['case'];
        }

        if (array_key_exists('name', $args)) {
            return $case->name === $args['name'];
        }

        if (array_key_exists('value', $args)) {
            return $case->value === $args['value'];
        }

        return $case->value;
    }
}
