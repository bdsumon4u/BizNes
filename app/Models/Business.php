<?php

namespace App\Models;

use App\Exceptions\TenantNotFoundException;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasCurrentTenantLabel;
use Illuminate\Database\Eloquent\Casts\Attribute as CastsAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Business extends Model implements HasAvatar, HasCurrentTenantLabel
{
    use HasFactory;

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::creating(function (Business $business) {
            $business->domain = Str::slug($business->name);
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

    public function domain(): CastsAttribute
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

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function getCurrentTenantLabel(): string
    {
        if (optional(Filament::auth()->user())->isOwner($this)) {
            return 'Owner';
        }

        return '';
    }

    public function giveSuperAdminRoleTo(User $user)
    {
        // temporary: get session team_id for restore at end
        $session_team_id = getPermissionsTeamId();
        // set actual new team_id to package instance
        setPermissionsTeamId($this);
        // get the admin user and assign roles/permissions on new team model
        $user->assignRole($this->getSuperAdminRole());
        // restore session team_id to package instance using temporary value stored above
        setPermissionsTeamId($session_team_id);
    }

    private function getSuperAdminRole(): Role
    {
        return tap(Utils::getRoleModel()::firstOrCreate(
            [
                'name' => Utils::getSuperAdminName(),
                Utils::getTenantModelForeignKey() => $this->getKey(),
            ],
            ['guard_name' => Utils::getFilamentAuthGuard()]
        ), fn (Role $role) => $role->givePermissionTo(
            Utils::getPermissionModel()::where('guard_name', Utils::getFilamentAuthGuard())->pluck('id')->toArray()
        ));
    }
}
