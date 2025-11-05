<?php

namespace Netauratech\CoreCms\Http\Controllers\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Netauratech\CoreCms\Contracts\CacheServiceInterface;
use Netauratech\CoreCms\Http\Controllers\AdminController;
use Netauratech\CoreCms\Models\FailedJob;
use Netauratech\CoreCms\Services\Admin\DashboardManager;

class DashboardController extends AdminController
{
    protected array $permissions = [
        'access-administration' => ['index', 'cache', 'retry_job', 'destroy_job'],
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

    public function cache(CacheServiceInterface $cache): RedirectResponse
    {
        $cache->clear();


        return to_route('admin.dashboard')->with('success', __('core-cms::admin.cache.cleared'));
    }

    public function retry_job(FailedJob $job): RedirectResponse
    {
        Artisan::call('queue:retry ' . $job->uuid);

        return to_route('admin.dashboard')->with('success', __('core-cms::admin.job.relaunch.confirmed'));
    }

    public function destroy_job(FailedJob $job): RedirectResponse
    {
        Artisan::call('queue:forget ' . $job->uuid);

        return to_route('admin.dashboard')->with('success', __('core-cms::admin.job.delete.confirmed'));
    }
}