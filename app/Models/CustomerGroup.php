<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CustomerGroup extends Model
{
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_prices')
            ->withPivot(['id', 'quantity', 'price'])
            ->using(ProductPrice::class);
    }
}
