<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    use HasRecursiveRelationships;

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function getCustomPaths(): array
    {
        return [
            [
                'name' => 'name_path',
                'column' => 'name',
                'separator' => ' / ',
                'reverse' => true,
            ],
        ];
    }

    /**
     * Use to display custom label into filament relationship select form component
     */
    public function getLabelOptionName(): string
    {
        return $this->ancestorsAndSelf->last()->name_path;
    }

    public function getParentPathName(): string
    {
        return $this->ancestors->last()->name_path;
    }
}
