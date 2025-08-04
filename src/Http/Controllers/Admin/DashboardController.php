<?php

namespace Netauratech\CoreCms\Http\Controllers\Admin;

use Illuminate\Contracts\View\View;
use Netauratech\CoreCms\Http\Controllers\AdminController;
use Netauratech\CoreCms\Services\Admin\DashboardManager;

class DashboardController extends AdminController
{
    protected array $permissions = [
        'access-administration' => ['index'],
    ];

    protected DashboardManager $dashboardManager;

    public function __construct(DashboardManager $dashboardManager)
    {
        parent::__construct();

        $this->dashboardManager = $dashboardManager;
    }

    public function index(): View
    {
        $widgets = $this->dashboardManager->getWidgets();

        return view('core-cms::admin.dashboard', compact('widgets'));
    }
}