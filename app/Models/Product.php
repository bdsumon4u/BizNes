<?php

namespace App\Models;

use App\Enum\ProductType;
use App\Traits\HasMedia;
use App\Traits\HasPrices;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\MediaLibrary\HasMedia as IMedia;

class Product extends Model implements IMedia
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    use HasMedia {
        registerMediaCollections as registerMediaCollectionsFromTrait;
    }
    use HasPrices;

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

    public function purchases(): MorphMany
    {
        return $this->morphMany(PurchaseItem::class, 'purchasable');
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
}
