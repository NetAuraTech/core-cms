<?php

namespace Netauratech\CoreCms\Http\Controllers\Auth;

use Illuminate\Contracts\View\View;
use Netauratech\CoreCms\Http\Controllers\Controller;
use Netauratech\CoreCms\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{

    public function connect(string $service): RedirectResponse
    {
        return Socialite::driver($service)->redirect();
    }

    public function callback(string $service): RedirectResponse
    {
        $method = $service . '_id';
        $user = Socialite::driver($service)->user();

        $auth = Auth::user();

        if ($auth) {
            $auth->$method = $user->id;
            $auth->save();
            Auth::login($auth);
            return Redirect::route('profile.index')->with('status', 'oauth-link');
        } else {
            $isNew = $this->registerOrLogin($user, $service);
            if ($isNew) {
                return Redirect::route('oauth.define-password');
            }
        }

        return redirect()->route('profile.index');
    }

    protected function registerOrLogin($incomingUser, string $service): bool
    {
        $method = $service . '_id';

        $user = User::where($method, $incomingUser->id)->first();

        $isNew = false;

        if (!$user) {
            $user = new User();
            $user->username = $incomingUser->name;
            $user->email = $incomingUser->email;
            $user->status = 1;
            $user->$method = $incomingUser->id;
            $user->password = encrypt('password');
            $user->save();
            $isNew = true;
        }

        Auth::login($user);
        return $isNew;
    }

    public function defineOauthPassword(): View
    {
        return view('core-cms::auth.define-password');
    }

    public function unlink(string $service): RedirectResponse
    {
        $method = $service . '_id';

        $user = Auth::user();
        $user->$method = null;
        $user->save();
        Auth::login($user);

        return Redirect::route('profile.index')->with('status', 'oauth-unlink');
    }
}
