<?php

namespace Netauratech\CoreCms\Http\Controllers\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Netauratech\CoreCms\Http\Requests\Admin\CategoryFormRequest;
use Netauratech\CoreCms\Models\Category;
use Netauratech\CoreCms\Http\Controllers\AdminController;

class CategoryController extends AdminController
{
    protected array $permissions = [
        'category-list'   => ['index'],
        'category-create' => ['create', 'store'],
        'category-edit'   => ['edit', 'update'],
        'category-delete' => ['destroy'],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $categories = Category::orderBy('name', 'asc')->paginate(20);
        return view('core-cms::admin.categories.index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $category = new Category();
        return view('core-cms::admin.categories.form', [
            'category' => $category,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryFormRequest $request): RedirectResponse
    {
        $category = new Category();
        $category->fill($request->validated());
        $category->save();

        return to_route('admin.categories.index')->with('success', __('core-cms::admin.content.category.created'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): View
    {
        return view('core-cms::admin.categories.form', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryFormRequest $request, Category $category): RedirectResponse
    {
        $category->fill($request->validated());
        $category->save();

        return to_route('admin.categories.index')->with('success', __('core-cms::admin.content.category.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();
        return to_route('admin.categories.index')->with('success', __('core-cms::admin.content.category.deleted'));
    }
}
