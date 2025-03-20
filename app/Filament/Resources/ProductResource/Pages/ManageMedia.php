<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class ManageMedia extends EditRecord
{
    protected static string $resource = ProductResource::class;

    public function getHeading(): string
    {
        return '';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function form(Form $form): Form
    {
        return $this->makeForm()
            ->operation('edit')
            ->model($this->getRecord())
            ->statePath($this->getFormStatePath())
            ->columns($this->hasInlineLabels() ? 1 : 2)
            ->inlineLabel($this->hasInlineLabels())
            ->schema([
                Forms\Components\SpatieMediaLibraryFileUpload::make('thumbnail')
                    ->collection('thumbnail')
                    ->directory('products/images')
                    ->helperText(__('Used to represent your product during checkout, social sharing and more.'))
                    ->image()
                    ->panelLayout('grid')
                    ->maxSize(512)
                    ->columnSpan(['lg' => 1]),
                Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                    ->collection('uploads')
                    ->directory('products/images')
                    ->helperText(__('Add additional images to your product.'))
                    ->multiple()
                    ->reorderable()
                    ->panelLayout('grid')
                    ->maxSize(512)
                    ->columnSpan(['lg' => 2]),
            ])
            ->columns(3);
    }
}
