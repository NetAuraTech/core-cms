<?php

namespace Netauratech\CoreCms\Http\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Netauratech\CoreCms\Models\Option;

class OptionUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * The instance of the option that has been updated.
     *
     * @var Option
     */
    public Option $option;

    /**
     * Creates a new event instance.
     *
     * @param Option $option
     */
    public function __construct(Option $option)
    {
        $this->option = $option;
    }
}