<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PointOfSale extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static string $view = 'filament.pages.point-of-sale';

    protected static bool $shouldRegisterNavigation = false;
}
