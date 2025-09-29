<?php

namespace Netauratech\CoreCms\Contracts;

use Closure;
use Illuminate\Http\Request;

interface ThemeMiddlewareInterface
{
    /**
     * Handle an incoming request and load the appropriate theme.
     */
    public function handle(Request $request, Closure $next);
}