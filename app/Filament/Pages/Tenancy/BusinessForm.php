<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

trait BusinessForm
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    // General Step
                    Wizard\Step::make('General')
                        ->schema([
                            TextInput::make('name')
                                ->label('Business Name')
                                ->required()
                                ->maxLength(255)
                                ->prefixIcon('heroicon-o-building-storefront'),
                            PhoneInput::make('phone')
                                ->label('Business Phone')
                                ->required()
                                ->disallowDropdown()
                                ->defaultCountry('BD')
                                ->initialCountry('BD')
                                ->unique(ignoreRecord: true),
                            TextInput::make('email')
                                ->label('Business Email')
                                ->email()
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->prefixIcon('heroicon-o-envelope')
                                ->columnSpanFull(),
                            Textarea::make('about')
                                ->minLength(50)
                                ->hint('Tell us about your business.')
                                ->columnSpanFull(),
                        ])
                        ->columns(2),

                    // Branding Step
                    Wizard\Step::make('Branding')
                        ->schema([
                            FileUpload::make('logo')
                                ->image()
                                ->directory('business/logos')
                                ->maxSize(512),
                            FileUpload::make('favicon')
                                ->image()
                                ->imageResizeMode('force')
                                ->imageResizeTargetHeight(48)
                                ->imageResizeTargetWidth(48)
                                ->directory('business/favicons')
                                ->maxSize(512),
                        ])
                        ->visibleOn('edit')
                        ->columns(2),

                    // Location Step
                    Wizard\Step::make('Location')
                        ->schema([
                            Textarea::make('street')
                                ->label('Street Address')
                                ->minLength(10)
                                ->required()
                                ->columnSpanFull(),
                            TextInput::make('district')
                                ->label('District')
                                ->required(),
                            TextInput::make('city')
                                ->label('City')
                                ->required(),
                        ])
                        ->hiddenOn('edit')
                        ->columns(2),

                    // Social Media Step
                    Wizard\Step::make('Social Media')
                        ->schema([
                            TextInput::make('facebook')
                                ->label('Facebook')
                                ->prefixIcon('ri-facebook-line')
                                ->placeholder('https://facebook.com/username')
                                ->url(),
                            TextInput::make('twitter')
                                ->label('Twitter')
                                ->prefixIcon('ri-twitter-line')
                                ->placeholder('https://twitter.com/username')
                                ->url(),
                            TextInput::make('instagram')
                                ->label('Instagram')
                                ->prefixIcon('ri-instagram-line')
                                ->placeholder('https://instagram.com/username')
                                ->url(),
                            TextInput::make('tiktok')
                                ->label('TikTok')
                                ->prefixIcon('ri-tiktok-line')
                                ->placeholder('https://tiktok.com/@username')
                                ->url(),
                            TextInput::make('youtube')
                                ->label('YouTube')
                                ->prefixIcon('ri-youtube-line')
                                ->placeholder('https://youtube.com/channel/username')
                                ->url(),
                            TextInput::make('linkedin')
                                ->label('LinkedIn')
                                ->prefixIcon('ri-linkedin-line')
                                ->placeholder('https://linkedin.com/in/username')
                                ->url(),
                        ])
                        ->columns(2),
                ])
                    ->extraAlpineAttributes([
                        'x-on:keydown.enter.prevent' => '
                            isLastStep()
                                ? $el.closest(\'form\').dispatchEvent(new Event(\'submit\', { bubbles: true }))
                                : $wire.dispatchFormEvent(\'wizard::nextStep\', \'data\', getStepIndex(step));
                        ',
                    ])
                    ->submitAction(new HtmlString(Blade::render(<<<'BLADE'
                        <x-filament::button
                            type="submit"
                        >
                            Submit
                        </x-filament::button>
                    BLADE))),
            ]);
    }
}
