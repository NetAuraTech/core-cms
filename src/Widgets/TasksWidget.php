<?php

namespace Netauratech\CoreCms\Widgets;

use Illuminate\Contracts\View\View;
use Netauratech\CoreCms\Models\FailedJob;

class TasksWidget
{
    public function render(): View
    {
        return view('core-cms::widgets.tasks', [
            'failed_jobs' => FailedJob::all(),
        ]);
    }
}