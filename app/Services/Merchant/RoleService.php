<?php

namespace App\Services\Merchant;

use App\Models\Merchant;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleService
{
    protected $merchant;

    public function __construct()
    {
        $this->merchant = auth()->user()->merchant;
        app(PermissionRegistrar::class)->setPermissionsTeamId($this->merchant->id);
    }

    public function getRoles()
    {
        return Role::where('merchant_id', $this->merchant->id)->with('permissions')->get();
    }

    public function getAdminPermissions()
    {
        $adminRole = Role::where('name', 'merchant_admin')
            ->where('merchant_id', $this->merchant->id)
            ->with('permissions')
            ->first();

        return $adminRole?->permissions ?? collect();
    }

    public function createRole(string $name): bool
    {
        $slug = Str::slug($name, '_');
        $fullRoleName = strtolower($this->merchant->name) . '_' . $slug;

        if (Role::where('name', $fullRoleName)->where('merchant_id', $this->merchant->id)->exists()) {
            return false;
        }

        Role::create([
            'name' => $fullRoleName,
            'merchant_id' => $this->merchant->id,
        ]);

        return true;
    }

    public function assignPermissions(Role $role, array $permissionIds): bool
    {
        if ($role->merchant_id !== $this->merchant->id) {
            return false;
        }

        $permissions = Permission::whereIn('id', $permissionIds)->get();
        $role->givePermissionTo($permissions);

        return true;
    }

    public function revokePermission(Role $role, Permission $permission): bool
    {
        if (
            $role->merchant_id !== $this->merchant->id ||
            !$role->permissions->contains($permission)
        ) {
            return false;
        }

        $role->revokePermissionTo($permission);
        return true;
    }

    public function deleteRole(Role $role): bool
    {
        if (
            $role->merchant_id !== $this->merchant->id ||
            strtolower($role->name) === 'merchant_admin'
        ) {
            return false;
        }

        $role->permissions()->detach();
        $role->users()->detach();
        $role->delete();

        return true;
    }
}
