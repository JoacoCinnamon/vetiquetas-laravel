<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('registrar', [RegisteredUserController::class, 'create'])
        ->name('registrar');
    Route::post('registrar', [RegisteredUserController::class, 'store'])->middleware("throttle:6");

    Route::get('iniciar-sesion', [AuthenticatedSessionController::class, 'create'])
        ->name('iniciar-sesion');
    Route::post('iniciar-sesion', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('cerrar-sesion', [AuthenticatedSessionController::class, 'destroy'])
        ->name('cerrar-sesion');
});
