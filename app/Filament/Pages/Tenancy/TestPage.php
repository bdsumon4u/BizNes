<?php

namespace App\Filament\Pages\Tenancy;

use App\Filament\Clusters\BusinessProfile;
use Filament\Pages\Page;

class TestPage extends Page
{
        protected static ?string $cluster = BusinessProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.test-page';
}
