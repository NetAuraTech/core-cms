<?php

namespace Netauratech\CoreCms\Listeners;

use Illuminate\Support\Facades\Cache;
use Netauratech\CoreCms\Events\OptionUpdated;

class ClearOptionCache
{

    /**
     * Creates the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handles the OptionUpdated event.
     *
     * @param OptionUpdated $event
     * @return void
     */
    public function handle(OptionUpdated $event): void
    {
        $cache = Cache::store('database');
        $cache->forget('options');
    }
}