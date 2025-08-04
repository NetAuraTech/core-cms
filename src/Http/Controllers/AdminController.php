<?php

namespace Netauratech\CoreCms\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

abstract class AdminController extends Controller
{
    protected array $permissions = [];

    public function __construct()
    {
        if (method_exists(config('auth.providers.users.model'), 'hasPermissionTo')) {
            $this->applyPermissions();
        }
    }

    /**
     * Applies permission middleware based on defined permissions.
     */
    protected function applyPermissions(): void
    {
        foreach ($this->permissions as $permission => $methods) {
            $this->middleware("permission:{$permission}", ['only' => $methods]);
        }
    }
}
