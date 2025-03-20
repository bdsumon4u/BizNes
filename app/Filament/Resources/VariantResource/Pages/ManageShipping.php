<?php

namespace App\Filament\Resources\VariantResource\Pages;

use App\Filament\Resources\ProductResource\Pages\ManageShipping as ManageProductShipping;
use App\Filament\Resources\VariantResource;

class ManageShipping extends ManageProductShipping
{
    protected static string $resource = VariantResource::class;
}
