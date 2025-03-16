<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Enum\Dimension\Length;
use App\Enum\Dimension\Volume;
use App\Enum\Dimension\Weight;
use App\Filament\Resources\ProductResource;
use App\Forms\Components\TextInputSelect;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class ManageShipping extends EditRecord
{
    protected static string $resource = ProductResource::class;

    public function getHeading(): string
    {
        return '';
    }

    public function getSubheading(): string|Htmlable|null
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
                Forms\Components\Section::make(__('Package dimension'))
                    ->description(__('Charge additional shipping costs based on packet dimensions covered here.'))
                    ->aside()
                    ->compact()
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                TextInputSelect::make('height_value')
                                    ->label(__('Height'))
                                    ->numeric()
                                    ->select(
                                        fn () => Forms\Components\Select::make('height_unit')
                                            ->selectablePlaceholder(false)
                                            ->native(false)
                                            ->options(Length::toArray())
                                            ->default(Length::CM)
                                    ),
                                TextInputSelect::make('width_value')
                                    ->label(__('Width'))
                                    ->numeric()
                                    ->select(
                                        fn () => Forms\Components\Select::make('width_unit')
                                            ->selectablePlaceholder(false)
                                            ->native(false)
                                            ->options(Length::toArray())
                                            ->default(Length::CM)
                                    ),
                                TextInputSelect::make('depth_value')
                                    ->label(__('Depth'))
                                    ->numeric()
                                    ->select(
                                        fn () => Forms\Components\Select::make('depth_unit')
                                            ->selectablePlaceholder(false)
                                            ->native(false)
                                            ->options(Length::toArray())
                                            ->default(Length::CM)
                                    ),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                TextInputSelect::make('weight_value')
                                    ->label(__('Weight'))
                                    ->numeric()
                                    ->select(
                                        fn () => Forms\Components\Select::make('weight_unit')
                                            ->selectablePlaceholder(false)
                                            ->native(false)
                                            ->options(Weight::toArray())
                                            ->default(Weight::KG)
                                    ),
                                TextInputSelect::make('volume_value')
                                    ->label(__('Volume'))
                                    ->numeric()
                                    ->select(
                                        fn () => Forms\Components\Select::make('volume_unit')
                                            ->selectablePlaceholder(false)
                                            ->native(false)
                                            ->options(Volume::toArray())
                                            ->default(Volume::ML)
                                    ),
                            ]),
                    ]),
            ]);
    }
}
