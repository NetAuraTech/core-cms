<?php

namespace Netauratech\CoreCms\Http\Controllers\Admin;

use Netauratech\CoreCms\Http\Controllers\AdminController;
use Netauratech\CoreCms\Http\Events\OptionUpdated;
use Netauratech\CoreCms\Http\Requests\Admin\OptionContentFormRequest;
use Netauratech\CoreCms\Models\Option;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Netauratech\CoreCms\Contracts\ContentProviderInterface;

class OptionController extends AdminController
{
    protected array $permissions = [
        'option-list'   => ['index'],
        'option-create' => ['create', 'store'],
        'option-edit'   => ['edit', 'update'],
        'option-delete' => ['destroy'],
    ];

    /**
     * @var ContentProviderInterface
     */
    protected ContentProviderInterface $contentProvider;

    public function __construct(ContentProviderInterface $contentProvider)
    {
        parent::__construct();
        $this->contentProvider = $contentProvider;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('core-cms::admin.option.index', [
            'cms_options' => Option::orderBy('created_at', 'desc')->paginate(20),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $option = new Option();
        $option->used_by_cms = false;

        return view('core-cms::admin.option.form', [
            'option' => $option,
            'blogPosts' => $this->contentProvider->getArticles(),
            'pagePosts' => $this->contentProvider->getPages(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OptionContentFormRequest $request): RedirectResponse
    {
        $option = new Option();
        $option->used_by_cms = false;
        $option->created_at = new Carbon();
        $option->fill($request->validated());
        $option->save();

        OptionUpdated::dispatch($option);

        return to_route('admin.option.index')->with('success', __('core-cms::admin.option.created'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Option $option): View
    {
        return view('core-cms::admin.option.form', [
            'option' => $option,
            'articles' => $this->contentProvider->getArticles(),
            'pages' => $this->contentProvider->getPages(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OptionContentFormRequest $request, Option $option): RedirectResponse
    {
        if ($option->used_by_cms) {
            $validated = $request->validated();

            $validated['key'] = $option->key;
            $validated['type'] = $option->type;

            $option->update($validated);
        } else {
            $option->update($request->validated());
        }

        OptionUpdated::dispatch($option);

        Artisan::call('view:clear');

        return to_route('admin.option.index')->with('success', __('core-cms::admin.option.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Option $option): RedirectResponse
    {
        if(!$option->used_by_cms) {
            $option->delete();
            return to_route('admin.option.index')->with('success', __('core-cms::admin.option.deleted'));
        }

        return to_route('admin.option.index')->with('error', __('core-cms::admin.option.cannot_deleted'));
    }
}