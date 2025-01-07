<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Pages\Tenancy\EditTenantProfile;

class EditBusinessProfile extends EditTenantProfile
{
    use BusinessForm;

    public static function getLabel(): string
    {
        return 'Business Profile';
    }
}
