<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Route;
use Illuminate\Translation\Translator;
use Netauratech\CoreCms\Http\Controllers\AssetController;
use Netauratech\CoreCms\Http\Controllers\CaptchaController;
use Netauratech\CoreCms\Http\Controllers\ProfileController;
use Netauratech\CoreCms\Services\AssetManager;

Route::get('js/translations.js', function (AssetManager $assetManager, Translator $translator) {
    $lang = config('app.locale');
    $cache = Cache::store('database');
    $strings = $cache->rememberForever('lang_'.$lang.'.js', function () use($lang, $assetManager, $translator) {
        $allStrings = [];

        $appLangPath = base_path('lang/' . $lang);
        if (File::isDirectory($appLangPath)) {
            foreach (File::files($appLangPath) as $file) {
                if ($file->getExtension() === 'php') {
                    $group = $file->getBasename('.php');
                    $allStrings[$group] = Lang::get($group, [], $lang);
                }
            }
        }

        foreach ($assetManager->getTranslationPaths() as $namespace => $packageLangBasePath) {
            $specificLangPath = $packageLangBasePath . DIRECTORY_SEPARATOR . $lang;
            if (File::isDirectory($specificLangPath)) {
                foreach (File::files($specificLangPath) as $file) {
                    if ($file->getExtension() === 'php') {
                        $group = $file->getBasename('.php');
                        $allStrings[$namespace][$group] = Lang::get($namespace . '::' . $group, [], $lang);
                    }
                }
            }
        }

        return $allStrings;
    });
    header('Content-Type: text/javascript');
    echo('window.i18n = ' . json_encode($strings) . ';');
    exit();
})->name('translations');

/**
 * Assets
 */
Route::get('/assets/{path}', [AssetController::class, 'show'])
    ->where('path', '.*')
    ->name('assets.show');

/**
 * Captcha
 */
Route::middleware(['lscache:no-cache'])->group(function () {
    Route::get('/captcha/{key}', [CaptchaController::class, 'show'])->name('captcha.image');
    Route::post('/captcha/check', [CaptchaController::class, 'check'])->name('captcha.check')->withoutMiddleware(VerifyCsrfToken::class);
});

/**
 * Profile
 */
Route::middleware(['auth', 'lscache:private;'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/clean-notifications', [ProfileController::class, 'cleanNotification'])->name('profile.clean-notification');
});