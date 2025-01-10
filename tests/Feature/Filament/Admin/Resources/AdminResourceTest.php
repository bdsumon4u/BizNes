
<?php

use App\Filament\Admin\Resources\AdminResource;
use App\Filament\Admin\Resources\AdminResource\Pages\ListAdmins;
use App\Filament\Admin\Resources\RoleResource;
use App\Models\Admin;
use App\Models\Role;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

function allRoles()
{
    return RoleResource::getEloquentQuery()->pluck('id')->toArray();
}

function superAdminRole()
{
    return RoleResource::getEloquentQuery()
        ->firstOrCreate([
            'name' => Utils::getSuperAdminName(),
            'guard_name' => Utils::getFilamentAuthGuard(),
        ]);
}

function normalAdminRole()
{
    return tap(RoleResource::getEloquentQuery()
        ->firstOrCreate([
            'name' => 'Normal Admin',
            'guard_name' => Utils::getFilamentAuthGuard(),
        ]), function (Role $role) {
            if (! $role->wasRecentlyCreated) {
                return;
            }

            $role->givePermissionTo(superAdminRole()->permissions);
        });
}

function createNormalAdmin()
{
    return tap(Admin::factory()->create(), function (Admin $admin) {
        $admin->assignRole(normalAdminRole());
    });
}

beforeEach(function () {
    Filament::setCurrentPanel(
        Filament::getPanel('admin'),
    );
});

it('has only one admin', function () {
    expect(AdminResource::getEloquentQuery()->count())->toBeOne();
});

test('he has super admin role', function () {
    expect(AdminResource::getEloquentQuery()->first()->hasRole(
        Utils::getSuperAdminName(),
        Utils::getFilamentAuthGuard(),
    ))->toBeTrue();
});

describe('admin', function () {
    beforeEach(function () {
        /** @var App\Models\Admin */
        $this->admin = createNormalAdmin();
        actingAs($this->admin, Utils::getFilamentAuthGuard());
    });

    test('can see list of admins', function () {
        Livewire::test(ListAdmins::class)
            ->assertStatus(200)
            ->assertCanSeeTableRecords([$this->admin]);
    });

    test('can create a new admin', function (array $roles) {
        Livewire::test(ListAdmins::class)
            ->assertSee('New user')
            ->mountAction('create')
            ->setActionData([
                'name' => $name = fake()->name(),
                'email' => $email = fake()->unique()->safeEmail(),
                'password' => $password = fake()->password(),
                'password_confirmation' => $password,
                'roles' => $roles,
            ])
            ->callMountedAction()
            ->assertHasNoErrors();

        $newAdmin = AdminResource::getEloquentQuery()->where(['name' => $name, 'email' => $email])->first();
        expect($newAdmin)->not->toBeNull()->name->toBe($name)->email->toBe($email);
        expect($newAdmin->roles()->pluck('id')->toArray())->toEqualCanonicalizing($roles);
    })->with([
        [fn () => [], 'No role.'],
        [fn () => allRoles(), 'All roles.'],
        [fn () => [superAdminRole()->getKey()], 'Super admin role.'],
        [fn () => [normalAdminRole()->getKey()], 'Normal admin role.'],
    ]);

    test('can change his personal info except role', function (array $roles) {
        expect($this->admin->hasRole(normalAdminRole()))->toBeTrue();
        expect($this->admin->hasRole(superAdminRole()))->toBeFalse();
        Livewire::test(ListAdmins::class)
            ->assertCanSeeTableRecords([$this->admin])
            ->callTableAction('edit', $this->admin, [
                'name' => $name = fake()->name(),
                'email' => $email = fake()->unique()->safeEmail(),
                'password' => $password = fake()->password(),
                'password_confirmation' => $password,
                'roles' => $roles,
            ])
            ->assertHasNoErrors();

        $this->admin->refresh();
        expect($this->admin)->name->toBe($name)->email->toBe($email);
        expect(Hash::check($password, $this->admin->password))->toBeTrue();
        expect($this->admin->hasRole(normalAdminRole()))->toBeTrue();
        expect($this->admin->hasRole(superAdminRole()))->toBeFalse();
        expect($this->admin->roles()->pluck('id')->toArray())->toBe([
            normalAdminRole()->getKey(),
        ]);
    })->with([
        [fn () => [], 'No role.'],
        [fn () => allRoles(), 'All roles.'],
        [fn () => [superAdminRole()->getKey()], 'Super admin role.'],
        [fn () => [normalAdminRole()->getKey()], 'Normal admin role.'],
    ]);

    test('admin can change other\'s personal info with role', function (array $roles) {
        $admin = createNormalAdmin();
        expect($admin->hasRole(superAdminRole()))->toBeFalse();
        expect($admin->hasRole(normalAdminRole()))->toBeTrue();
        Livewire::test(ListAdmins::class)
            ->assertCanSeeTableRecords([$admin])
            ->callTableAction('edit', $admin, [
                'name' => $name = fake()->name(),
                'email' => $email = fake()->unique()->safeEmail(),
                'password' => $password = fake()->password(),
                'password_confirmation' => $password,
                'roles' => $roles,
            ])
            ->assertHasNoErrors();

        $admin->refresh();
        expect($admin)->name->toBe($name)->email->toBe($email);
        expect(Hash::check($password, $admin->password))->toBeTrue();
        expect($admin->roles()->pluck('id')->toArray())->toEqualCanonicalizing($roles);
    })->with([
        [fn () => [], 'No role.'],
        [fn () => allRoles(), 'All roles.'],
        [fn () => [superAdminRole()->getKey()], 'Super admin role.'],
        [fn () => [normalAdminRole()->getKey()], 'Normal admin role.'],
    ]);
});
