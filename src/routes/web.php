<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Route;
use Illuminate\Translation\Translator;
use Netauratech\CoreCms\Contracts\ContentProviderInterface;
use Netauratech\CoreCms\Form\FormRegistry;
use Netauratech\CoreCms\Http\Controllers\AssetController;
use Netauratech\CoreCms\Http\Controllers\CaptchaController;
use Netauratech\CoreCms\Http\Controllers\FormSubmissionController;
use Netauratech\CoreCms\Http\Controllers\PageController;
use Netauratech\CoreCms\Http\Controllers\ProfileController;
use Netauratech\CoreCms\Http\Controllers\SeoContentController;
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

/**
 * Pages
 */
Route::get('/', [PageController::class, 'homepage'])->name('home');
Route::get('/sitemap.xml', [SeoContentController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoContentController::class, 'robotsTxt'])->name('robots.txt');
Route::post('/forms/{slug}/{formType}', [FormSubmissionController::class, 'submit'])->name('forms.submit');


if(env('APP_ENV') == 'production') {
    $adminPrefix = config('core-cms.admin.prefix');

    if ($adminPrefix && $adminPrefix !== '/') {
        Route::redirect($adminPrefix, $adminPrefix . '/', 301);
    }
}


Route::fallback(function (ContentProviderInterface $contentProvider, FormRegistry $formRegistry) {
    $slug = request()->path();

    $content = $contentProvider->getContentBySlug($slug);

    if (!$content || $content->type !== 'page' || $content->status !== 'published') {
        abort(404, 'Page introuvable ou non publiée.');
    }

    return view('core-cms::front.page', [
        'content' => $content,
        'isHomepage' => false,
        'metas' => $formRegistry->getFormFields('content_meta'),
    ]);
})->name('page.show');