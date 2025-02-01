<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Forms\Components\SEOField;
use App\Models\Brand;
use App\Models\Business;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?string $navigationIcon = 'ri-bookmark-line';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('General'))
                    ->collapsible()
                    ->compact()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->placeholder('Apple, Nike, Samsung...')
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state, ?Model $record): void {
                                if (! $get('is_slug_changed_manually') && filled($state) && blank($record)) {
                                    $set('slug', Str::slug($state, language: config('app.locale', 'en')));
                                }
                            })
                            ->debounce('500ms')
                            ->required(),
                        Forms\Components\Hidden::make('is_slug_changed_manually')
                            ->default(false)
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('slug')
                            ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule) => $rule->where((new Business)->getForeignKey(), Filament::getTenant()->getKey()))
                            ->afterStateUpdated(function (Set $set): void {
                                $set('is_slug_changed_manually', true);
                            })
                            ->rule(fn ($state): \Closure => function (string $attribute, $value, Closure $fail) use ($state): void {
                                if ($state !== '/' && (Str::startsWith($value, '/') || Str::endsWith($value, '/'))) {
                                    $fail(__('The slug cannot start or end with a slash.'));
                                }
                            })
                            ->required(),
                        Forms\Components\TextInput::make('website')
                            ->placeholder('https://example.com')
                            ->url(),
                        Forms\Components\Toggle::make('is_enabled')
                            ->label(__('Enabled'))
                            ->default(true)
                            ->helperText(__('Set :name visibility for the customers.', ['name' => __('brand')])),
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
                            ->directory('brands')
                            ->image()
                            ->maxSize(512),
                    ]),
                ...SEOField::make(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->square()
                    ->grow(false)
                    ->defaultImageUrl(url('/imgs/no-image-100x100.svg')),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('website')
                    ->url(fn (Brand $record): ?string => $record->website, true)
                    ->icon('ri-external-link-line')
                    ->iconPosition(IconPosition::After),
                Tables\Columns\IconColumn::make('is_enabled')
                    ->label(__('Enabled'))
                    ->boolean()
                    ->alignCenter()
                    ->action(fn (Brand $record): bool => $record->update(['is_enabled' => ! $record->is_enabled])),
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
                                    'item' => __('brand'),
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
                                    'item' => __('brand'),
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
            'index' => Pages\ListBrands::route('/'),
            // 'create' => Pages\CreateBrand::route('/create'),
            // 'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
