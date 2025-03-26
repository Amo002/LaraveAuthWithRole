<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRoleRequest;
use App\Models\User;
use App\Services\Admin\UserService;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the users.
     */
    public function index()
    {
        Gate::authorize('viewAny', User::class);

        $result = $this->userService->getUsers(auth()->user());

        return view('admin.users', [
            'users' => $result['data'],
            'availableRoles' => $result['roles'],
        ]);
    }
    

    /**
     * Delete a user.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Use Gate to authorize
        Gate::authorize('delete', $user);

        $result = $this->userService->deleteUser($id);

        return redirect()->route('admin.users.index')->with(
            $result['status'] ? 'success' : 'error',
            $result['message']
        );
    }

    /**
     * Update the user role.
     */
    public function updateRole(UpdateUserRoleRequest $request, $id)
    {
        $user = User::findOrFail($id);

        // Use Gate to authorize
        Gate::authorize('update', $user);

        $result = $this->userService->updateUserRole($id, $request->role);

        return redirect()->route('admin.users.index')->with(
            $result['status'] ? 'success' : 'error',
            $result['message']
        );
    }
}
