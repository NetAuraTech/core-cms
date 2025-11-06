<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Netauratech\CoreCms\Http\Controllers\Admin\CategoryController;
use Netauratech\CoreCms\Http\Controllers\Admin\ContentController;
use Netauratech\CoreCms\Http\Controllers\Admin\DashboardController;
use Netauratech\CoreCms\Http\Controllers\Admin\OptionController;
use Netauratech\CoreCms\Http\Controllers\Admin\ImpersonateController;
use Netauratech\CoreCms\Http\Controllers\Admin\RoleController;
use Netauratech\CoreCms\Http\Controllers\Admin\TagController;
use Netauratech\CoreCms\Http\Controllers\Admin\UserController;

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

/**
 * Users
 */
Route::resource('user', UserController::class)->except(['show']);
Route::post('/user/{user:id}/ban', [UserController::class, 'ban'])->where(['user' => '[0-9]+'])->name('user.ban');
Route::post('/user/{user:id}/unban', [UserController::class, 'unban'])->where(['user' => '[0-9]+'])->name('user.unban');
Route::post('/user/{user:id}/confirm', [UserController::class, 'confirm'])->where(['user' => '[0-9]+'])->name('user.confirm');
Route::get('/user/{user}/impersonate', [ImpersonateController::class, 'impersonate'])->where(['user' => '[0-9]+'])->name('user.impersonate');
Route::get('/user/impersonate/leave', [ImpersonateController::class, 'leave'])->where(['user' => '[0-9]+'])->name('user.impersonate.leave');

/**
 * Roles
 */
Route::resource('role', RoleController::class)->except(['show']);

/**
 * Content (page, article, template, ...)
 */
Route::get('contents/{type}', [ContentController::class, 'index'])->name('contents.index');
Route::get('contents/{type}/create', [ContentController::class, 'create'])->name('contents.create');
Route::post('contents/{type}', [ContentController::class, 'store'])->name('contents.store');
Route::get('contents/{content}/edit', [ContentController::class, 'edit'])->name('contents.edit');
Route::put('contents/{content}', [ContentController::class, 'update'])->name('contents.update');
Route::delete('contents/{content}', [ContentController::class, 'destroy'])->name('contents.destroy');
Route::post('contents/{type}/preview', [ContentController::class, 'preview'])->name('contents.preview')->withoutMiddleware(VerifyCsrfToken::class);

/**
 * Categories
 */
Route::resource('categories', CategoryController::class);

/**
 * Tags
 */
Route::resource('tags', TagController::class);