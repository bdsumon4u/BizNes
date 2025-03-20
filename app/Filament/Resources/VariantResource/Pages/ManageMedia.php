<?php

namespace App\Filament\Resources\VariantResource\Pages;

use App\Filament\Resources\ProductResource\Pages\ManageMedia as ManageProductMedia;
use App\Filament\Resources\VariantResource;

class ManageMedia extends ManageProductMedia
{
    protected static string $resource = VariantResource::class;
}
