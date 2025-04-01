<?php

namespace App\Filament\Resources\PurchaseResource\Pages;

use App\Filament\Resources\PurchaseResource;
use App\Models\Product;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreatePurchase extends CreateRecord
{
    protected static string $resource = PurchaseResource::class;

    protected static string $view = 'filament.pages.purchases.create';

    public ?string $search = '';

    public array $searchResults = [];

    public array $purchaseItems = [];

    public float $global_discount = 0;

    public string $global_discount_type = 'fixed';

    public float $final_amount = 0;

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
        dd($data, $this->purchaseItems);
        // Include the global discount and final amount in your purchase record
        $data['global_discount'] = $this->global_discount;
        $data['global_discount_type'] = $this->global_discount_type;
        $data['amount'] = $this->final_amount;

        return $data;
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
                    ->columnSpanFull(),
            ]);
    }
}
