<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Merchant\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Show list of merchant users
     */
    public function index()
    {
        
        Gate::authorize('viewAny', User::class);

        $users = $this->userService->getUsersForMerchant(auth()->user());
        $roles = $this->userService->getAssignableRolesForMerchant(auth()->user());

        return view('merchant.users', compact('users', 'roles'));
    }

    /**
     * Create a user (for testing purposes)
     */

    public function store(Request $request)
    {
        // Policy check is handled by middleware
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
        ]);

        $result = $this->userService->createUserForMerchant($request, auth()->user());

        return redirect()->route('merchant.users.index')->with(
            $result['status'] ? 'success' : 'error',
            $result['message']
        );
    }

    /**
     * Update the role for a user (via modal)
     */
    public function updateRole(Request $request, $id)
    {
        Gate::authorize('assignRole', User::class);

        $request->validate([
            'role' => 'required|string',
        ]);

        $result = $this->userService->updateUserRole($id, $request->role, auth()->user());

        return redirect()->route('merchant.users.index')->with(
            $result['status'] ? 'success' : 'error',
            $result['message']
        );
    }

    /**
     * Delete a merchant user
     */
    public function destroy($id)
    {
        $targetUser = User::findOrFail($id);

        Gate::authorize('delete', $targetUser);

        $result = $this->userService->deleteUser($id, auth()->user());

        return redirect()->route('merchant.users.index')->with(
            $result['status'] ? 'success' : 'error',
            $result['message']
        );
    }
}
