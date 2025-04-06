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

        // Merchant-specific permissions
        $permissions = Permission::where('merchant_id', $merchant->id)->get();

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
     * Create/store a new permission for the merchant.
     */
    public function storePermission(Merchant $merchant, array $data)
    {
        // Validate required field
        if (empty($data['permission_name'])) {
            return [
                'status'  => false,
                'message' => 'Permission name is required.',
            ];
        }

        // Check if permission name already exists
        $existing = Permission::where('name', $data['permission_name'])->first();
        if ($existing) {
            return [
                'status'  => false,
                'message' => 'Permission name already exists.',
            ];
        }

        // Create a new Permission for this merchant
        Permission::create([
            'name'        => $data['permission_name'],
            'merchant_id' => $merchant->id,
        ]);

        return [
            'status'  => true,
            'message' => 'New permission added successfully.',
        ];
    }

    /**
     * Delete a permission from the merchant.
     */
    public function destroyPermission(Merchant $merchant, Permission $permission)
    {
        // Confirm the permission truly belongs to this merchant
        if ($permission->merchant_id !== $merchant->id) {
            return [
                'status'  => false,
                'message' => 'Permission not found for this merchant.',
            ];
        }

        $permission->delete();

        return [
            'status'  => true,
            'message' => 'Permission deleted successfully.',
        ];
    }

    /**
     * Assign a permission to a role of this merchant.
     */
    public function assignMultiplePermissionsToRole(Merchant $merchant, Role $role, array $permissionIds)
    {
        // Confirm the role belongs to this merchant
        if ($role->merchant_id !== $merchant->id) {
            return [
                'status'  => false,
                'message' => 'Role not found for this merchant.',
            ];
        }

        if (empty($permissionIds)) {
            return [
                'status'  => false,
                'message' => 'No permissions were selected.',
            ];
        }

        // Fetch only those that belong to this merchant
        $permissions = \Spatie\Permission\Models\Permission::where('merchant_id', $merchant->id)
            ->whereIn('id', $permissionIds)
            ->get();

        if ($permissions->isEmpty()) {
            return [
                'status'  => false,
                'message' => 'None of the selected permissions belong to this merchant.',
            ];
        }

        // Attach them all in one go
        $role->givePermissionTo($permissions);

        return [
            'status'  => true,
            'message' => 'Permissions assigned to role [' . $role->name . '] successfully.',
        ];
    }

    public function revokePermissionFromRole(Merchant $merchant, Role $role, Permission $permission)
    {
        // 1. Confirm the role belongs to this merchant
        if ($role->merchant_id !== $merchant->id) {
            return [
                'status'  => false,
                'message' => 'Role not found for this merchant.',
            ];
        }

        // 2. Confirm the permission belongs to this merchant
        if ($permission->merchant_id !== $merchant->id) {
            return [
                'status'  => false,
                'message' => 'Permission not found for this merchant.',
            ];
        }

        // 3. Use Spatie's built-in method to revoke permission from the role
        $role->revokePermissionTo($permission);

        return [
            'status'  => true,
            'message' => "Permission [{$permission->name}] has been removed from role [{$role->name}].",
        ];
    }
}
