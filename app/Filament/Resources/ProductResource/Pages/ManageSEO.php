<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Forms\Components\SEOField;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class ManageSEO extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected static string $view = 'filament.resources.product-resource.pages.manage-seo';

    public function getHeading(): string
    {
        return 'Search Engine Optimization';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Improve your ranking and how your product page will appear in search engines results.';
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
                Forms\Components\TextInput::make('meta_title')
                    ->label(__('Meta Title'))
                    ->maxLength(255)
                    ->extraAlpineAttributes(['x-model' => 'meta_title']),
                Forms\Components\Textarea::make('meta_description')
                    ->label(__('Meta Description'))
                    ->maxLength(500)
                    ->debounce(),
                Forms\Components\TextInput::make('meta_keywords')
                    ->label(__('Meta Keywords'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('og_title')
                    ->label(__('OG Title'))
                    ->maxLength(255),
                Forms\Components\Textarea::make('og_description')
                    ->label(__('OG Description'))
                    ->maxLength(500),
                Forms\Components\KeyValue::make('metadata')
                    ->label(__('Metadata'))
                    ->reorderable(),
            ])
            ->columns(1);
    }
}
