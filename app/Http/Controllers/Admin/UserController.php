<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Show all users (across merchants)
     */
    public function index()
    {
        $result = $this->userService->getUsers(auth()->user());

        return view('admin.users', [
            'users' => $result['data'],
            'availableRoles' => $result['roles'],
            'merchants' => $result['merchants']
        ]);
    }

    /**
     * Delete a user
     */
    public function destroy($id)
    {
        $result = $this->userService->deleteUser($id);

        return redirect()->route('admin.users.index')->with(
            $result['status'] ? 'success' : 'error',
            $result['message']
        );
    }

    /**
     * Update a user's role
     */
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|string',
        ]);

        $result = $this->userService->updateUserRole($id, $request->role);

        return redirect()->route('admin.users.index')->with(
            $result['status'] ? 'success' : 'error',
            $result['message']
        );
    }
}
