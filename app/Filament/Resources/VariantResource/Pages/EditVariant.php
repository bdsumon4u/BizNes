<?php

namespace App\Filament\Resources\VariantResource\Pages;

use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\VariantResource;
use App\Models\Variant;
use Filament\Actions;
use Illuminate\Database\Eloquent\Model;

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
        // $resource = static::getResource();
        $product = $this->getRecord()->product;

        $breadcrumbs = [
            parent::$resource::getUrl() => parent::$resource::getBreadcrumb(),
            // $resource::getUrl() => $resource::getBreadcrumb(),
            parent::$resource::getUrl('edit', ['record' => $product, 'tab' => 'variants']) => $product->name,
            $this->getRecord()->name,
        ];

        // if (filled($cluster = static::getCluster())) {
        //     return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        // }

        return $breadcrumbs;
    }

    protected function resolveRecord(int|string $key): Model
    {
        return Variant::findOrFail($key);
    }
}
