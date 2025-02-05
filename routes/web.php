<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('/dashboard', fn () => redirect(Filament::getDefaultPanel()->getUrl()));
