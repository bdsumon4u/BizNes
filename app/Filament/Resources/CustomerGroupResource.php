<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerGroupResource\Pages;
use App\Models\CustomerGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CustomerGroupResource extends Resource
{
    protected static ?string $breadcrumb = 'Groups';

    protected static ?string $model = CustomerGroup::class;

    protected static ?string $navigationGroup = 'Customer';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationParentItem = 'Customers';

    protected static ?string $navigationLabel = 'Groups';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->placeholder(__('Name of the customer group'))
                    ->required(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->reorderable('position')
            ->defaultSort(fn ($query) => $query->orderByRaw('business_id IS NOT NULL, position'))
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver()
                    ->modalWidth('xl'),
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
            'index' => Pages\ListCustomerGroups::route('/'),
            // 'create' => Pages\CreateCustomerGroup::route('/create'),
            // 'edit' => Pages\EditCustomerGroup::route('/{record}/edit'),
        ];
    }

    public static function scopeEloquentQueryToTenant(Builder $query, ?Model $tenant): Builder
    {
        return parent::scopeEloquentQueryToTenant($query, $tenant)->orWhereNull('business_id');
    }
}
