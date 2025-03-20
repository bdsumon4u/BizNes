<?php

namespace App\Filament\Resources\VariantResource\Pages;

use App\Filament\Resources\ProductResource\Pages\ManagePricing as ManageProductPricing;
use App\Filament\Resources\VariantResource;
use Livewire\Attributes\Lazy;

#[Lazy()]
class ManagePricing extends ManageProductPricing
{
    protected static string $resource = VariantResource::class;
}
