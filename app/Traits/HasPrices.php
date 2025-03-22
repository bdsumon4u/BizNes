<?php

namespace App\Traits;

use App\Models\Price;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasPrices
{
    /**
     * @return MorphMany<Price, $this>
     */
    public function prices(): MorphMany
    {
        return $this->morphMany(Price::class, 'priceable');
    }
}
