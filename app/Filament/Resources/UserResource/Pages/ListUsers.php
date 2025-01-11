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
                ->modalDescription('If there is a user with the same email address, the existing user will be used instead of creating a new one. In this case, the existing user will be assigned to the current business. And the name, password, etc. will NOT be updated.')
                ->slideOver()
                ->modalWidth('md'),
        ];
    }
}
