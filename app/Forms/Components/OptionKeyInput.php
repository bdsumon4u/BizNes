<?php

namespace App\Forms\Components;

use App\Enum\FieldType;
use Filament\Forms\Components;
use Filament\Support\Components\Component;

final class OptionKeyInput
{
    public static function make(string $key, FieldType $type): Component
    {
        return match ($type) {
            FieldType::ColorPicker => Components\ColorPicker::make($key)
                ->default('#1E3A8A'),
            default => Components\TextInput::make($key)
        };
    }
}
