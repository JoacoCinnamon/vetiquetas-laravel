<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('registrar', [RegisteredUserController::class, 'create'])
        ->name('registrar');
    Route::post('registrar', [RegisteredUserController::class, 'store'])->middleware("throttle:6");

    Route::get('iniciar-sesion', [AuthenticatedSessionController::class, 'create'])
        ->name('iniciar-sesion');
    Route::post('iniciar-sesion', [AuthenticatedSessionController::class, 'store']);

    // Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    // Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    // Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    // Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    /**
     * Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
     * Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
     *       ->middleware(['signed', 'throttle:6,1'])
     *       ->name('verification.verify');
     */

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('cerrar-sesion', [AuthenticatedSessionController::class, 'destroy'])
        ->name('cerrar-sesion');
});
