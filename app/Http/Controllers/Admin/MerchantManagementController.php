<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Services\Admin\MerchantManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class MerchantManagementController extends Controller
{
    protected $merchantService;

    public function __construct(MerchantManagementService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

    /**
     * Show the merchant's management page.
     */
    public function show($id)
    {
        Gate::authorize('admin');

        // If you want to scope even "show" read queries, do:
        app(PermissionRegistrar::class)->setPermissionsTeamId($id);

        $result = $this->merchantService->showMerchantManagement($id);

        if (!$result['status']) {
            return redirect()->route('admin.merchants.index')
                ->with('error', $result['message']);
        }

        return view('admin.merchants-manage', $result['data']);
    }

    /**
     * Store a new permission for the given merchant.
     */
    public function storePermission(Request $request, Merchant $merchant)
    {
        Gate::authorize('admin');

        // Set the "team ID" so Spatie knows we're operating under this merchant
        app(PermissionRegistrar::class)->setPermissionsTeamId($merchant->id);

        $result = $this->merchantService->storePermission($merchant, $request->all());

        return redirect()
            ->route('admin.merchants.manage', $merchant->id)
            ->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Delete a permission from the merchant.
     */
    public function destroyPermission(Merchant $merchant, Permission $permission)
    {
        Gate::authorize('admin');

        app(PermissionRegistrar::class)->setPermissionsTeamId($merchant->id);

        $result = $this->merchantService->destroyPermission($merchant, $permission);

        return redirect()
            ->route('admin.merchants.manage', $merchant->id)
            ->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Assign a permission to a specific role of the merchant.
     */
    public function assignPermission(Request $request, Merchant $merchant, Role $role)
    {
        Gate::authorize('admin');

        // set the team ID to the merchant's ID
        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($merchant->id);

        // We expect an array of permission IDs
        $permissionIds = $request->input('permission_ids', []); // this can be an empty array if none selected

        $result = $this->merchantService->assignMultiplePermissionsToRole($merchant, $role, $permissionIds);

        return redirect()
            ->route('admin.merchants.manage', $merchant->id)
            ->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    public function revokePermission(Merchant $merchant, Role $role, Permission $permission)
    {
        Gate::authorize('admin');

        // If you use Spatie's "teams" feature, scope the correct merchant ID
        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($merchant->id);

        // Call service method
        $result = $this->merchantService->revokePermissionFromRole($merchant, $role, $permission);

        return redirect()
            ->route('admin.merchants.manage', $merchant->id)
            ->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    public function unlockDevPermissions(Request $request, Merchant $merchant)
    {
        Gate::authorize('admin');

        $request->validate([
            'password' => ['required'],
        ]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return back()->with('error', 'Invalid developer password.');
        }

        session(['show_merchant_permissions' => true]);

        return redirect()->route('admin.merchants.manage', $merchant->id)->with('success', 'Developer permissions unlocked.');
    }
}
