<?php

namespace App\Filament\Resources\VariantResource\Pages;

use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\VariantResource;
use App\Models\Product;
use Filament\Actions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EditVariant extends EditProduct
{
    protected static string $resource = VariantResource::class;

    protected static string $view = 'filament.pages.variants.edit';

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        $resource = parent::getResource();
        $product = $this->getRecord()->parent;

        throw_unless($product, (new ModelNotFoundException)->setModel(Product::class));

        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
            $resource::getUrl('edit', ['record' => $product, 'tab' => 'variants']) => $product->name,
            $this->getRecord()->name,
        ];

        return $breadcrumbs;
    }
}
