<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Models\Attribute;
use App\Models\AttributeProduct;
use App\Models\Option;
use App\Models\Product;
use Illuminate\Support\Arr;

final class MapProductOptions
{
    public static function generate(Product $product): array
    {
        $options = AttributeProduct::with(['attribute', 'option'])
            ->where('product_id', $product->id)
            ->get()
            ->map(fn ($attributeProduct) => $attributeProduct->option)
            ->filter(fn ($option) => $option instanceof Option);

        $optionsX = collect();

        foreach ($product->attributes as $attribute) {
            if ($attribute->hasTextOption()) {
                continue;
            }

            $attributeOptions = $options->where('attribute_id', $attribute->id)
                ->map(fn ($option) => self::mapOptionValue($option))
                ->toArray();

            $optionsX->push(self::mapOption($attribute, $attributeOptions));
        }

        return $optionsX->groupBy('id')
            ->map(fn ($group, $key) => Arr::collapse($group))
            ->values()
            ->toArray();
    }

    protected static function mapOption(Attribute $attribut, array $options = []): array
    {
        return [
            'id' => $attribut->id,
            'key' => 'attribute_'.$attribut->id,
            'name' => $attribut->name,
            'options' => $options,
        ];
    }

    protected static function mapOptionValue(Option $option): array
    {
        return [
            'id' => $option->id,
            'key' => 'option_'.$option->id,
            'option' => $option->value,
        ];
    }
}
