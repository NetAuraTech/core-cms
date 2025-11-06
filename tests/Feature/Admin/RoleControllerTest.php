<?php

namespace Netauratech\CoreCms\Tests\Feature\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Netauratech\CoreCms\Tests\TestCase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_guest_cannot_access_role_index(): void
    {
        $response = $this->get(route('admin.role.index'));
        $response->assertStatus(403);
    }

    /** @test */
    public function test_authenticated_user_can_view_roles(): void
    {
        $user = $this->actingAsUser(1);
        $role = Role::create(['name' => 'Test Role']);

        $response = $this->get(route('admin.role.index'));

        $response->assertOk();
        $response->assertSee('Test Role');
    }

    /** @test */
    public function test_it_shows_create_form(): void
    {
        $user = $this->actingAsUser(1);
        $response = $this->get(route('admin.role.create'));

        $response->assertOk();
        $response->assertViewIs('core-cms::admin.roles.form');
    }

    /** @test */
    public function test_it_can_create_role_with_permissions(): void
    {
        $user = $this->actingAsUser(1);
        $permission1 = Permission::create(['name' => 'test-permission-1']);
        $permission2 = Permission::create(['name' => 'test-permission-2']);

        $response = $this->post(route('admin.role.store'), [
            'name' => 'New Role',
            'permission' => [$permission1->id, $permission2->id],
        ]);

        $response->assertRedirect(route('admin.role.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('roles', ['name' => 'New Role']);

        $role = Role::where('name', 'New Role')->first();
        $this->assertTrue($role->hasPermissionTo('test-permission-1'));
        $this->assertTrue($role->hasPermissionTo('test-permission-2'));
    }

    /** @test */
    public function test_it_validates_role_name_is_required(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.role.store'), [
            'name' => '',
            'permission' => [],
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function test_it_validates_role_name_is_unique(): void
    {
        $user = $this->actingAsUser(1);
        Role::create(['name' => 'Existing Role']);

        $response = $this->post(route('admin.role.store'), [
            'name' => 'Existing Role',
            'permission' => [],
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function test_it_validates_permissions_are_required(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.role.store'), [
            'name' => 'Test Role',
        ]);

        $response->assertSessionHasErrors(['permission']);
    }

    /** @test */
    public function test_it_shows_edit_form(): void
    {
        $user = $this->actingAsUser(1);
        $role = Role::create(['name' => 'Test Role']);

        $response = $this->get(route('admin.role.edit', $role));

        $response->assertOk();
        $response->assertViewIs('core-cms::admin.roles.form');
        $response->assertViewHas('role', $role);
    }

    /** @test */
    public function test_edit_form_displays_existing_permissions(): void
    {
        $user = $this->actingAsUser(1);
        $permission = Permission::create(['name' => 'test-permission']);
        $role = Role::create(['name' => 'Test Role']);
        $role->givePermissionTo($permission);

        $response = $this->get(route('admin.role.edit', $role));

        $response->assertOk();
        $rolePermissions = $response->viewData('rolePermissions');
        $this->assertArrayHasKey($permission->id, $rolePermissions);
    }

    /** @test */
    public function test_it_can_update_role(): void
    {
        $user = $this->actingAsUser(1);
        $role = Role::create(['name' => 'Old Name']);
        $permission1 = Permission::create(['name' => 'permission-1']);
        $permission2 = Permission::create(['name' => 'permission-2']);

        $response = $this->put(route('admin.role.update', $role), [
            'name' => 'Updated Name',
            'permission' => [$permission1->id, $permission2->id],
        ]);

        $response->assertRedirect(route('admin.role.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Updated Name',
        ]);

        $role->refresh();
        $this->assertTrue($role->hasPermissionTo('permission-1'));
        $this->assertTrue($role->hasPermissionTo('permission-2'));
    }

    /** @test */
    public function test_update_validates_unique_name_except_current(): void
    {
        $user = $this->actingAsUser(1);
        $role1 = Role::create(['name' => 'Role 1']);
        $role2 = Role::create(['name' => 'Role 2']);
        $permission = Permission::create(['name' => 'test-perm']);

        $response = $this->put(route('admin.role.update', $role1), [
            'name' => 'Role 2',
            'permission' => [$permission->id],
        ]);

        $response->assertSessionHasErrors(['name']);

        $response = $this->put(route('admin.role.update', $role1), [
            'name' => 'Role 1',
            'permission' => [$permission->id],
        ]);

        $response->assertRedirect(route('admin.role.index'));
        $response->assertSessionDoesntHaveErrors(['name']);
    }

    /** @test */
    public function test_it_can_remove_permissions_from_role(): void
    {
        $user = $this->actingAsUser(1);
        $permission1 = Permission::create(['name' => 'permission-1']);
        $permission2 = Permission::create(['name' => 'permission-2']);
        $role = Role::create(['name' => 'Test Role']);
        $role->givePermissionTo([$permission1, $permission2]);

        $response = $this->put(route('admin.role.update', $role), [
            'name' => 'Test Role',
            'permission' => [$permission1->id],
        ]);

        $role->refresh();
        $this->assertTrue($role->hasPermissionTo('permission-1'));
        $this->assertFalse($role->hasPermissionTo('permission-2'));
    }

    /** @test */
    public function test_it_can_delete_role(): void
    {
        $user = $this->actingAsUser(1);
        $role = Role::create(['name' => 'Deletable Role']);

        $response = $this->delete(route('admin.role.destroy', $role));

        $response->assertRedirect(route('admin.role.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    /** @test */
    public function test_deleting_role_removes_user_associations(): void
    {
        $user = $this->actingAsUser(1);
        $role = Role::create(['name' => 'Deletable Role']);
        $testUser = $this->createUser();
        $testUser->assignRole($role);

        $this->assertTrue($testUser->hasRole('Deletable Role'));

        $response = $this->delete(route('admin.role.destroy', $role));

        $testUser->refresh();
        $this->assertFalse($testUser->hasRole('Deletable Role'));
    }

    /** @test */
    public function test_roles_are_paginated(): void
    {
        $user = $this->actingAsUser(1);

        for ($i = 1; $i <= 7; $i++) {
            Role::create(['name' => "Role $i"]);
        }

        $response = $this->get(route('admin.role.index'));

        $response->assertOk();
        $response->assertViewHas('roles');

        $roles = $response->viewData('roles');
        $this->assertEquals(5, $roles->perPage());
        $this->assertGreaterThan(1, $roles->lastPage());
    }

    /** @test */
    public function test_roles_are_ordered_by_id_desc(): void
    {
        $user = $this->actingAsUser(1);

        $role1 = Role::create(['name' => 'First Role']);
        $role2 = Role::create(['name' => 'Second Role']);

        $response = $this->get(route('admin.role.index'));

        $roles = $response->viewData('roles');
        $this->assertEquals('Second Role', $roles->first()->name);
    }

    /** @test */
    public function test_user_without_permission_cannot_access_roles(): void
    {
        $regularUser = $this->createUser();
        $this->actingAs($regularUser);

        $response = $this->get(route('admin.role.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_user_without_permission_cannot_create_roles(): void
    {
        $regularUser = $this->createUser();
        $this->actingAs($regularUser);
        $permission = Permission::create(['name' => 'test-perm']);

        $response = $this->post(route('admin.role.store'), [
            'name' => 'New Role',
            'permission' => [$permission->id],
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function test_user_without_permission_cannot_delete_roles(): void
    {
        $regularUser = $this->createUser();
        $this->actingAs($regularUser);
        $role = Role::create(['name' => 'Test Role']);

        $response = $this->delete(route('admin.role.destroy', $role));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_it_can_assign_role_to_existing_users(): void
    {
        $user = $this->actingAsUser(1);
        $role = Role::create(['name' => 'New Role']);
        $permission = Permission::create(['name' => 'test-perm']);
        $role->givePermissionTo($permission);

        $testUser = $this->createUser();
        $testUser->assignRole($role);

        $this->assertTrue($testUser->hasRole('New Role'));
        $this->assertTrue($testUser->can('test-perm'));
    }

    /** @test */
    public function test_all_permissions_are_displayed_in_form(): void
    {
        $user = $this->actingAsUser(1);
        $data = Permission::all();

        $response = $this->get(route('admin.role.create'));

        $response->assertOk();
        $permissions = $response->viewData('permission');
        $this->assertCount(count($data), $permissions);
    }
}