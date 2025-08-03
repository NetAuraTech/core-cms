<?php

use Illuminate\Support\Facades\Route;
use NetAuraTech\CoreCms\Http\Controllers\Admin\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');