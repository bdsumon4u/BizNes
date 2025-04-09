<?php

namespace App\Filament\Resources\PurchaseResource\Pages;

use App\Filament\Resources\PurchaseResource;
use App\Models\Product;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreatePurchase extends CreateRecord
{
    protected static string $resource = PurchaseResource::class;

    protected static string $view = 'filament.pages.purchases.create';

    public array $searchResults = [];

    public array $purchaseItems = [];

    public float $discount = 0;

    public string $discountType = 'fixed';

    public float $additionalCost = 0;

    public string $additionalCostNote = '';

    public $errorMessages = [];

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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Calculate subtotal from all purchase items
        $subtotal = collect($this->purchaseItems)->reduce(function ($total, $item) {
            $itemTotal = $item['price'] * $item['quantity'];

            // Apply individual item discount
            if ($item['discount_type'] === 'percent') {
                $itemTotal -= ($itemTotal * $item['discount'] / 100);
            } else {
                $itemTotal -= $item['discount'];
            }

            return $total + $itemTotal;
        }, 0);

        // Apply global discount
        if ($this->discountType === 'percent') {
            $subtotal -= ($subtotal * $this->discount / 100);
        } else {
            $subtotal -= $this->discount;
        }

        // Add additional costs
        $data['amount'] = $subtotal + $this->additionalCost;

        foreach (['discount', 'discountType', 'additionalCost', 'additionalCostNote'] as $key) {
            $data[Str::snake($key)] = $this->{$key};
        }

        return $data;
    }

    protected function rules(): array
    {
        $rules = [
            'purchaseItems' => ['required', 'array', 'min:1'],
            'purchaseItems.*.id' => ['required', 'exists:products,id'],
            'purchaseItems.*.quantity' => ['required', 'numeric', 'min:1'],
            'purchaseItems.*.price' => ['required', 'numeric', 'min:0'],
            'purchaseItems.*.discount_type' => ['required', 'in:fixed,percent'],
            'purchaseItems.*.expiry_date' => ['nullable', 'date', 'after_or_equal:today'],
            'discountType' => ['required', 'in:fixed,percent'],
            'additionalCost' => ['required', 'numeric', 'min:0'],
        ];

        // Add product discount validation rules
        foreach ($this->purchaseItems as $index => $item) {
            $rules["purchaseItems.{$index}.discount"] = ['required', 'numeric', 'min:0', function ($attribute, $value, $fail) use ($item) {
                if ($item['discount_type'] === 'percent' && $value > 100) {
                    $fail('Discount percentage cannot exceed 100%');
                } elseif ($item['discount_type'] === 'fixed' && $value > $item['price']) {
                    $fail('Discount amount cannot exceed price');
                }
            }];
        }

        // Add global discount validation rule
        $rules['discount'] = ['required', 'numeric', 'min:0', function ($attribute, $value, $fail) {
            if ($this->discountType === 'percent' && $value > 100) {
                $fail('Discount percentage cannot exceed 100%');
            } elseif ($this->discountType === 'fixed') {
                $subtotal = collect($this->purchaseItems)->reduce(function ($total, $item) {
                    return $total + ($item['price'] * $item['quantity']);
                }, 0);

                if ($value > $subtotal) {
                    $fail('Discount amount cannot exceed subtotal');
                }
            }
        }];

        return $rules;
    }

    protected function afterValidate(): void
    {
        $this->validate($this->rules(), attributes: [
            'purchaseItems.*.quantity' => 'quantity',
            'purchaseItems.*.price' => 'price',
            'purchaseItems.*.discount' => 'discount',
            'purchaseItems.*.expiry_date' => 'expiry date',
            'purchaseItems.*.discount_type' => 'discount type',
        ]);
    }

    protected function onValidationError(ValidationException $exception): void
    {
        $this->errorMessages = $exception->errors();
    }

    protected function afterCreate(): void
    {
        parent::afterCreate();

        // Create purchase items
        foreach ($this->purchaseItems as $item) {
            $this->record->items()->create([
                'product_id' => $item['id'],
                'purchasable_type' => Product::class,
                'purchasable_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'discount' => $item['discount'],
                'discount_type' => $item['discount_type'],
                'expiry_date' => $item['expiry_date'] ?? null,
            ]);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                ...$form->getComponents(),
                Forms\Components\TextInput::make('search')
                    ->hiddenLabel()
                    ->placeholder('Search products...')
                    ->live(debounce: 300)
                    ->extraAlpineAttributes(['autofocus' => true])
                    ->afterStateUpdated(function ($state) {
                        if (! $state || strlen($state) < 2) {
                            $this->searchResults = [];

                            return;
                        }

                        $this->searchResults = Product::search($state)
                            ->query(function ($query) {
                                $query->where('business_id', Filament::getTenant()->getKey())
                                    ->with('variants.parent');
                            })
                            ->get(['id', 'parent_id', 'name', 'price'])
                            ->flatMap(function (Product $product) {
                                if ($product->variants->isEmpty()) {
                                    return [[
                                        'id' => $product->id,
                                        'name' => $product->name,
                                        'image' => $product->getFirstMediaUrl('thumbnail'),
                                        'price' => 12,
                                    ]];
                                }

                                return $product->variants->map(function (Product $product) {
                                    return [
                                        'id' => $product->id,
                                        'name' => $product->title,
                                        'image' => $product->getFirstMediaUrl('thumbnail'),
                                        'price' => 23,
                                    ];
                                });
                            })
                            ->toArray();
                    })
                    ->columnSpanFull()
                    ->dehydrated(false),
            ]);
    }
}
