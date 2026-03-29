<?php

use App\Livewire\Installer\PanelInstaller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StripeWebhookController;


Route::get('installer', PanelInstaller::class)->name('installer')
    ->withoutMiddleware(['auth']);

Route::get('/', function () {
    return view('home');
})->name('home')->middleware('guest');

Route::get('/store', function () {
    return view('store');
})->name('store');


Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
    Route::post('/checkout/intent', [CheckoutController::class, 'createIntent'])->name('checkout.intent');
    Route::post('/checkout/complete', [CheckoutController::class, 'complete'])->name('checkout.complete');
    Route::get('/order/confirmation', [CheckoutController::class, 'confirmation'])->name('order.confirmation');
});
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);