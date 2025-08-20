<?php

use Illuminate\Support\Facades\Route;
use Netauratech\CoreCms\Http\Controllers\Admin\DashboardController;
use Netauratech\CoreCms\Http\Controllers\Admin\OptionController;

/**
 * Dashboard
 */
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::delete('/cache', [DashboardController::class, 'cache'])->name('cache');
Route::post('/job/{job}/retry', [DashboardController::class, 'retry_job'])->name('retry_job');
Route::delete('/job/{job}/destroy', [DashboardController::class, 'destroy_job'])->name('destroy_job');

/**
 * Options
 */
Route::resource('option', OptionController::class)->except(['show']);