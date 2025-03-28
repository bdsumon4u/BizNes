<?php

namespace App\Traits;

use App\Models\Price;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasPrices
{
    /**
     * @return HasMany<Price, $this>
     */
    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }
}
