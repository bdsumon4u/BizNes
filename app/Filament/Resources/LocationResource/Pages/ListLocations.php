<?php

namespace App\Filament\Resources\LocationResource\Pages;

use App\Filament\Resources\LocationResource;
use App\LocationType;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListLocations extends ListRecords
{
    protected static string $resource = LocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->slideOver()
                ->modalWidth('md')
                ->using(fn (array $data) => DB::transaction(function () use ($data) {
                    if ($data['is_main'] ?? false) {
                        static::$resource::getEloquentQuery()->update(['is_main' => false]);
                    }

                    return Filament::getTenant()->locations()->create($data);
                }))
                ->visible(fn () => Filament::getTenant()->locations()->count() < 4),
        ];
    }
}
