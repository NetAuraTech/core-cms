<?php

namespace Netauratech\CoreCms\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class ContentSaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Model $content;
    public Request $request;

    /**
     * Creates a new event instance.
     *
     * @param Model $content The Content instance that has been saved or updated.
     * @param Request $request The complete HTTP request.
     */
    public function __construct(Model $content, Request $request)
    {
        $this->content = $content;
        $this->request = $request;
    }
}