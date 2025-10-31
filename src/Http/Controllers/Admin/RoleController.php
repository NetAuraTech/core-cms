<?php

namespace Netauratech\CoreCms\Http\Controllers\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Netauratech\CoreCms\Http\Controllers\AdminController;
use Netauratech\CoreCms\Http\Requests\Admin\UserFormRequest;
use Netauratech\CoreCms\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends AdminController
{
    protected array $permissions = [
        'role-list'   => ['index'],
        'role-create' => ['create', 'store'],
        'role-edit'   => ['edit', 'update'],
        'role-delete' => ['destroy'],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $roles = Role::orderBy('id', 'DESC')->paginate(5);

        return view('core-cms::admin.roles.index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $role = new Role();
        $permission = Permission::get();

        return view('core-cms::admin.roles.form', [
            'role' => $role,
            'permission' => $permission,
            'rolePermissions' => [],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $role = Role::create(['name' => $request->input('name')]);

        $permissionIds = $request->input('permission', []);
        $permissions = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();
        $role->syncPermissions($permissions);

        return redirect()->route('admin.role.index')
            ->with('success', __('core-cms::admin.role.created'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role): View
    {
        $permission = Permission::get();
        $rolePermissions = DB::table('role_has_permissions')->where('role_has_permissions.role_id', $role->id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return view('core-cms::admin.roles.form', [
            'role' => $role,
            'permission' => $permission,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name,' . $role->id,
            'permission' => 'required',
        ]);

        $role->name = $request->input('name');
        $role->save();

        $permissionIds = $request->input('permission', []);

        $permissions = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();
        $role->syncPermissions($permissions);

        return redirect()->route('admin.role.index')
            ->with('success', __('core-cms::admin.role.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();

        return to_route('admin.role.index')->with('success', __('core-cms::admin.role.deleted'));
    }
}