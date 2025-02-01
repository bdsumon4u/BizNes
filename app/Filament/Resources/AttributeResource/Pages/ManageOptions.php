<?php

namespace App\Filament\Resources\AttributeResource\Pages;

use App\Enum\FieldType;
use App\Filament\Resources\AttributeResource;
use App\Forms\Components\OptionKeyInput;
use App\Models\Option;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class ManageOptions extends ManageRelatedRecords
{
    protected static string $resource = AttributeResource::class;

    protected static string $relationship = 'options';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Options';
    }

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
                Forms\Components\TextInput::make('value')
                    ->label(__('forms.label.value'))
                    ->placeholder('My value')
                    ->maxLength(75)
                    ->required()
                    ->live(true, condition: fn (): bool => $this->getOwnerRecord()->type !== FieldType::ColorPicker)
                    ->afterStateUpdated(function ($state, Forms\Set $set): void {
                        $set('key', Str::slug($state, language: config('app.locale', 'en')));
                    }),
                OptionKeyInput::make('key', $this->getOwnerRecord()->type)
                    ->label(__('forms.label.key'))
                    ->helperText(__('The key will be used for the values in storage for the forms (option, radio, etc.). Must be in slug format'))
                    ->required()
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn ($rule) => $rule->where('attribute_id', $this->getOwnerRecord()->getKey())),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('value')
            ->columns([
                Tables\Columns\TextColumn::make('value')
                    ->label(__('forms.label.value'))
                    ->formatStateUsing(function (Option $record): View|string {
                        if ($this->getOwnerRecord()->type !== FieldType::ColorPicker) {
                            return $record->value;
                        }

                        return view('filament.attribute-color-badge', [
                            'value' => $record->value,
                            'key' => $record->key,
                        ]);
                    })
                    ->badge(fn (): bool => $this->getOwnerRecord()->type !== FieldType::ColorPicker),
                Tables\Columns\TextColumn::make('key')
                    ->label(__('forms.label.key')),
            ])
            ->reorderable('position')
            ->defaultSort('position')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->slideOver()
                    ->modalWidth('md'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver()
                    ->modalWidth('md'),
            ]);
    }
}
