<?php

use Illuminate\Support\Facades\Route;
use Netauratech\CoreCms\Http\Controllers\Admin\DashboardController;
use Netauratech\CoreCms\Http\Controllers\Admin\OptionController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

/**
 * Options
 */
Route::resource('option', OptionController::class)->except(['show']);