<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    public function index()
    {
        Gate::authorize('admin');

        $roles = Role::with(['permissions', 'users'])->withCount('users')->get(); // Add withCount
        $permissions = Permission::all();
        $merchants = Merchant::pluck('name', 'id'); // id => name

        return view('admin.roles', compact('roles', 'permissions', 'merchants'));
    }


    public function store(Request $request)
    {
        Gate::authorize('admin');

        $request->validate([
            'role_name' => 'required|string|max:255|unique:roles,name',
            'merchant_id' => 'required|exists:merchants,id',
        ]);

        $merchant = Merchant::findOrFail($request->merchant_id);
        $isGlobal = $merchant->id == 1;

        $finalName = $isGlobal
            ? $request->role_name
            : strtolower(str_replace(' ', '_', $merchant->name)) . '_' . $request->role_name;

        Role::create([
            'name' => $finalName,
            'merchant_id' => $merchant->id,
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }


    public function assignPermission(Request $request, Role $role)
    {
        Gate::authorize('admin');

        $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $permissions = Permission::whereIn('id', $request->permission_ids)->get();

        $role->givePermissionTo($permissions);

        return redirect()->route('admin.roles.index')->with('success', 'Permissions assigned successfully.');
    }

    public function revokePermission(Role $role, Permission $permission)
    {
        Gate::authorize('admin');

        if (!$role->permissions->contains($permission)) {
            return redirect()->back()->with('error', 'Permission not assigned to this role.');
        }

        $role->revokePermissionTo($permission);

        return redirect()->route('admin.roles.index')->with('success', 'Permission revoked successfully.');
    }

    public function destroy(Role $role)
    {
        Gate::authorize('admin');

        // Prevent deleting global 'admin' role
        if ($role->name === 'admin' && $role->merchant_id === 1) {
            return redirect()->back()->with('error', 'Cannot delete the global admin role.');
        }

        $role->permissions()->detach();
        $role->users()->detach();
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
