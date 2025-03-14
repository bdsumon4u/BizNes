<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Livewire\Attributes\Lazy;

#[Lazy()]
class ManagePricing extends ManageRelatedRecords
{
    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'prices';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name'),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->allowDuplicates()
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('price'),
            ])
            ->defaultSort('quantity')
            ->defaultGroup('name')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()->autofocus(),
                        Forms\Components\TextInput::make('quantity')
                            ->placeholder(__('Minimum quantity')),
                        Forms\Components\TextInput::make('price')
                            ->placeholder(__('Unit price')),
                    ])
                    ->multiple()
                    ->slideOver()
                    ->modalWidth('md'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form(fn (Tables\Actions\EditAction $action): array => [
                        $action->getRecordSelect()->autofocus(),
                        Forms\Components\TextInput::make('quantity')
                            ->placeholder(__('Minimum quantity')),
                        Forms\Components\TextInput::make('price')
                            ->placeholder(__('Unit price')),
                    ])
                    ->slideOver()
                    ->modalWidth('md'),
            ]);
    }
}
