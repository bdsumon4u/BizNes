<?php

namespace App\Filament\Resources;

use App\Enum\FieldType;
use App\Filament\Resources\AttributeResource\Pages;
use App\Filament\Resources\AttributeResource\Pages\ManageOptions;
use App\Forms\Components\IconPicker;
use App\Models\Attribute;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class AttributeResource extends Resource
{
    protected static ?string $model = Attribute::class;

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationParentItem = 'Products';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('forms.label.name'))
                    ->required()
                    ->lazy()
                    ->maxLength(75)
                    ->afterStateUpdated(function ($state, Forms\Set $set): void {
                        $set('slug', Str::slug($state, language: config('app.locale', 'en')));
                    }),

                Forms\Components\TextInput::make('slug')
                    ->label(__('forms.label.slug'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn ($rule) => $rule->where('business_id', Filament::getTenant()->getKey())),

                Forms\Components\Select::make('type')
                    ->label(__('forms.label.type'))
                    ->options(FieldType::class)
                    ->required()
                    ->native(false),

                IconPicker::make('icon')
                    ->label(__('forms.label.icon')),

                Forms\Components\Textarea::make('description')
                    ->label(__('forms.label.description'))
                    ->hint(__('words.characters', ['number' => 100]))
                    ->maxLength(100)
                    ->rows(3),

                Forms\Components\Toggle::make('is_enabled')
                    ->label(__('forms.actions.enable'))
                    ->default(true)
                    ->onColor('success')
                    ->helperText(__('Set attribute visibility for the customers.')),

                Forms\Components\Checkbox::make('is_searchable')
                    ->label(__('forms.label.is_searchable'))
                    ->helperText(__('You can use this attribute to search and filter product.')),

                Forms\Components\Checkbox::make('is_filterable')
                    ->label(__('forms.label.is_filterable'))
                    ->helperText(__('You can use this attribute as a filter on your front store.')),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('icon')
                    ->icon(fn (Attribute $record) => $record->icon)
                    ->label(__('forms.label.icon')),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('forms.label.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('forms.label.type'))
                    ->formatStateUsing(fn (Attribute $record) => $record->type_formatted)
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_enabled')
                    ->label(__('words.is_enabled'))
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_searchable')
                    ->label(__('forms.label.is_searchable'))
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_filterable')
                    ->label(__('forms.label.is_filterable'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('forms.label.updated_at'))
                    ->date(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_enabled')
                    ->label(__('forms.actions.enable')),
                Tables\Filters\TernaryFilter::make('is_searchable')
                    ->label(__('forms.label.is_searchable')),
                Tables\Filters\TernaryFilter::make('is_filterable')
                    ->label(__('forms.label.is_filterable')),
            ])
            ->actions([
                Tables\Actions\Action::make('options')
                    ->color('gray')
                    ->icon('untitledui-dotpoints')
                    ->modalContent(fn (Attribute $record) => new HtmlString(
                        Blade::render('@livewire('.ManageOptions::class.'::class, [
                            \'record\' => '.$record->getKey().',
                        ])')
                    ))
                    ->visible(fn (Attribute $record) => in_array($record->type, Attribute::fieldsWithOptions()))
                    ->slideOver()
                    ->modalWidth('xl')
                    ->modalHeading(fn (Attribute $record) => new HtmlString(
                        Blade::render('<div class="flex">'.__('Manage Options').' <x-filament::badge class="px-1 mx-1" type="primary">'.$record->name.'</x-filament::badge></div>')
                    ))
                    ->modalDescription(new HtmlString(__('Add common options for this attribute.<br>These options will be available on product attributes tabs.')))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),
                Tables\Actions\EditAction::make()
                    ->slideOver()
                    ->modalWidth('md'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('enabled')
                        ->label(__('forms.actions.enable'))
                        ->icon('untitledui-check-verified')
                        ->action(function (Collection $records): void {
                            $records->each->update(['is_enabled' => true]);

                            Notification::make()
                                ->title(
                                    __('notifications.enabled', [
                                        'item' => __('pages/attributes.single'),
                                    ])
                                )
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('disabled')
                        ->label(__('forms.actions.disable'))
                        ->icon('untitledui-slash-circle-01')
                        ->action(function (Collection $records): void {
                            $records->each->update(['is_enabled' => false]);

                            Notification::make()
                                ->title(__('components.tables.status.updated'))
                                ->body(
                                    __('notifications.disabled', [
                                        'item' => __('pages/attributes.single'),
                                    ])
                                )
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
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
            'index' => Pages\ListAttributes::route('/'),
            // 'create' => Pages\CreateAttribute::route('/create'),
            // 'edit' => Pages\EditAttribute::route('/{record}/edit'),
            // 'options' => ManageOptions::route('/{record}/options'),
        ];
    }
}
