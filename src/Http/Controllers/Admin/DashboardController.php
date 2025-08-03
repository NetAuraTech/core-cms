<?php

namespace NetAuraTech\CoreCms\Http\Controllers\Admin;

use NetAuraTech\CoreCms\Http\Controllers\Controller;
use NetAuraTech\CoreCms\Services\Admin\DashboardManager;

class DashboardController extends Controller
{
    protected $dashboardManager;

    public function __construct(DashboardManager $dashboardManager)
    {
        $this->dashboardManager = $dashboardManager;
    }

    public function index()
    {
        $widgets = $this->dashboardManager->getWidgets();

        return view('core-cms::admin.dashboard', compact('widgets'));
    }
}