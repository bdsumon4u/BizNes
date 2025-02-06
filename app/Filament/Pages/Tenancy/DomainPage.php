<?php

namespace App\Filament\Pages\Tenancy;

use App\Filament\Clusters\Tenancy\BusinessSettings;
use Filament\Pages\Page;

class DomainPage extends Page
{
    protected static ?string $cluster = BusinessSettings::class;

    protected static ?string $navigationLabel = 'Domain';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'ri-global-line';

    protected static string $view = 'filament.pages.tenancy.domain-page';
}
