<?php

namespace App\Filament\Resources\CustomerGroupResource\Pages;

use App\Filament\Resources\CustomerGroupResource;
use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomerGroups extends ListRecords
{
    protected static string $resource = CustomerGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->slideOver()
                ->modalWidth('md'),
        ];
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();

        $breadcrumbs = [
            CustomerResource::getUrl() => CustomerResource::getNavigationLabel(),
            $resource::getUrl() => $resource::getBreadcrumb(),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }
}
