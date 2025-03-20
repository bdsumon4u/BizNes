<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Macros\Arr;
use App\Models\Product;
use App\Models\Variant;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GenerateVariants extends EditRecord
{
    protected static string $resource = ProductResource::class;

    public array $availableOptions = [];

    public array $variants = [];

    protected function resolveRecord(int | string $key): Model
    {
        $record = parent::resolveRecord($key);

        $this->setupProductAttributes($record);

        return $record;
    }

    public function setupProductAttributes(Product $product): void
    {
        $this->availableOptions = MapProductOptions::generate($product);

        $this->mapVariantPermutations($product);
    }

    public function mapVariantPermutations(Product $product): void
    {
        $optionsValues = collect($this->availableOptions)
            ->mapWithKeys(fn ($attribute) => [
                $attribute['name'] => collect($attribute['options'])
                    ->map(fn ($item) => [
                        'id' => $item['id'],
                        'option' => $item['option'],
                    ]),
            ])
            ->toArray();
        dump('available options', $this->availableOptions, 'options values', $optionsValues);

        $variants = Variant::query()
            // ->with(['prices', 'values'])
            ->where('product_id', $product->getKey())
            ->get()
            ->map(fn (Variant $variant) => [ // @phpstan-ignore-line
                'id' => $variant->id,
                'sku' => $variant->sku,
                // 'price' => $variant->prices()->first()?->amount ?: 0,
                // 'stock' => $variant->stock,
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
        dump('existing variants', $variants);

        $this->variants = $this->mapVariantsToProductOptions($product, $optionsValues, $variants);
        dump('mapped variants', $this->variants);
    }

    protected function mapVariantsToProductOptions(Product $product, array $options, array $variants): array
    {
        $permutations = Arr::permutate($options);
        dump('permutations', $permutations);

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
            $price = $variant['price'] ?? 0;
            $stock = $variant['stock'] ?? 0;

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
                    $price = 0;
                    $stock = 0;
                }
            }

            $variantPermutations[] = [
                'key' => Str::random(),
                'variant_id' => $variantId,
                'name' => $name,
                'sku' => $sku,
                'price' => $price,
                'stock' => $stock,
                'values' => Arr::getPermutationIds($permutation),
            ];
        }

        return $variantPermutations;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data + [
            // 'variants' => $this->variants,
        ];
    }

    protected function afterFill()
    {
        // $this->form->fill([
        //     'variants' => $this->variants,
        // ]);
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
                Forms\Components\TextInput::make('name'),
                Forms\Components\Repeater::make('variants')
                    // ->relationship('variants')
                    ->schema([
                        Forms\Components\TextInput::make('name'),
                        Forms\Components\TextInput::make('sku')
                            ->label(__('SKU')),
                    ])
                    ->formatStateUsing(fn () => $this->variants)
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        dd($record, $data);
        // $record->update($data);

        return $record;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}
