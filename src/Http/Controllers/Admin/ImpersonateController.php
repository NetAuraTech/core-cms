<?php

namespace Netauratech\CoreCms\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Netauratech\CoreCms\Http\Controllers\AdminController;
use Netauratech\CoreCms\Models\User;

class ImpersonateController extends AdminController
{
    protected array $permissions = [
        'user-edit'   => ['impersonate'],
    ];

    /**
     * Allows you to impersonate a user in order to debug when necessary
     */
    public function impersonate(User $user): RedirectResponse
    {
        if (!session()->has('impersonate')) {
            session(['impersonate' => auth()->id()]);
            Auth::login($user);
        }

        return to_route('profile.index');
    }

    /**
     * Ends spoofing mode
     */
    public function leave(): RedirectResponse
    {
        if (session()->has('impersonate')) {
            $adminId = session('impersonate');
            session()->forget('impersonate');
            Auth::login(User::find($adminId));
        }

        return to_route('admin.user.index');
    }
}