<?php

namespace Netauratech\CoreCms\Tests\Stubs;

use Closure;
use Netauratech\CoreCms\Contracts\ThemeMiddlewareInterface;

class NullThemeMiddleware implements ThemeMiddlewareInterface
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}