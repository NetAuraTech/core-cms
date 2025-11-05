<?php


use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Netauratech\CoreCms\Http\Controllers\Api\TaxonomieController;
use Netauratech\CoreCms\Http\Controllers\CaptchaController;

Route::get('captcha/generate', [CaptchaController::class, 'generate'])
    ->name('captcha');

Route::get('csrf', function() {
    return response()->json([
        'token' => csrf_token()
    ])
        ->header('Cache-Control', 'private, max-age=900')
        ->header('X-LiteSpeed-Cache-Control', 'private, max-age=900')
        ->header('X-LiteSpeed-Tag', 'csrf-token')
        ->header('Vary', 'Cookie');
})->name('csrf.token');

Route::get('flash-messages', function () {
    $duration = 10;
    $flashMessages = [];

    $backup = session('_flash_backup', []);

    $getFromSessionOrBackup = function (string $key) use ($backup) {
        return session()->has($key) ? session($key) : ($backup[$key] ?? null);
    };

    if ($message = $getFromSessionOrBackup('success')) {
        $flashMessages[] = [
            'type' => 'success',
            'message' => $message,
            'duration' => $duration,
        ];
    }

    if ($message = $getFromSessionOrBackup('error')) {
        if ($message === 'user-banned') {
            $message = __('core-cms::auth.account.account.banned');
        }
        $flashMessages[] = [
            'type' => 'error',
            'message' => $message,
            'duration' => $duration,
        ];
    }

    if ($message = $getFromSessionOrBackup('warning')) {
        $flashMessages[] = [
            'type' => 'warning',
            'message' => $message,
            'duration' => $duration,
        ];
    }

    if ($message = $getFromSessionOrBackup('info')) {
        $flashMessages[] = [
            'type' => 'info',
            'message' => $message,
            'duration' => $duration,
        ];
    }

    if ($status = $getFromSessionOrBackup('status')) {
        $statusMappings = [
            'verification-link-instruction' => __('core-cms::auth.account.password.reset.instruction'),
            'password-reseted'             => __('core-cms::auth.account.password.reset.confirmed'),
            'password-updated'             => __('core-cms::auth.account.password.updated'),
            'password-defined'             => __('core-cms::auth.account.password.define.defined'),
            'profile-updated'              => __('core-cms::core.profile.updated'),
            'verification-link-sent'       => __('core-cms::core.profile.email.verify.confirmed'),
            'email-verified'               => __('core-cms::core.profile.email.verified'),
            'notification-deleted'         => __('core-cms::core.profile.notifications.deleted'),
            'oauth-link'                   => __('core-cms::core.profile.social.link.confirmed'),
            'oauth-unlink'                 => __('core-cms::core.profile.social.unlink.confirmed'),
        ];

        if (isset($statusMappings[$status])) {
            $flashMessages[] = [
                'type' => 'success',
                'message' => $statusMappings[$status],
                'duration' => $duration,
            ];
        }
    }

    $errors = session()->has('errors') ? session('errors') : ($backup['errors'] ?? null);
    if ($errors && $errors->any()) {
        foreach ($errors->all() as $error) {
            $flashMessages[] = [
                'type' => 'danger',
                'message' => $error,
                'duration' => $duration,
            ];
        }
    }

    if (!empty($backup)) {
        session()->forget('_flash_backup');
    }

    if (empty($flashMessages)) {
        return response()->json([], 204);
    }

    return response()->json($flashMessages)
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0')
        ->header('X-LiteSpeed-Cache-Control', 'no-cache');
})->name('flash.messages');

Route::middleware(['auth'])->group(function () {
    Route::get('/{type}/search', [TaxonomieController::class, 'search'])->name('taxonomie.search')->withoutMiddleware(VerifyCsrfToken::class);
});