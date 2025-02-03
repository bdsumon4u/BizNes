<?php

namespace App\Enum;

use App\Traits\ArrayableEnum;
use App\Traits\HasEnumStaticMethods;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use JaOcero\RadioDeck\Contracts\HasDescriptions;
use JaOcero\RadioDeck\Contracts\HasIcons;

/**
 * @method static string External()
 * @method static string Virtual()
 * @method static string Standard()
 * @method static string Variant()
 */
enum ProductType: string implements HasColor, HasDescription, HasDescriptions, HasIcon, HasIcons, HasLabel
{
    use ArrayableEnum;
    use HasEnumStaticMethods;

    case External = 'external';

    case Virtual = 'virtual';

    case Standard = 'standard';

    case Variant = 'variant';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::External => __('External'),
            self::Virtual => __('Virtual'),
            self::Standard => __('Standard'),
            self::Variant => __('Variant'),
        };
    }

    public function getDescriptions(): ?string
    {
        return match ($this) {
            self::External => __('A product sourced from another supplier.'),
            self::Virtual => __('A digital product that can be downloaded by customers.'),
            self::Standard => __('This product has no variations in size, color, or similar.'),
            self::Variant => __('This product allows variations based on product attributes.'),
        };
    }

    public function getDescription(): ?string
    {
        return $this->getDescriptions();
    }

    public function getIcons(): ?string
    {
        return match ($this) {
            self::External => 'phosphor-link-simple-duotone',
            self::Virtual => 'phosphor-monitor-duotone',
            self::Standard => 'phosphor-tag-duotone',
            self::Variant => 'phosphor-swatches-duotone',
        };
    }

    public function getIcon(): ?string
    {
        return $this->getIcons();
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::External => 'indigo',
            self::Virtual => 'info',
            self::Standard => 'gray',
            self::Variant => 'primary',
        };
    }
}
