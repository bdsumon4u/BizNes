<?php

namespace App\Models;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->name === Utils::getSuperAdminName();
    }
}
