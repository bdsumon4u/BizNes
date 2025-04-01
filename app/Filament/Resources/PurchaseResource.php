<?php

namespace App\Filament\Resources;

use App\Enum\LocationType;
use App\Filament\Resources\PurchaseResource\Pages;
use App\Forms\Components\ProductSelect;
use App\Forms\Components\TextInputSelect;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Number;
use LaraZeus\Quantity\Components\Quantity;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $navigationIcon = 'untitledui-shopping-bag-03';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('location_id')
                    ->relationship('location', 'name', modifyQueryUsing: function ($query) {
                        return $query->where('business_id', Filament::getTenant()->getKey())
                            ->where('type', '!=', LocationType::BRANCH);
                    })
                    ->searchable()
                    ->required()
                    ->default(function () {
                        if (tap(Filament::auth()->user())->isOwner(Filament::getTenant())) {
                            return LocationResource::getEloquentQuery()->where('is_main', true)->value('id');
                        }

                        return value(
                            fn (User $user) => $user->locations()
                                ->where('business_id', Filament::getTenant()->getKey())
                                ->where('type', '!=', LocationType::BRANCH)
                                ->orderByDesc('is_main')
                                ->value('id'),
                            Filament::auth()->user()
                        );
                    }),
                Forms\Components\Select::make('supplier_id')
                    ->relationship('supplier', 'name', modifyQueryUsing: function ($query) {
                        return $query->where('business_id', Filament::getTenant()->getKey());
                    })
                    ->searchable()
                    ->required()
                    ->createOptionForm(fn (Form $form) => SupplierResource::form($form))
                    ->createOptionAction(fn (Action $action) => $action->slideOver()->modalWidth('md')),
                Forms\Components\DatePicker::make('date')
                    ->default(now())
                    ->required()
                    ->native(false),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('supplier.name')->sortable(),
                Tables\Columns\TextColumn::make('amount')->sortable(),
                Tables\Columns\TextColumn::make('date')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            PurchaseResource\RelationManagers\PurchaseItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
            'edit' => Pages\EditPurchase::route('/{record}/edit'),
        ];
    }
}
