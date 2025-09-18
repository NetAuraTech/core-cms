<?php

namespace Netauratech\CoreCms\Services;

use Illuminate\Support\Facades\Artisan;
use Litespeed\LSCache\LSCache;
use Netauratech\CoreCms\Contracts\CacheServiceInterface;
use Netauratech\CoreCms\Events\CacheCleared;

class CacheService implements CacheServiceInterface
{
    public function clear(): void
    {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');

        LSCache::purgeAll();

        CacheCleared::dispatch();
    }

    public function purgeItems(mixed $items): void
    {
        LSCache::purgeItems($items);
    }
}
