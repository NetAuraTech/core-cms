<?php

namespace Netauratech\CoreCms\Http\Controllers\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Netauratech\CoreCms\Http\Controllers\AdminController;
use Netauratech\CoreCms\Http\Requests\Admin\UserFormRequest;
use Netauratech\CoreCms\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends AdminController
{
    protected array $permissions = [
        'user-list'   => ['index'],
        'user-create' => ['create', 'store'],
        'user-edit'   => ['edit', 'update', 'ban', 'unban', 'confirm'],
        'user-delete' => ['destroy'],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('core-cms::admin.users.index', [
            'users' => User::orderBy('created_at', 'desc')->paginate(20),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = new User();

        return view('core-cms::admin.users.form', [
            'user' => $user,
            'roles' => Role::all(),
            'userRoles' => [],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserFormRequest $request): RedirectResponse
    {
        $user = new User();
        $user->fill($request->validated());
        if ($request->validated('new_password')) {
            $user->password = Hash::make($request->validated('new_password'));
        }

        $roleIds = $request->input('role', []);

        $roles = Role::whereIn('id', $roleIds)->pluck('name')->toArray();
        $user->syncRoles($roles);

        $user->save();

        return to_route('admin.user.index')->with('success', __('core-cms::admin.user.created'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        $userRoles = DB::table('model_has_roles')->where('model_has_roles.model_type', User::class)->where('model_has_roles.model_id', $user->id)
            ->pluck('model_has_roles.role_id', 'model_has_roles.role_id')
            ->all();

        return view('core-cms::admin.users.form', [
            'user' => $user,
            'roles' => Role::all(),
            'userRoles' => $userRoles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserFormRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());
        if ($request->validated('new_password')) {
            $user->password = Hash::make($request->validated('new_password'));
            $user->save();
        }

        $roleIds = $request->input('role', []);

        $roles = Role::whereIn('id', $roleIds)->pluck('name')->toArray();
        $user->syncRoles($roles);

        return to_route('admin.user.index')->with('success', __('core-cms::admin.user.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return to_route('admin.user.index')->with('success', __('core-cms::admin.user.deleted'));
    }

    public function ban(User $user): RedirectResponse
    {
        $user->status = 0;
        $user->save();

        return to_route('admin.user.index')->with('success', __('core-cms::admin.user.ban.confirmed'));
    }

    public function unban(User $user): RedirectResponse
    {
        $user->status = 1;
        $user->save();

        return to_route('admin.user.index')->with('success', __('core-cms::admin.user.unban.confirmed'));
    }

    public function confirm(User $user): RedirectResponse
    {
        $user->email_verified_at = now();
        $user->save();

        return to_route('admin.user.index')->with('success', __('core-cms::admin.user.confirmed'));
    }
}