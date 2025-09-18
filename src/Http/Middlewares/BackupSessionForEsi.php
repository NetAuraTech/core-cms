<?php

namespace Netauratech\CoreCms\Http\Middlewares;

use Closure;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class BackupSessionForEsi
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle($request, Closure $next)
    {
        $backup = [];

        $flashKeys = ['error', 'success', 'warning', 'info', 'status', 'message'];

        foreach ($flashKeys as $key) {
            if (session()->has($key)) {
                $backup[$key] = session()->get($key);
            }
        }

        if ($request->routeIs('login') && session()->has('errors')) {
            $backup['errors'] = session()->get('errors');
        }

        if (!empty($backup)) {
            session(['_flash_backup' => $backup]);
        }

        return $next($request);
    }
}