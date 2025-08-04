<?php

use Illuminate\Support\Facades\Route;
use Netauratech\CoreCms\Http\Controllers\Admin\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');