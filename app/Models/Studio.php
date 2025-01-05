<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Studio extends Model
{
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class, 'team_id');
    }
}
