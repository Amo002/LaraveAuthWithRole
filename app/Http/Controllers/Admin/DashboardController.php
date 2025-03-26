<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();

        // Data for Admin
        if ($authUser->hasRole('admin')) {
            $data = [
                'totalUsers' => User::count(),
                'activeSessions' => DB::table('sessions')->count(),
                'pendingRequests' => User::whereNull('email_verified_at')->count(),
            ];
        }
        // Data for Merchant
        elseif ($authUser->hasRole('merchant')) {
            $data = [
                'totalMerchantUsers' => User::where('merchant_id', $authUser->id)->count(),
                'successfulOrders' => rand(5, 20), // Example value, replace with actual logic
                'failedOrders' => rand(1, 5), // Example value, replace with actual logic
            ];
        } else {
            $data = [];
        }

        return view('admin.dashboard', array_merge($data, [
            'authUser' => $authUser
        ]));
    }
}
