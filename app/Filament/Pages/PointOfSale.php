<?php

namespace App\Filament\Pages;

use App\Models\Coupon;
use App\Models\Draft;
use App\Models\Product;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class PointOfSale extends Page
{
    protected static ?string $navigationIcon = 'tabler-building-store';

    protected static string $view = 'filament.pages.point-of-sale';

    protected static bool $shouldRegisterNavigation = false;

    protected ?string $maxContentWidth = 'full';

    public ?string $search = '';

    public array $searchResults = [];

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return '';
    }

    public function updatedSearch()
    {
        $this->updateSearchResults();
    }

    protected function updateSearchResults()
    {
        $this->searchResults = Product::search($this->search)
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

                return $product->variants->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->title,
                        'image' => $product->getFirstMediaUrl('thumbnail'),
                        'price' => 23,
                    ];
                });
            })
            ->toArray();
    }

    protected function mapProductData()
    {
        return function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => 12,
                'image' => '', // Add this if you have image URLs
            ];
        };
    }

    public function saveDraft($draft)
    {
        Draft::create([
            'business_id' => Filament::getTenant()->getKey(),
            'user_id' => Auth::id(),
            'name' => $draft['name'],
            'data' => $draft,
        ]);

        $this->dispatch('draft-saved')->success('Draft saved successfully');
    }

    public function validateCoupon($code)
    {
        $coupon = Coupon::where('business_id', Filament::getTenant()->getKey())
            ->where('code', $code)
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->first();

        if (! $coupon) {
            return [
                'valid' => false,
                'message' => 'Invalid or expired coupon code',
            ];
        }

        return [
            'valid' => true,
            'discount' => $coupon->discount_amount,
            'type' => $coupon->discount_type,
        ];
    }

    public function getDrafts()
    {
        return Draft::where('business_id', Filament::getTenant()->getKey())
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function mount()
    {
        // Load initial products
        $this->updateSearchResults();
    }
}
