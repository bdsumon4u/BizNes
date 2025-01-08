<?php

namespace App\Models;

use Filament\Facades\Filament;
use Filament\Models\Contracts\HasCurrentTenantLabel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Business extends Model implements HasCurrentTenantLabel
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
        return $this->belongsToMany(User::class)->withPivot('is_owner');
    }

    public function owners(): BelongsToMany
    {
        return $this->users()->wherePivot('is_owner', true);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function getCurrentTenantLabel(): string
    {
        if (optional(Filament::auth()->user())->isOwner($this)) {
            return 'Owner';
        }

        return '';
    }
}
