<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Facades\Filament;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Panel;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Arr;

class EditBusinessProfile extends EditTenantProfile
{
    use BusinessForm;

    protected static ?string $slug = 'profile';

    protected ?string $maxContentWidth = MaxWidth::ThreeExtraLarge->value;

    public static function getLabel(): string
    {
        return 'Business Profile';
    }


    protected static ?string $clusterBreadcrumb = null;

    /**
     * @return array<class-string>
     */
    public static function getClusteredComponents(): array
    {
        return Filament::getClusteredComponents(static::class);
    }

    public static function canAccessClusteredComponents(): bool
    {
        foreach (static::getClusteredComponents() as $component) {
            if ($component::canAccess()) {
                return true;
            }
        }

        return false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccessClusteredComponents();
    }

    public function mount(): void
    {
        foreach ($this->getCachedSubNavigation() as $navigationGroup) {
            foreach ($navigationGroup->getItems() as $navigationItem) {
                redirect($navigationItem->getUrl());

                return;
            }
        }
    }

    public function getSubNavigation(): array
    {
        return $this->generateNavigationItems(static::getClusteredComponents());
    }

    /**
     * @param  array<string, string>  $breadcrumbs
     * @return array<string, string>
     */
    public static function unshiftClusterBreadcrumbs(array $breadcrumbs): array
    {
        $clusterBreadcrumb = static::getClusterBreadcrumb();

        if (Arr::first($breadcrumbs) === $clusterBreadcrumb) {
            return $breadcrumbs;
        }

        return [
            ...[static::getUrl() => $clusterBreadcrumb],
            ...$breadcrumbs,
        ];
    }

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? static::$title ?? str(class_basename(static::class))
            ->beforeLast('Cluster')
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return static::$clusterBreadcrumb ?? static::$title ?? str(class_basename(static::class))
            ->beforeLast('Cluster')
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    public static function prependClusterSlug(string $slug): string
    {
        return static::getSlug() . "/{$slug}";
    }

    public static function prependClusterRouteBaseName(string $name): string
    {
        return (string) str(static::getSlug())
            ->replace('/', '.')
            ->append(".{$name}");
    }

    public static function getSlug(): string
    {
        if (filled(static::$slug)) {
            return static::$slug;
        }

        return (string) str(class_basename(static::class))
            ->beforeLast('Cluster')
            ->kebab()
            ->slug();
    }

    public static function getNavigationItemActiveRoutePattern(): string
    {
        return static::getRouteName() . '.*';
    }
}
