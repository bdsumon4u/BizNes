<?php

namespace App\Filament\Resources\VariantResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Filament\Resources\VariantResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewVariant extends ViewRecord
{
    protected static string $resource = VariantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
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
            ProductResource::getUrl('edit', ['record' => $product, 'tab' => 'variants']) => $product->name,
            // $resource::getUrl() => $resource::getBreadcrumb(),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    public function getBreadcrumb(): string
    {
        return $this->getRecord()->name;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('name'),
                Infolists\Components\TextEntry::make('email'),
                Infolists\Components\TextEntry::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
