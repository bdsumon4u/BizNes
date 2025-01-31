<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;
use Filament\Forms;
use Filament\Forms\Set;

class SEOField extends Field
{
    protected string $view = 'forms.components.seo-field';

    public static function make(string $name = 'seo'): static
    {
        return parent::make($name)
            ->label('')
            ->schema([
                Forms\Components\Section::make('SEO Settings')
                    ->collapsed()
                    ->compact()
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label(__('Meta Title'))
                            ->maxLength(255),
                        Forms\Components\Textarea::make('meta_description')
                            ->label(__('Meta Description'))
                            ->maxLength(500),
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
                    ]),
            ])
            ->columnSpanFull();
    }
}
