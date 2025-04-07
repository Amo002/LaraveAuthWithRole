<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();

        // Global Admin
        if ($authUser->hasRole('admin')) {
            Gate::authorize('viewAny', User::class);

            $data = [
                'authUser' => $authUser,
                'totalUsers' => User::count(),
                'activeSessions' => DB::table('sessions')->count(),
                'pendingRequests' => User::whereNull('email_verified_at')->count(),
            ];

            return view('admin.dashboard', $data);
        }

        // Merchant Admin
        if ($authUser->hasRole('merchant_admin')) {
            $data = [
                'authUser' => $authUser,
                'totalMerchantUsers' => User::where('merchant_id', $authUser->merchant_id)->count(),
                'successfulOrders' => rand(5, 20), // Replace with real logic
                'failedOrders' => rand(1, 5),       // Replace with real logic
            ];

            return view('shared.dashboard', $data);
        }

        // Fallback (if needed)
        abort(403, 'Unauthorized');
    }
}
