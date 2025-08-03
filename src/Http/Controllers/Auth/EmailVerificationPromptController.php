<?php

namespace NetAuraTech\CoreCms\Http\Controllers\Auth;

use NetAuraTech\CoreCms\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('profile.index', absolute: false))
                    : view('core-cms::auth.verify-email');
    }
}
