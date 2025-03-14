<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class ManageMedia extends EditRecord
{
    protected static string $resource = ProductResource::class;

    public function form(Form $form): Form
    {
        return $form;
    }
}
