<?php

namespace App\Models;

use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function isSuperAdmin(): bool
    {
        return $this->name === Utils::getSuperAdminName();
    }
}
