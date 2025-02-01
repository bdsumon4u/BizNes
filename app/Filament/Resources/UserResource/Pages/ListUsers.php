<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
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
                ->modalWidth('md')
                ->using(function (Actions\CreateAction $action, array $data, HasActions $livewire) {
                    $model = $action->getModel();

                    if ($data['is_user_exists'] ?? false) {
                        $record = $model::query()->where('email', $data['email'])->firstOrFail();
                        $record->businesses()->syncWithoutDetaching(Filament::getTenant());

                        return $record;
                    }

                    if ($translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver()) {
                        $record = $translatableContentDriver->makeRecord($model, $data);
                    } else {
                        $record = new $model;
                        $record->fill($data);
                    }

                    if ($relationship = $action->getRelationship()) {
                        /** @phpstan-ignore-next-line */
                        $relationship->save($record);

                        return $record;
                    }

                    $record->save();

                    return $record;
                }),
        ];
    }
}
