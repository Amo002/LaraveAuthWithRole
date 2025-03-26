<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\MerchantUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MerchantUserController extends Controller
{
    protected $merchantService;

    public function __construct(MerchantUserService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

    /**
     * Display a listing of the merchant's users.
     */
    public function index()
    {
        // Authorize using policy (hits UserPolicy::viewAny)
        Gate::authorize('viewAny', User::class);

        $users = $this->merchantService->getMerchantUsers(auth()->id());

        return view('merchant.users', compact('users'));
    }


    /**
     * Delete a user for the merchant.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Authorize using policy (hits UserPolicy::delete)
        Gate::authorize('delete', $user);

        $result = $this->merchantService->deleteUser($id, auth()->id());

        return redirect()->route('merchant.users.index')->with(
            $result['status'] ? 'success' : 'error',
            $result['message']
        );
    }
}
