<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalDescription(__('If a user with the same email exists, they will be assigned to the current business without updating their name, password, or other details.'))
                ->slideOver()
                ->modalWidth('md'),
        ];
    }
}
