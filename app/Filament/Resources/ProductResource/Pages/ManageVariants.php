<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Macros\Arr;
use App\Models\Product;
use App\Models\Variant;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Attributes\Lazy;
use Livewire\Livewire;

#[Lazy()]
class ManageVariants extends ManageRelatedRecords
{
    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'variants';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Variants';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('thumbnail')
                    ->collection('thumbnail')
                    ->square(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sku')
                    ->label(__('SKU'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('position')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->formatStateUsing(
                        fn ($record): HtmlString => new HtmlString(Blade::render(<<<BLADE
                            <div class="flex items-center">
                                <x-filament::badge class="px-1 mx-1" type="primary">{{$record->stock}}</x-filament::badge>
                                {{ __('in stock') }}
                            </div>
                        BLADE))
                    ),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                // Tables\Actions\AssociateAction::make(),
                Tables\Actions\Action::make('generate')
                    ->icon($this->getRecord()->type->getIcon())
                    ->label(__('Generate variants'))
                    ->color('gray')
                    ->modalContent(fn () => new HtmlString( // closure is must.
                        Blade::render('@livewire('.GenerateVariants::class.'::class, [
                            \'record\' => '.$this->getRecord()->getKey().',
                        ])')
                    ))
                    ->visible($this->getRecord()->attributes->isNotEmpty())
                    ->slideOver()
                    ->modalWidth('lg')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),
                Tables\Actions\CreateAction::make()
                    ->icon($this->getRecord()->type->getIcon())
                    ->label(__('Generate variants'))
                    ->color('gray')
                    ->fillForm(function () {
                        $product = $this->getRecord();
                        $availableOptions = MapProductOptions::generate($product);

                        $optionsValues = collect($availableOptions)
                            ->mapWithKeys(fn ($attribute) => [
                                $attribute['name'] => collect($attribute['options'])
                                    ->map(fn ($item) => [
                                        'id' => $item['id'],
                                        'option' => $item['option'],
                                    ]),
                            ])
                            ->toArray();

                        $variants = Variant::query()
                            ->with(['options.attribute'])
                            ->where('product_id', $product->getKey())
                            ->get()
                            ->map(fn (Variant $variant) => [
                                'id' => $variant->id,
                                'sku' => $variant->sku,
                                'options' => $variant->options->mapWithKeys(
                                    fn ($option) => [
                                        $option->attribute->name => [
                                            'id' => $option->id,
                                            'option' => $option->value,
                                        ],
                                    ]
                                )->toArray(),
                            ])
                            ->toArray();
                            
                        return ['variants' => $this->mapVariantsToProductOptions($product, $optionsValues, $variants)];
                    })
                    ->form([
                        Forms\Components\Repeater::make('variants')
                            ->schema([
                                Forms\Components\TextInput::make('name'),
                                Forms\Components\TextInput::make('sku')
                                    ->label(__('SKU')),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->addable(false)
                            ->reorderable(false)
                            ->itemLabel(function (array $state): ?string {
                                $label = $state['name'] ?? null;
                                if (! $state['variant_id']) {
                                    $label .= ' (new)';
                                }

                                return $label;
                            }),
                    ])
                    ->using(function (array $data) {
                        DB::beginTransaction();
                        foreach ($data['variants'] as $variantState) {
                            $variant = Variant::query()->firstOrCreate([
                                'id' => $variantState['variant_id'],
                            ], [
                                'business_id' => Filament::getTenant()->getKey(),
                                'name' => $variantState['name'],
                                'slug' => Str::slug($variantState['name']),
                                'product_id' => $this->getRecord()->getKey(),
                                'sku' => $variantState['sku'],
                            ]);

                            $variant->options()->sync($variantState['values']);
                        }

                        $variantIds = collect($data['variants'])->pluck('variant_id');

                        $this->getRecord()->variants()->whereNotIn('id', $variantIds)
                            ->get()
                            ->each(
                                fn ($variant) => $variant->delete()
                            );

                        DB::commit();

                        return $this->getRecord();
                    })
                    ->visible($this->getRecord()->attributes->isNotEmpty())
                    ->slideOver()
                    ->modalWidth('lg'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DissociateAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DissociateBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function mapVariantsToProductOptions(Product $product, array $options, array $variants): array
    {
        $permutations = Arr::permutate($options);

        if (count($options) === 1) {
            $newPermutations = [];

            foreach ($permutations as $p) {
                $newPermutations[] = [
                    array_key_first($options) => $p,
                ];
            }

            $permutations = $newPermutations;
        }

        $variantPermutations = [];

        foreach ($permutations as $permutation) {
            $variantIndex = collect($variants)->search(function ($variant) use ($permutation) {
                $valueDifference = Arr::recursiveArrayDiffAssoc($permutation, $variant['options']);

                if (! count($valueDifference)) {
                    return $variant;
                }

                $amountMatched = count($permutation) - count($valueDifference);

                return $amountMatched === count($variant['options']);
            });

            $variant = $variants[$variantIndex] ?? null;

            $variantId = $variant['id'] ?? null;
            $name = $variant['name'] ?? Arr::performPermutationIntoWord($permutation, 'option');
            $sku = $variant['sku'] ?? null;

            if ($variant) {
                $existing = collect($variantPermutations)
                    ->where('variant_id', $variant['id'])
                    ->first();

                if ($existing) {
                    $variantId = null;
                    $sku = \Illuminate\Support\Arr::join([
                        $product->sku,
                        mb_strtoupper(Str::slug(Arr::performPermutationIntoWord($permutation, 'option', '-'))),
                    ], '-');
                }
            }

            $variantPermutations[] = [
                'key' => Str::random(),
                'variant_id' => $variantId,
                'name' => $name,
                'sku' => $sku,
                'values' => Arr::getPermutationIds($permutation),
            ];
        }

        return $variantPermutations;
    }
}
