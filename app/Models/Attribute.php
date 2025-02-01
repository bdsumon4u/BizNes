<?php

namespace App\Models;

use App\Enum\FieldType;
use Illuminate\Database\Eloquent\Casts\Attribute as CastsAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    /** @use HasFactory<\Database\Factories\AttributeFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => FieldType::class,
            'is_enabled' => 'boolean',
            'is_searchable' => 'boolean',
            'is_filterable' => 'boolean',
        ];
    }

    protected function typeFormatted(): CastsAttribute
    {
        return CastsAttribute::make(
            get: fn () => static::typesFields()[$this->type->value]
        );
    }

    public static function typesFields(): array
    {
        return FieldType::options();
    }

    public static function fieldsWithOptions(): array
    {
        return [
            FieldType::Checkbox,
            FieldType::ColorPicker,
            FieldType::Select,
        ];
    }

    public function hasMultipleOptions(): bool
    {
        return in_array($this->type, [FieldType::Checkbox, FieldType::ColorPicker]);
    }

    public function hasSingleOption(): bool
    {
        return $this->type === FieldType::Select;
    }

    public function hasTextOption(): bool
    {
        return in_array($this->type, [
            FieldType::Text,
            FieldType::Number,
            FieldType::RichText,
            FieldType::DatePicker,
        ]);
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }
}
