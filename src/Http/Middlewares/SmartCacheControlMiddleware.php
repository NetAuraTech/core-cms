<?php

namespace Netauratech\CoreCms\Http\Middlewares;

use Closure;

class SmartCacheControlMiddleware
{
    private array $config;

    public function __construct()
    {
        $this->config = [
            'enabled' => true,
            'patterns' => [
                'forms' => ['<form', '_token'],
                'inputs' => ['form-control', 'form-group'],
                'interactive' => ['<alert-message', '<puzzle-captcha']
            ],
            'excluded_routes' => ['api/*', 'admin/*', 'esi/*'],
        ];
    }

    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (!$this->config['enabled'] || !$this->shouldProcess($request, $response)) {
            return $response;
        }

        $content = $response->getContent();

        if ($this->detectInteractiveContent($content)) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->headers->set('X-LiteSpeed-Cache-Control', 'no-cache');
            $response->headers->set('X-Cache-Reason', 'form-detected');
        }

        return $response;
    }

    private function shouldProcess($request, $response): bool
    {
        if (!$request->isMethod('GET')) {
            return false;
        }

        foreach ($this->config['excluded_routes'] as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }

        $contentType = $response->headers->get('content-type', '');
        return str_contains($contentType, 'text/html');
    }

    private function detectInteractiveContent($content): bool
    {
        foreach ($this->config['patterns'] as $category => $patterns) {
            foreach ($patterns as $pattern) {
                if (str_contains($content, $pattern)) {
                    return true;
                }
            }
        }

        return false;
    }
}