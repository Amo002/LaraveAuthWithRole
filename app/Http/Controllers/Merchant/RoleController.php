<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Merchant\AssignPermissionRequest;
use App\Http\Requests\Merchant\StoreRoleRequest;
use App\Services\Merchant\RoleService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class RoleController extends Controller
{
    protected $service;

    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $merchant = auth()->user()->merchant;

        return view('merchant.roles', [
            'merchant' => $merchant,
            'roles' => $this->service->getRoles(),
            'availablePermissions' => $this->service->getAdminPermissions(),
        ]);
    }

    public function store(StoreRoleRequest $request)
    {
        $created = $this->service->createRole($request->role_name);

        if (!$created) {
            return redirect()->back()->with('error', 'Role already exists.');
        }

        return redirect()->route('merchant.roles.index')->with('success', 'Role created successfully.');
    }

    public function assignPermission(AssignPermissionRequest $request, Role $role)
    {
        $assigned = $this->service->assignPermissions($role, $request->permission_ids);

        if (!$assigned) {
            return redirect()->back()->with('error', 'Unauthorized role access.');
        }

        return redirect()->route('merchant.roles.index')->with('success', 'Permissions assigned successfully.');
    }


    public function revokePermission(Role $role, Permission $permission)
    {
        $revoked = $this->service->revokePermission($role, $permission);

        if (!$revoked) {
            return redirect()->back()->with('error', 'Unauthorized or invalid permission.');
        }

        return redirect()->route('merchant.roles.index')->with('success', 'Permission revoked successfully.');
    }

    public function destroy(Role $role)
    {
        $deleted = $this->service->deleteRole($role);

        if (!$deleted) {
            return redirect()->back()->with('error', 'Cannot delete this role.');
        }

        return redirect()->route('merchant.roles.index')->with('success', 'Role deleted successfully.');
    }
}
