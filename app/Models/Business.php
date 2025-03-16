<?php

namespace App\Models;

use App\Exceptions\TenantNotFoundException;
use Filament\Facades\Filament;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasCurrentTenantLabel;
use Illuminate\Database\Eloquent\Casts\Attribute as CastsAttribute;
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

        foreach ([Role::class, Location::class, Brand::class, Category::class, Attribute::class, Product::class, CustomerGroup::class] as $class) {
            static::resolveRelationUsing(str(class_basename($class))->camel()->plural()->toString(), fn (Model $model) => $model->hasMany($class));
        }
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

    public function uuid(): CastsAttribute
    {
        return CastsAttribute::make(
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

    public function getFilamentLogoUrl(): ?string
    {
        return isset($this->logo) ? Storage::url($this->logo) : null;
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('is_owner');
    }

    public function owners(): BelongsToMany
    {
        return $this->users()->wherePivot('is_owner', true);
    }

    public function getCurrentTenantLabel(): string
    {
        if (optional(Filament::auth()->user())->isOwner($this)) {
            return 'Owner';
        }

        return '';
    }
}
