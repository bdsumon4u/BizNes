<?php

namespace App;

use Filament\Support\Contracts\HasLabel;

enum LocationType: string implements HasLabel
{
    case BRANCH = 'branch';
    case WHOUSE = 'whouse';
    case HYBRID = 'hybrid';

    public function getLabel(): string
    {
        return match ($this) {
            self::BRANCH => 'Store Branch',
            self::WHOUSE => 'Warehouse',
            self::HYBRID => 'Hybrid',
        };
    }
}
