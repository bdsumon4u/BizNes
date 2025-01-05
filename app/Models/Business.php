<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Business extends Model
{
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::creating(function (Business $business) {
            $business->uuid = Str::uuid();
        });
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
