<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Business;
use BezhanSalleh\FilamentShield\FilamentShield;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;

class RegisterBusiness extends RegisterTenant
{
    protected ?bool $hasDatabaseTransactions = true;

    public static function getLabel(): string
    {
        return 'Register Business';
    }
 
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
            ]);
    }
 
    protected function handleRegistration(array $data): Business
    {
        $business = Business::create($data);
 
        $business->users()->attach(Filament::auth()->user());
        
        // FilamentShield::createRole(tenantId: $business->id)->users()->attach(
        //     Filament::auth()->user(),
        // );
        
        // temporary: get session team_id for restore at end
        $session_team_id = getPermissionsTeamId();
        // set actual new team_id to package instance
        setPermissionsTeamId($business);
        // get the admin user and assign roles/permissions on new team model
        Filament::auth()->user()->assignRole(
            FilamentShield::createRole(tenantId: $business->id),
        );
        // restore session team_id to package instance using temporary value stored above
        setPermissionsTeamId($session_team_id);
 
        return $business;
    }
}