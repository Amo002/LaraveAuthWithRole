<?php

namespace App\Services\Admin;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleService
{
    public function listRoles()
    {
        return Role::with(['permissions', 'users'])->withCount('users')->get();
    }

    public function listPermissions()
    {
        return Permission::all();
    }

    public function listMerchants()
    {
        return Merchant::pluck('name', 'id');
    }

    public function createRole(array $data): Role
    {
        $merchant = Merchant::findOrFail($data['merchant_id']);
        $isGlobal = $merchant->id == 1;

        $finalName = $isGlobal
            ? $data['role_name']
            : strtolower(str_replace(' ', '_', $merchant->name)) . '_' . $data['role_name'];

        return Role::create([
            'name' => $finalName,
            'merchant_id' => $merchant->id,
        ]);
    }

    public function assignPermissions(Role $role, array $permissionIds)
    {
        $permissions = Permission::whereIn('id', $permissionIds)->get();
        $role->givePermissionTo($permissions);
    }

    public function revokePermission(Role $role, Permission $permission): bool
    {
        if (!$role->permissions->contains($permission)) {
            return false;
        }

        $role->revokePermissionTo($permission);
        return true;
    }

    public function deleteRole(Role $role): bool
    {
        if ($role->name === 'admin' && $role->merchant_id === 1) {
            return false;
        }

        $role->permissions()->detach();
        $role->users()->detach();
        $role->delete();

        return true;
    }
}
