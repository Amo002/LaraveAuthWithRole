<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    public function index()
    {
        $merchant = auth()->user()->merchant;


        app(PermissionRegistrar::class)->setPermissionsTeamId($merchant->id);

        $roles = Role::where('merchant_id', $merchant->id)->with('permissions')->get();

        $adminRole = Role::where('name', 'merchant_admin')
            ->where('merchant_id', $merchant->id)
            ->with('permissions')
            ->first();

        $availablePermissions = $adminRole?->permissions ?? collect();

        return view('merchant.roles', [
            'merchant' => $merchant,
            'roles' => $roles,
            'availablePermissions' => $availablePermissions
        ]);
    }

    public function store(Request $request)
    {
        $merchant = auth()->user()->merchant;


        app(PermissionRegistrar::class)->setPermissionsTeamId($merchant->id);

        $validated = $request->validate([
            'role_name' => 'required|string|max:50',
        ]);

        $fullRoleName = strtolower($merchant->name) . '_' . Str::slug($validated['role_name'], '_');

        if (Role::where('name', $fullRoleName)->where('merchant_id', $merchant->id)->exists()) {
            return redirect()->back()->with('error', 'Role already exists.');
        }

        Role::create([
            'name' => $fullRoleName,
            'merchant_id' => $merchant->id,
        ]);

        return redirect()->route('merchant.roles.index')->with('success', 'Role created successfully.');
    }

    public function assignPermission(Request $request, Role $role)
    {
        $merchant = auth()->user()->merchant;


        app(PermissionRegistrar::class)->setPermissionsTeamId($merchant->id);

        if ($role->merchant_id !== $merchant->id) {
            return redirect()->back()->with('error', 'Unauthorized role access.');
        }

        $permissionIds = $request->input('permission_ids', []);

        if (empty($permissionIds)) {
            return redirect()->back()->with('error', 'No permissions selected.');
        }

        $permissions = Permission::whereIn('id', $permissionIds)->get();
        $role->givePermissionTo($permissions);

        return redirect()->route('merchant.roles.index')->with('success', 'Permissions assigned successfully.');
    }

    public function revokePermission(Role $role, Permission $permission)
    {
        $merchant = auth()->user()->merchant;


        app(PermissionRegistrar::class)->setPermissionsTeamId($merchant->id);

        if ($role->merchant_id !== $merchant->id || !$role->permissions->contains($permission)) {
            return redirect()->back()->with('error', 'Unauthorized or invalid permission.');
        }

        $role->revokePermissionTo($permission);

        return redirect()->route('merchant.roles.index')->with('success', 'Permission revoked successfully.');
    }

    public function destroy(Role $role)
    {
        $merchant = auth()->user()->merchant;

        if ($role->merchant_id !== $merchant->id) {
            return redirect()->back()->with('error', 'Unauthorized role access.');
        }

        if (strtolower($role->name) === 'merchant_admin') {
            return redirect()->back()->with('error', 'Cannot delete the merchant_admin role.');
        }

        $role->permissions()->detach();
        $role->users()->detach();
        $role->delete();

        return redirect()->route('merchant.roles.index')->with('success', 'Role deleted successfully.');
    }
}
