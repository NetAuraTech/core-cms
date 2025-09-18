<?php

namespace Netauratech\CoreCms\Contracts;

interface CacheServiceInterface
{
    public function clear(): void;

    public function purgeItems(mixed $items): void;
}