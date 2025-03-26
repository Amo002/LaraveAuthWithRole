<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', User::class);

        $authUser = auth()->user();

        $data = [
            'authUser' => $authUser,
            'totalUsers' => User::count(),
            'activeSessions' => DB::table('sessions')->count(),
            'pendingRequests' => User::whereNull('email_verified_at')->count(),
        ];

        return view('shared.dashboard', $data);
    }
}
