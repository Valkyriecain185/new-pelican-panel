<?php

use App\Livewire\Installer\PanelInstaller;
use Illuminate\Support\Facades\Route;

Route::get('installer', PanelInstaller::class)->name('installer')
    ->withoutMiddleware(['auth']);

Route::get('/', function () {
    return view('home');
})->name('home')->middleware('guest');