<?php

namespace Netauratech\CoreCms\Services;

use Illuminate\Support\Facades\Artisan;
use Litespeed\LSCache\LSCache;
use Netauratech\CoreCms\Events\CacheCleared;

class CacheService
{
    public function clear(): void
    {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');

        CacheCleared::dispatch();
    }

    public function purgeItems(mixed $items)
    {
        LSCache::purgeItems($items);
    }
}
