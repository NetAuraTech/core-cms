<?php

namespace Netauratech\CoreCms\Http\Controllers\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Netauratech\CoreCms\Http\Requests\Admin\TagFormRequest;
use Netauratech\CoreCms\Models\Tag;
use Netauratech\CoreCms\Http\Controllers\AdminController;

class TagController extends AdminController
{
    protected array $permissions = [
        'tag-list'   => ['index'],
        'tag-create' => ['create', 'store'],
        'tag-edit'   => ['edit', 'update'],
        'tag-delete' => ['destroy'],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $tags = Tag::orderBy('name', 'asc')->paginate(20);
        return view('core-cms::admin.tags.index', [
            'tags' => $tags,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $tag = new Tag();
        return view('core-cms::admin.tags.form', [
            'tag' => $tag,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TagFormRequest $request): RedirectResponse
    {
        $tag = new Tag();
        $tag->fill($request->validated());
        $tag->save();

        return to_route('admin.tags.index')->with('success', __('core-cms::admin.content.tag.created'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag): View
    {
        return view('core-cms::admin.tags.form', [
            'tag' => $tag,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TagFormRequest $request, Tag $tag): RedirectResponse
    {
        $tag->fill($request->validated());
        $tag->save();

        return to_route('admin.tags.index')->with('success', __('core-cms::admin.content.tag.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();
        return to_route('admin.tags.index')->with('success', __('core-cms::admin.content.tag.deleted'));
    }
}
