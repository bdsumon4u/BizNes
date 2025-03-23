<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Unique;
use Livewire\Attributes\Lazy;

#[Lazy()]
class ManagePricing extends ManageRelatedRecords
{
    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'prices';

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
        return $form
            ->schema([
                Forms\Components\Select::make('customer_group_id')
                    ->relationship('customerGroup', 'name')
                    ->preload()
                    ->required()
                    ->native(false),
                Forms\Components\TextInput::make('quantity')
                    ->placeholder(__('Minimum quantity'))
                    ->integer()
                    ->minValue(1)
                    ->unique('prices', modifyRuleUsing: function (Unique $rule, Forms\Get $get) {
                        $rule->whereIn('customer_group_id', Arr::wrap($get('customer_group_id')))
                            ->where('priceable_type', $this->getRecord()::class)
                            ->where('priceable_id', $this->getRecord()->getKey());
                    }, ignoreRecord: true)
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->integer()
                    ->minValue(0)
                    ->required(),
                Forms\Components\TextInput::make('compare_amount')
                    ->integer()
                    ->minValue(0)
                    ->required(),
                Forms\Components\TextInput::make('cost_amount')
                    ->integer()
                    ->minValue(0)
                    ->required(),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('customerGroup'))
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('customerGroup.name'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('amount')
                    ->money(),
                Tables\Columns\TextColumn::make('compare_amount')
                    ->money(),
                Tables\Columns\TextColumn::make('cost_amount')
                    ->money(),
            ])
            ->defaultGroup(
                Group::make('customerGroup.name')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false)
                    ->orderQueryUsing(function (Builder $query, string $direction) {
                        $query->leftJoinRelationship('customerGroup')
                            ->orderByRaw('business_id IS NOT NULL, position asc, quantity desc');
                    }),
            )
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Add price'))
                    ->slideOver()
                    ->modalWidth('md'),
            ])
            ->actions([
                Tables\Actions\ReplicateAction::make()
                    ->form(fn (Form $form) => $this->form($form))
                    ->slideOver()
                    ->modalWidth('md'),
                Tables\Actions\EditAction::make()
                    ->slideOver()
                    ->modalWidth('md'),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
