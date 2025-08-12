<?php


use Illuminate\Support\Facades\Route;
use Netauratech\CoreCms\Http\Controllers\CaptchaController;

Route::get('captcha/generate', [CaptchaController::class, 'generate'])
    ->name('captcha');