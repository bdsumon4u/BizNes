<?php

namespace App\Models;

use App\Enum\ProductType;
use App\Traits\HasMedia;
use App\Traits\HasPrices;
use Illuminate\Database\Eloquent\Casts\Attribute as CastsAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Laravel\Scout\Attributes\SearchUsingFullText;
use Laravel\Scout\Attributes\SearchUsingPrefix;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia as IMedia;

class Product extends Model implements IMedia
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, HasMedia, HasPrices, Searchable {
        HasMedia::registerMediaCollections as registerMediaCollectionsFromTrait;
    }

    protected function casts(): array
    {
        return [
            'type' => ProductType::class,
        ];
    }

    public function isStandard(): bool
    {
        return $this->type === ProductType::Standard;
    }

    public function title(): CastsAttribute
    {
        return CastsAttribute::get(function () {
            if (! $this->parent_id) {
                return $this->name;
            }

            return $this->parent->name.' ['.$this->name.']';
        })->shouldCache();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function categories(): MorphToMany
    {
        return $this->morphedByMany(Category::class, 'productable', 'product_has_relations');
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class)
            ->withPivot(['option_id', 'value'])
            ->using(AttributeProduct::class);
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(Option::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    public function purchases(): BelongsToMany
    {
        return $this->belongsToMany(Purchase::class);
    }

    public function stocks(): MorphMany
    {
        return $this->morphMany(Stock::class, 'stockable');
    }

    public function registerMediaCollections(): void
    {
        $this->registerMediaCollectionsFromTrait();

        $this->addMediaCollection('files');
    }

    #[SearchUsingPrefix(['id', 'name'])]
    #[SearchUsingFullText(['summary'])]
    public function toSearchableArray(): array
    {
        $searchableArray = [
            'id' => (string) $this->id,
            'name' => $this->name,
            'summary' => $this->summary,
        ];

        if (config('scout.driver') === 'database') {
            return $searchableArray;
        }

        return array_merge($searchableArray, [
            'brand' => $this->brand?->name,
            'title' => $this->title,
            // 'categories' => $this->categories->pluck('name')->toArray(),
            // 'attributes' => $this->attributes->mapWithKeys(function ($attribute) {
            //     return [$attribute->name => $attribute->pivot->value];
            // })->toArray(),
            // 'options' => $this->options->pluck('name')->toArray(),
            // 'variants' => $this->variants->pluck('name')->toArray(),
        ]);
    }
}
