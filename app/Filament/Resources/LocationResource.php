<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Tenancy\BusinessSettings;
use App\Filament\Resources\LocationResource\Pages;
use App\Models\Location;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LocationResource extends Resource
{
    protected static ?string $cluster = BusinessSettings::class;

    protected static ?string $model = Location::class;

    protected static ?string $navigationIcon = 'ri-map-pin-line';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver()
                    ->modalWidth('md'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocations::route('/'),
            // 'create' => Pages\CreateLocation::route('/create'),
            // 'edit' => Pages\EditLocation::route('/{record}/edit'),
        ];
    }
}
