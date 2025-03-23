<?php

namespace App\Models;

use App\Traits\HasMedia;
use App\Traits\HasPrices;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia as IMedia;

class Variant extends Model implements IMedia
{
    use HasMedia {
        registerMediaCollections as registerMediaCollectionsFromTrait;
    }
    use HasPrices;

    public function type(): Attribute
    {
        return Attribute::get(fn () => $this->product->type);
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(Option::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function registerMediaCollections(): void
    {
        $this->registerMediaCollectionsFromTrait();

        $this->addMediaCollection('files');
    }
}
