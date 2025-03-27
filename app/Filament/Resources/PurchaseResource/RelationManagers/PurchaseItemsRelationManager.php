<?php

namespace App\Filament\Resources\PurchaseResource\RelationManagers;

use App\Models\Product;
use App\Models\Variant;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class PurchaseItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Select::make('purchasable_type')
                    ->options([
                        Product::class => 'Product',
                        Variant::class => 'Variant',
                    ])
                    ->reactive()
                    ->required(),

                Select::make('purchasable_id')
                    ->label('Product / Variant')
                    ->options(fn ($get) => match ($get('purchasable_type')) {
                        Product::class => Product::pluck('name', 'id'),
                        Variant::class => Variant::pluck('name', 'id'),
                        default => [],
                    })
                    ->searchable()
                    ->required(),

                TextInput::make('price')
                    ->numeric()
                    ->required(),

                TextInput::make('quantity')
                    ->numeric()
                    ->required(),

                DatePicker::make('expiry_date')
                    ->nullable(),
            ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('purchasable.name')
                    ->label('Product / Variant')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('price')
                    ->sortable(),

                TextColumn::make('quantity')
                    ->sortable(),

                TextColumn::make('expiry_date')
                    ->sortable(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
