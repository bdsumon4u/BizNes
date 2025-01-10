<?php

use BezhanSalleh\FilamentShield\Support\Utils;

it('has two super admin roles', function () {
    $roles = Utils::getRoleModel()::all();
    $this->assertCount(2, $roles);

    // $this->assertCount(1, $admins);
    // $this->assertTrue($admins->first()->hasRole(
    //     Utils::getSuperAdminName(),
    // ));
});
