<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Business;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;

class RegisterBusiness extends RegisterTenant
{
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
 
        return $business;
    }
}