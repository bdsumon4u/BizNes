<?php

namespace App\Filament\Resources\PurchaseResource\Pages;

use App\Filament\Resources\PurchaseResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreatePurchase extends CreateRecord
{
    protected static string $resource = PurchaseResource::class;

    protected static string $view = 'filament.pages.purchases.create';

    public function getHeading(): string
    {
        return '';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return '';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
