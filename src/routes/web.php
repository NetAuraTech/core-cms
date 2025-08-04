<?php

/**
 * Profile
 */

use Illuminate\Support\Facades\Route;
use Netauratech\CoreCms\Http\Controllers\ProfileController;

Route::middleware(['auth', 'lscache:no-cache'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/clean-notifications', [ProfileController::class, 'cleanNotification'])->name('profile.clean-notification');
});