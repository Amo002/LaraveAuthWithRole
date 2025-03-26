<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Services\Merchant\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        Gate::authorize('viewAny', User::class);

        $users = $this->userService->getUsersForMerchant(auth()->user());

        return view('merchant.users', compact('users'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', User::class);

        $result = $this->userService->createUserForMerchant($request, auth()->user());

        return redirect()->route('merchant.users.index')->with(
            $result['status'] ? 'success' : 'error',
            $result['message']
        );
    }

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