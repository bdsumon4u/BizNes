<?php

namespace App\Models;

use App\Exceptions\TenantNotFoundException;
use Filament\Facades\Filament;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasCurrentTenantLabel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Business extends Model implements HasAvatar, HasCurrentTenantLabel
{
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::creating(function (Business $business) {
            $business->uuid = Str::uuid();
        });
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if ($value === domain()) {
            return Filament::getUserDefaultTenant(Filament::auth()->user());
        }

        $value = str($value)->beforeLast(subdomain());

        $record = parent::resolveRouteBinding($value, $field);

        throw_unless($record, (new TenantNotFoundException)->setModel(static::class, [$value]));

        return $record;
    }

    public function uuid(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (request()->getHost() === domain()) {
                    if (request()->is(Filament::getDefaultPanel()->getPath())) {
                        return domain();
                    }

                    return subdomain($value);
                }

                if (Str::endsWith(request()->getHost(), subdomain())) {
                    return subdomain($value);
                }

                return is_tld($value) ? $value : subdomain($value);
            },
        );
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return isset($this->favicon) ? Storage::url($this->favicon) : null;
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

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function brands(): HasMany
    {
        return $this->hasMany(Brand::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getCurrentTenantLabel(): string
    {
        if (optional(Filament::auth()->user())->isOwner($this)) {
            return 'Owner';
        }

        return '';
    }
}
