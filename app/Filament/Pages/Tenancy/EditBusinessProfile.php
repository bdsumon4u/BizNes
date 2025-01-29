<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Support\Enums\MaxWidth;

class EditBusinessProfile extends EditTenantProfile
{
    use BusinessForm;

    protected ?string $maxContentWidth = MaxWidth::ThreeExtraLarge->value;

    public static function getLabel(): string
    {
        return 'Business Profile';
    }
}
