<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignPermissionRequest;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Services\Admin\RoleService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    protected $service;

    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        Gate::authorize('admin');

        $roles = $this->service->listRoles();
        $permissions = $this->service->listPermissions();
        $merchants = $this->service->listMerchants();

        return view('admin.roles', compact('roles', 'permissions', 'merchants'));
    }

    public function store(StoreRoleRequest $request)
    {
        Gate::authorize('admin');

        $this->service->createRole($request->only('role_name', 'merchant_id'));

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function assignPermission(AssignPermissionRequest $request, Role $role)
    {
        Gate::authorize('admin');

        $this->service->assignPermissions($role, $request->permission_ids);

        return redirect()->route('admin.roles.index')->with('success', 'Permissions assigned successfully.');
    }

    public function revokePermission(Role $role, Permission $permission)
    {
        Gate::authorize('admin');

        $success = $this->service->revokePermission($role, $permission);

        if (!$success) {
            return redirect()->back()->with('error', 'Permission not assigned to this role.');
        }

        return redirect()->route('admin.roles.index')->with('success', 'Permission revoked successfully.');
    }

    public function destroy(Role $role)
    {
        Gate::authorize('admin');

        $success = $this->service->deleteRole($role);

        if (!$success) {
            return redirect()->back()->with('error', 'Cannot delete the global admin role.');
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
