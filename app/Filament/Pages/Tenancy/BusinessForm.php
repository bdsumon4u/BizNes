<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

trait BusinessForm
{
    public function getSubheading(): string
    {
        return 'Enter information about your business.';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required(),
                PhoneInput::make('phone')
                    ->required()
                    ->defaultCountry('BD')
                    ->initialCountry('BD'),
            ]);
    }
}
