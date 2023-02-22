<?php

use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Auth;
use \App\Http\Controllers\MainController;

Route::get('/', function () {
    if(Auth::user()) {
        return redirect(RouteServiceProvider::HOME);
    }

    $title = 'Авторизация';
    return view('index', compact('title'));
})->name('index');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [MainController::class, 'dashboard'])->name('dashboard');
    Route::get('/order/{id}', [MainController::class, 'order'])->name('order');
});

Route::get('/yandex/map/geo', [MainController::class, 'map']);

require __DIR__.'/auth.php';
