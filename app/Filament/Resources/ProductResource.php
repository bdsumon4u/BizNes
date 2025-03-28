<?php

namespace App\Filament\Resources;

use App\Enum\ProductType;
use App\Filament\Resources\ProductResource\Pages;
use App\Forms\Components\SelectTree;
use App\Models\Product;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use JaOcero\RadioDeck\Forms\Components\RadioDeck;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                RadioDeck::make('type')
                    ->options(ProductType::class)
                    ->descriptions(ProductType::class)
                    ->icons(ProductType::class)
                    ->alignment(Alignment::Start)
                    ->color('primary')
                    ->columns(4)
                    ->required()
                    ->columnSpanFull()
                    ->live()
                    ->default(ProductType::Standard)
                    ->hint(new HtmlString(Blade::render('
                        <div class="flex">You can <x-filament::badge size="sm" color="danger" class="px-1 mx-1">NOT</x-filament::badge> change the product type once the product is created.</div>
                    ')))
                    ->hintColor('danger')
                    ->hintIcon('heroicon-o-exclamation-circle')
                    ->hiddenOn('edit'),

                // Search and select external product
                Forms\Components\Section::make(__('External Product'))
                    ->collapsible()
                    ->compact()
                    ->schema([
                        Forms\Components\Select::make('external_id')
                            ->label(__('External Product ID'))
                            ->options([])
                            ->searchable()
                            ->placeholder(__('Search for an external product...'))
                            ->helperText(__('Search for an external product by its ID.'))
                            ->afterStateUpdated(function ($state, Forms\Set $set): void {
                                //
                            }),
                    ])
                    ->columnSpanFull()
                    ->visible(fn (Forms\Get $get) => ProductType::External(case: $get('type'))),

                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Section::make(__('General information'))
                            ->collapsible()
                            ->compact()
                            ->description(fn (Product $product) => $product->type?->getDescription())
                            ->icon(fn (Product $product) => $product->type?->getIcon())
                            ->iconSize(IconSize::Large)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->default('Table set')
                                    ->afterStateUpdated(function ($state, Forms\Set $set): void {
                                        $set('slug', Str::slug($state));
                                    }),
                                Forms\Components\TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->maxLength(255)
                                    ->default('table-set')
                                    ->unique(ignoreRecord: true),
                                Forms\Components\Textarea::make('summary')
                                    ->columnSpan('full'),
                                Forms\Components\RichEditor::make('description')
                                    ->columnSpan('full'),

                                Forms\Components\Group::make()
                                    ->schema([
                                        // Components\Separator::make()
                                        //     ->columnSpanFull(),

                                        Forms\Components\TextInput::make('external_id')
                                            ->label(__('External Product ID'))
                                            ->exists(Product::class)
                                            ->helperText(__('The original identifier of your product from the external supplier.')),
                                    ])
                                    ->columnSpanFull()
                                    ->columns(),
                            ])
                            ->columns(),

                        Forms\Components\Section::make(__('Inventory'))
                            ->collapsible()
                            ->compact()
                            ->schema([
                                Forms\Components\Placeholder::make('attributes')
                                    ->content(new HtmlString(Blade::render(<<<'BLADE'
                                    <p class="max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('Configure the inventory attributes for this product.') }}
                                    </p>
                                BLADE))),

                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('sku')
                                            ->unique()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('barcode')
                                            ->unique()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('security_stock')
                                            ->helperText(__('The security stock is the limit stock for your products which alerts you if the product stock will soon be out of stock.'))
                                            ->numeric()
                                            ->default(0)
                                            ->rules(['integer', 'min:0']),
                                    ])
                                    ->columns(),
                            ])
                            ->hiddenOn('edit'),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema(static::class == ProductResource::class ? [
                        Forms\Components\Section::make(__('Product availability'))
                            ->collapsible()
                            ->compact()
                            ->schema([
                                Forms\Components\Toggle::make('is_visible')
                                    ->onColor('success')
                                    ->default(true),

                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label(__('Availability'))
                                    ->default(now())
                                    ->native(false)
                                    ->helperText(__('Specify a publication date so that your product are scheduled on your store.'))
                                    ->required(),
                            ]),

                        Forms\Components\Section::make(__('Media'))
                            ->collapsible()
                            ->compact()
                            ->schema([
                                Forms\Components\FileUpload::make('thumbnail')
                                    ->directory('products/thumbnail')
                                    ->helperText(__('Used to represent your product during checkout, social sharing and more.'))
                                    ->image()
                                    ->maxSize(512)
                                    ->columnSpan(['lg' => 2]),

                                Forms\Components\FileUpload::make('images')
                                    ->directory('products/images')
                                    ->helperText(__('Add additional images to your product.'))
                                    ->multiple()
                                    ->panelLayout('grid')
                                    ->maxSize(512)
                                    ->columnSpanFull(),
                            ])
                            ->hiddenOn('edit'),

                        Forms\Components\Section::make(__('Associations'))
                            ->collapsible()
                            ->compact()
                            ->schema([
                                Forms\Components\Select::make('brand_id')
                                    ->label(__('Brand'))
                                    ->relationship('brand', 'name', modifyQueryUsing: fn (Builder $query) => $query->whereBelongsTo(Filament::getTenant())->where('is_enabled', true)->orderBy('position'))
                                    ->searchable()
                                    ->preload(),

                                SelectTree::make('categories')
                                    ->relationship('categories', modifyQueryUsing: fn (Builder $query) => $query->whereBelongsTo(Filament::getTenant())->where('is_enabled', true)->orderBy('position'))
                                    ->independent(true)
                                    ->grouped(false)
                                    ->searchable()
                                    ->dehydrated(false)
                                    ->saveRelationshipsUsing(function ($state, $record) {
                                        $record->categories()->sync($state);
                                    }),
                            ]),
                    ] : [])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('thumbnail')
                    ->collection('thumbnail')
                    ->square()
                    ->grow(false),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\TextColumn::make('sku')
                    ->label(__('SKU'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('stock')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\IconColumn::make('is_visible')
                    ->label(__('Visibility'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label(__('Published At'))
                    ->dateTime()
                    ->toggleable()
                    ->sortable(),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraints([
                        QueryBuilder\Constraints\TextConstraint::make('name'),
                        QueryBuilder\Constraints\SelectConstraint::make('type')
                            ->options(ProductType::class)
                            ->multiple(),
                        QueryBuilder\Constraints\BooleanConstraint::make('is_visible')
                            ->label(__('Availability')),
                        QueryBuilder\Constraints\DateConstraint::make('published_at'),
                    ])
                    ->constraintPickerColumns(),
            ])
            ->deferFilters()
            ->filtersFormWidth(MaxWidth::Large)
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (static::class === ProductResource::class) {
            return $query->whereNull('parent_id');
        }

        return $query;
    }
}
