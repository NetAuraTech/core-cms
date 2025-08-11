<?php

namespace Netauratech\CoreCms\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LangLoaded
{
    use Dispatchable, SerializesModels;

    public string $namespace;

    /**
     * Creates a new event instance.
     *
     * @param string $namespace The loaded namespace
     */
    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }
}
