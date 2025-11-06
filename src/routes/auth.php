<?php

use Illuminate\Support\Facades\Route;
use Netauratech\CoreCms\Http\Controllers\Auth\AuthenticatedSessionController;
use Netauratech\CoreCms\Http\Controllers\Auth\ConfirmablePasswordController;
use Netauratech\CoreCms\Http\Controllers\Auth\EmailVerificationNotificationController;
use Netauratech\CoreCms\Http\Controllers\Auth\EmailVerificationPromptController;
use Netauratech\CoreCms\Http\Controllers\Auth\NewPasswordController;
use Netauratech\CoreCms\Http\Controllers\Auth\PasswordController;
use Netauratech\CoreCms\Http\Controllers\Auth\PasswordResetLinkController;
use Netauratech\CoreCms\Http\Controllers\Auth\RegisteredUserController;
use Netauratech\CoreCms\Http\Controllers\Auth\SocialController;
use Netauratech\CoreCms\Http\Controllers\Auth\VerifyEmailController;

Route::middleware(['lscache:no-cache','guest'])->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware(['lscache:no-cache', 'auth'])->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

Route::middleware(['lscache:no-cache'])->prefix('oauth')->name('oauth.')->group(function () {
    Route::get('define-password', [SocialController::class, 'defineOauthPassword'])->name('define-password');
    Route::get('{service}', [SocialController::class, 'connect'])->name('connect');
    Route::get('{service}/unlink', [SocialController::class, 'unlink'])->name('unlink');
    Route::get('{service}/callback', [SocialController::class, 'callback'])->name('callback');
});