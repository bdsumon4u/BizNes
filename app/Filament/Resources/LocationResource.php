<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Tenancy\BusinessSettings;
use App\Filament\Resources\LocationResource\Pages;
use App\Filament\Resources\LocationResource\RelationManagers\UsersRelationManager;
use App\LocationType;
use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

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
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->options(LocationType::class)
                    ->default(LocationType::HYBRID)
                    ->required()
                    ->native(false)
                    ->lazy(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255)
                    ->required(),
                PhoneInput::make('phone')
                    ->required()
                    ->disallowDropdown()
                    ->defaultCountry('BD')
                    ->initialCountry('BD')
                    ->unique(ignoreRecord: true),
                Forms\Components\Textarea::make('description')
                    ->minLength(50)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('street')
                    ->minLength(10)
                    ->maxLength(255)
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('district')
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('city')
                    ->maxLength(255)
                    ->required(),
                Forms\Components\Checkbox::make('is_main')
                    ->label('Main Location')
                    ->helperText('This location will be used as the main location for the business.')
                    ->columnSpanFull()
                    ->visible(fn (Get $get): bool => $get('type') === LocationType::HYBRID->value),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->icon(fn (Location $location) => $location->is_main ? 'ri-pushpin-line' : null)
                    ->iconPosition(IconPosition::After)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('users_count')
                    ->label(__('Users'))
                    ->counts('users')
                    ->sortable()
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                    // ->slideOver()
                    // ->modalWidth('md')
                    // ->using(fn (array $data, Location $location) => DB::transaction(function () use ($data, $location) {
                    //     if ($data['is_main'] ?? false) {
                    //         static::getEloquentQuery()->update(['is_main' => false]);
                    //     }

                    //     $location->update($data);
                    // })),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocations::route('/'),
            // 'create' => Pages\CreateLocation::route('/create'),
            'edit' => Pages\EditLocation::route('/{record}/edit'),
        ];
    }
}
