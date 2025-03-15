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
            // ->defaultSort(fn ($query) => $query->orderByRaw('position, quantity'))
            // ->defaultGroup(
            //     Group::make('name')
            //         ->collapsible()
            //         ->titlePrefixedWithLabel(false)
            //         // ->orderQueryUsing(fn (Builder $query, string $direction) => $query->orderByRaw('business_id IS NOT NULL, position ' . $direction))
            //     )
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label(__('Add price'))
                    ->preloadRecordSelect()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()->autofocus(),
                        Forms\Components\TextInput::make('quantity')
                            ->placeholder(__('Minimum quantity'))
                            ->integer()
                            ->minValue(1)
                            ->unique('product_prices', modifyRuleUsing: function (Unique $rule, Forms\Get $get) { 
                                $rule->whereIn('customer_group_id', $get('recordId'))
                                    ->where('product_id', $this->getRecord()->getKey());
                            }),
                        Forms\Components\TextInput::make('price')
                            ->placeholder(__('Unit price'))
                            ->integer()
                            ->minValue(0),
                    ])
                    ->multiple()
                    ->slideOver()
                    ->modalWidth('md'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form(fn (Tables\Actions\EditAction $action): array => [
                        // $action->getRecordSelect()->autofocus(),
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
