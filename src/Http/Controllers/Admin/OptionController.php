<?php

namespace Netauratech\CoreCms\Http\Controllers\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Netauratech\CoreCms\Contracts\ContentProviderInterface;
use Netauratech\CoreCms\Events\OptionUpdated;
use Netauratech\CoreCms\Form\FormRegistry;
use Netauratech\CoreCms\Http\Controllers\AdminController;
use Netauratech\CoreCms\Http\Requests\Admin\OptionContentFormRequest;
use Netauratech\CoreCms\Models\Option;

class OptionController extends AdminController
{
    protected array $permissions = [
        'option-list'   => ['index'],
        'option-create' => ['create', 'store'],
        'option-edit'   => ['edit', 'update'],
        'option-delete' => ['destroy'],
    ];

    protected ContentProviderInterface $contentProvider;
    protected FormRegistry $formRegistry;

    public function __construct(ContentProviderInterface $contentProvider, FormRegistry $formRegistry)
    {
        parent::__construct();
        $this->contentProvider = $contentProvider;
        $this->formRegistry = $formRegistry;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $groupedOptions = Option::orderBy('category')
            ->where('type', '!=', 'theme')
            ->orderBy('key')
            ->get()
            ->groupBy('category');

        $categoryOrder = [
            'custom',
            'general',
            'branding',
            'content_settings',
            'seo',
            'schedule',
            'contact_emails',
            'social_media',
            'security'
        ];

        $structuredGroups = $groupedOptions->map(function ($options, $categoryKey) {
            return (object) [
                'key' => $categoryKey,
                'label' => __('core-cms::admin.option.category.' . $categoryKey),
                'options' => $options,
            ];
        })->sortBy(function ($group) use ($categoryOrder) {
            $index = array_search($group->key, $categoryOrder);
            return $index === false ? PHP_INT_MAX : $index;
        })->values();

        $formFields = $this->formRegistry->getFormFields('option_media');

        return view('core-cms::admin.option.index', [
            'groupedOptions' => $structuredGroups,
            'formFields' => $formFields,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $option = new Option();
        $option->category = 'custom';

        $formFields = $this->formRegistry->getFormFields('option_media');

        return view('core-cms::admin.option.form', [
            'option' => $option,
            'articles' => $this->contentProvider->getContents('article'),
            'pages' => $this->contentProvider->getContents('page'),
            'templates' => $this->contentProvider->getContents('template'),
            'formFields' => $formFields,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OptionContentFormRequest $request): RedirectResponse
    {
        $option = new Option();
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
        $formFields = $this->formRegistry->getFormFields('option_media');

        return view('core-cms::admin.option.form', [
            'option' => $option,
            'articles' => $this->contentProvider->getContents('article'),
            'pages' => $this->contentProvider->getContents('page'),
            'templates' => $this->contentProvider->getContents('template'),
            'formFields' => $formFields
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OptionContentFormRequest $request, Option $option): RedirectResponse
    {
        if ($option->category !== 'custom') {
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
        if($option->category === 'custom') {
            $option->delete();
            OptionUpdated::dispatch($option);
            return to_route('admin.option.index')->with('success', __('core-cms::admin.option.deleted'));
        }

        return to_route('admin.option.index')->with('error', __('core-cms::admin.option.cannot_deleted'));
    }
}