<?php

namespace Netauratech\CoreCms\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CacheCleared
{
    use Dispatchable, SerializesModels;

    public function __construct()
    {
    }
}
