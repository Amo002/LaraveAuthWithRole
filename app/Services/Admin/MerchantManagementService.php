<?php

namespace App\Services\Admin;

use App\Models\Merchant;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MerchantManagementService
{
    /**
     * Retrieve the merchant's details (super admin, roles, permissions).
     */
    public function showMerchantManagement($merchantId)
    {
        $merchant = Merchant::find($merchantId);
        if (!$merchant) {
            return [
                'status'  => false,
                'message' => 'Merchant not found.',
            ];
        }

        // Roles with permissions/users
        $roles = Role::where('merchant_id', $merchant->id)
            ->with(['permissions', 'users'])
            ->get();

        // All available permissions globally
        $permissions = Permission::all();

        // Identify super admin user if it exists
        $superAdminRoleName = $merchant->name . '_superadmin';
        $superAdminRole = $roles->firstWhere('name', $superAdminRoleName);

        $superAdmin = null;
        if ($superAdminRole && $superAdminRole->users->isNotEmpty()) {
            $superAdmin = $superAdminRole->users->first();
        }

        return [
            'status' => true,
            'data'   => [
                'merchant'    => $merchant,
                'roles'       => $roles,
                'permissions' => $permissions,
                'superAdmin'  => $superAdmin,
            ],
            'message' => 'Merchant management data retrieved.',
        ];
    }

    /**
     * Create/store a new permission globally.
     */
    public function storePermission(Merchant $merchant, array $data)
    {
        if (empty($data['permission_name'])) {
            return [
                'status'  => false,
                'message' => 'Permission name is required.',
            ];
        }

        // Check if this permission already exists globally
        $existing = Permission::where('name', $data['permission_name'])->first();
        if ($existing) {
            return [
                'status'  => false,
                'message' => 'Permission name already exists.',
            ];
        }

        // Create globally for the 'web' guard
        Permission::create([
            'name' => $data['permission_name'],
            'guard_name' => 'web',
        ]);

        return [
            'status' => true,
            'message' => 'New permission added successfully.',
        ];
    }

    /**
     * Delete a global permission.
     */
    public function destroyPermission(Merchant $merchant, Permission $permission)
    {
        $permission->delete();

        return [
            'status' => true,
            'message' => 'Permission deleted successfully.',
        ];
    }

    /**
     * Assign selected permissions to a role.
     */
    public function assignMultiplePermissionsToRole(Merchant $merchant, Role $role, array $permissionIds)
    {
        if ($role->merchant_id !== $merchant->id) {
            return [
                'status' => false,
                'message' => 'Role not found for this merchant.',
            ];
        }

        if (empty($permissionIds)) {
            return [
                'status' => false,
                'message' => 'No permissions were selected.',
            ];
        }

        $permissions = Permission::whereIn('id', $permissionIds)->get();

        if ($permissions->isEmpty()) {
            return [
                'status' => false,
                'message' => 'No valid permissions found.',
            ];
        }

        $role->givePermissionTo($permissions);

        return [
            'status' => true,
            'message' => "Permissions assigned to role [{$role->name}] successfully.",
        ];
    }

    /**
     * Revoke a permission from a role.
     */
    public function revokePermissionFromRole(Merchant $merchant, Role $role, Permission $permission)
    {
        if ($role->merchant_id !== $merchant->id) {
            return [
                'status' => false,
                'message' => 'Role not found for this merchant.',
            ];
        }

        $role->revokePermissionTo($permission);

        return [
            'status' => true,
            'message' => "Permission [{$permission->name}] has been removed from role [{$role->name}].",
        ];
    }
}
