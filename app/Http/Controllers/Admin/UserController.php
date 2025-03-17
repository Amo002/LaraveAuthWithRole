<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRoleRequest;
use App\Services\Admin\UserService;
use App\Services\Admin\InviteService;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    protected $UserService;
    protected $inviteService;

    public function __construct(UserService $UserService)
    {
        $this->UserService = $UserService;
    }

    public function index()
    {
        $authUser = Auth::user();

        $result = $this->UserService->getUsers($authUser);
        $users = $result['data'];
        $message = $result['message'];

        return view('admin.users', [
            'users' => $users,
            'authUser' => $authUser,
            'message' => $message
        ]);
    }


    public function destroy($id)
    {
        $result = $this->UserService->deleteUser($id);
        return redirect()->route('users.index')->with(
            $result['status'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function updateRole(UpdateUserRoleRequest $request, $id)
    {
        $result = $this->UserService->updateUserRole($id, $request->role);

        return redirect()->route('users.index')->with(
            $result['status'] ? 'success' : 'error',
            $result['message']
        );
    }
}
