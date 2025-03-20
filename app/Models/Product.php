<?php

namespace App\Models;

use App\Enum\ProductType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;
    use InteractsWithMedia;

    protected function casts(): array
    {
        return [
            'type' => ProductType::class,
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function categories(): MorphToMany
    {
        return $this->morphedByMany(Category::class, 'productable', 'product_has_relations');
    }

    public function prices(): BelongsToMany
    {
        return $this->belongsToMany(CustomerGroup::class, 'product_prices')
            ->withPivot(['id', 'quantity', 'price'])
            ->using(ProductPrice::class);
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class)
            ->withPivot(['option_id', 'value'])
            ->using(AttributeProduct::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('uploads')
            ->acceptsMimeTypes(['image/jpg', 'image/jpeg', 'image/png'])
            ->useFallbackUrl(url('/imgs/no-image-100x100.svg'));

        $this->addMediaCollection('thumbnail')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpg', 'image/jpeg', 'image/png'])
            ->useFallbackUrl(url('/imgs/no-image-100x100.svg'));

        $this->addMediaCollection('files');
    }
}
