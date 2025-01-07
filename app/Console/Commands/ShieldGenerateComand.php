<?php

namespace App\Console\Commands;

use App\Models\Role;
use BezhanSalleh\FilamentShield\Commands\GenerateCommand;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Illuminate\Support\Collection;
use Spatie\Permission\PermissionRegistrar;

class ShieldGenerateComand extends GenerateCommand
{
    protected function generateForResources(array $resources): Collection
    {
        return collect($resources)
            ->values()
            ->each(function ($entity) {
                if ($this->generatorOption === 'policies_and_permissions') {
                    $policyPath = $this->generatePolicyPath($entity);
                    /** @phpstan-ignore-next-line */
                    if (! $this->option('ignore-existing-policies') || ($this->option('ignore-existing-policies') && ! $this->fileExists($policyPath))) {
                        $this->copyStubToApp(static::getPolicyStub($entity['model']), $policyPath, $this->generatePolicyStubVariables($entity));
                    }
                    $this->generateForResource($entity);
                }

                if ($this->generatorOption === 'policies') {
                    $policyPath = $this->generatePolicyPath($entity);
                    /** @phpstan-ignore-next-line */
                    if (! $this->option('ignore-existing-policies') || ($this->option('ignore-existing-policies') && ! $this->fileExists($policyPath))) {
                        $this->copyStubToApp(static::getPolicyStub($entity['model']), $policyPath, $this->generatePolicyStubVariables($entity));
                    }
                }

                if ($this->generatorOption === 'permissions') {
                    $this->generateForResource($entity);
                }
            });
    }

    protected function generateForPages(array $pages): Collection
    {
        return collect($pages)
            ->values()
            ->each(fn (array $page) => $this->generateForPage($page['permission']));
    }

    protected function generateForWidgets(array $widgets): Collection
    {
        return collect($widgets)
            ->values()
            ->each(fn (array $widget) => $this->generateForWidget($widget['permission']));
    }

    public function generateForResource(array $entity): void
    {
        $resourceByFQCN = $entity['fqcn'];
        $permissionPrefixes = Utils::getResourcePermissionPrefixes($resourceByFQCN);

        if (Utils::isResourceEntityEnabled()) {
            $permissions = collect();
            collect($permissionPrefixes)
                ->each(function ($prefix) use ($entity, $permissions) {
                    $permissions->push(Utils::getPermissionModel()::firstOrCreate([
                        'name' => $prefix.'_'.$entity['resource'],
                        'guard_name' => Utils::getFilamentAuthGuard(),
                    ]));
                });

            static::giveSuperAdminPermission($permissions);
        }
    }

    public static function generateForPage(string $page): void
    {
        if (Utils::isPageEntityEnabled()) {
            $permission = Utils::getPermissionModel()::firstOrCreate([
                'name' => $page,
                'guard_name' => Utils::getFilamentAuthGuard(),
            ])->name;

            static::giveSuperAdminPermission($permission);
        }
    }

    public static function generateForWidget(string $widget): void
    {
        if (Utils::isWidgetEntityEnabled()) {
            $permission = Utils::getPermissionModel()::firstOrCreate([
                'name' => $widget,
                'guard_name' => Utils::getFilamentAuthGuard(),
            ])->name;

            static::giveSuperAdminPermission($permission);
        }
    }

    protected static function giveSuperAdminPermission(string|array|Collection $permissions): void
    {
        if (! Utils::isSuperAdminDefinedViaGate() && Utils::isSuperAdminEnabled()) {
            $superAdmin = static::createRole(tenantId: Filament::getTenant()?->id);

            $superAdmin->givePermissionTo($permissions);

            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }

    public static function createRole(?string $name = null, ?int $tenantId = null): Role
    {
        if (Utils::isTenancyEnabled()) {
            if ($tenantId) {
                Utils::getRoleModel()::whereNull(
                    Utils::getTenantModelForeignKey()
                )
                    ->firstOrCreate([
                        'name' => $name ?? Utils::getSuperAdminName(),
                        'guard_name' => Utils::getFilamentAuthGuard(),
                    ]);
            }

            return Utils::getRoleModel()::firstOrCreate([
                'name' => $name ?? Utils::getSuperAdminName(),
                'guard_name' => Utils::getFilamentAuthGuard(),
                Utils::getTenantModelForeignKey() => $tenantId,
            ]);
        }

        return Utils::getRoleModel()::firstOrCreate([
            'name' => $name ?? Utils::getSuperAdminName(),
            'guard_name' => Utils::getFilamentAuthGuard(),
        ]);
    }
}
