<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'team_id');
    }
}
