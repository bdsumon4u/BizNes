<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Forms\Components\SEOField;
use App\Models\Category;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'ri-book-shelf-line';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('General'))
                    ->collapsible()
                    ->compact()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->placeholder('Women, Baby Shoes, MacBook...')
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state, TextInput $input, ?Model $record): void {
                                if (! $get('is_slug_changed_manually') && filled($state) && blank($record)) {
                                    $parent = $get('parent_id') ? $input->getContainer()->getComponent('parent_id')->getSelectedRecord() : null;
                                    $set('slug', Str::slug($parent?->slug.' '.$state, language: config('app.locale', 'en')));
                                }
                            })
                            ->debounce('500ms')
                            ->required(),
                        Forms\Components\Hidden::make('is_slug_changed_manually')
                            ->default(false)
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('slug')
                            ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule) => $rule->where('business_id', Filament::getTenant()))
                            ->afterStateUpdated(function (Set $set): void {
                                $set('is_slug_changed_manually', true);
                            })
                            ->rule(fn ($state): \Closure => function (string $attribute, $value, Closure $fail) use ($state): void {
                                if ($state !== '/' && (Str::startsWith($value, '/') || Str::endsWith($value, '/'))) {
                                    $fail(__('The slug cannot start or end with a slash.'));
                                }
                            })
                            ->required(),
                        Forms\Components\Select::make('parent_id')
                            ->label(__('Parent'))
                            ->relationship(
                                name: 'parent',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query, ?Model $record) {
                                    $query->with('ancestorsAndSelf')->where('is_enabled', true);
                                    if ($record) {
                                        $query->whereNotIn('id', $record->descendants()->pluck('id')->push($record->getKey())->toArray());
                                    }
                                }
                            )
                            ->getOptionLabelFromRecordUsing(fn (Category $record) => $record->getLabelOptionName())
                            ->preload()
                            ->searchable()
                            ->optionsLimit(20)
                            ->afterStateUpdated(function (Set $set, Get $get, Select $select, ?Model $record): void {
                                if (! $get('is_slug_changed_manually') && blank($record) && ! Str::startsWith($get('slug'), $get('parent.slug'))) {
                                    $set('slug', Str::slug($select->getSelectedRecord()?->slug.' '.$get('name'), language: config('app.locale', 'en')));
                                }
                            })
                            ->live()
                            ->key('parent_id'),
                        Forms\Components\Toggle::make('is_enabled')
                            ->label(__('Enabled'))
                            ->default(true)
                            ->helperText(__('Set :name visibility for the customers.', ['name' => __('category')])),
                        Forms\Components\RichEditor::make('description')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'link',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ]),
                    ]),
                Forms\Components\Section::make(__('Media'))
                    ->collapsible()
                    ->compact()
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->directory('categories')
                            ->image()
                            ->maxSize(512),
                    ]),
                ...SEOField::make(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('ancestors'))
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->square()
                    ->grow(false)
                    ->defaultImageUrl(url('/imgs/no-image-100x100.svg')),
                Tables\Columns\TextColumn::make('name')
                    ->description(function (Category $record): ?HtmlString {
                        if ($record->ancestors->isEmpty()) {
                            return null;
                        }

                        return new HtmlString(__('in <strong>:path</strong>', [
                            'path' => $record->getParentPathName(),
                        ]));
                    })
                    ->searchable()
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('slug')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\IconColumn::make('is_enabled')
                    ->label(__('Enabled'))
                    ->boolean()
                    ->alignCenter()
                    ->action(fn (Category $record): bool => $record->update(['is_enabled' => ! $record->is_enabled])),
                Tables\Columns\TextColumn::make('updated_at')
                    ->date()
                    ->sortable(),
            ])
            ->reorderable('position')
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver()
                    ->modalWidth('md'),
            ])
            ->groupedBulkActions([
                Tables\Actions\BulkAction::make('enabled')
                    ->label(__('Enable'))
                    ->icon('untitledui-check-verified')
                    ->action(function (Collection $records): void {
                        $records->each->update([
                            'is_enabled' => true,
                        ]);

                        Notification::make()
                            ->title(
                                __(':item has successfully enabled', [
                                    'item' => __('category'),
                                ])
                            )
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
                Tables\Actions\BulkAction::make('disabled')
                    ->label(__('Disable'))
                    ->icon('untitledui-slash-circle-01')
                    ->action(function (Collection $records): void {
                        $records->each->update([
                            'is_enabled' => false,
                        ]);

                        Notification::make()
                            ->title(
                                __(':item has successfully enabled', [
                                    'item' => __('category'),
                                ])
                            )
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_enabled')
                    ->label(__('Enabled'))
                    ->native(false),
            ])
            ->persistFiltersInSession();
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
            'index' => Pages\ListCategories::route('/'),
            // 'create' => Pages\CreateCategory::route('/create'),
            // 'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
