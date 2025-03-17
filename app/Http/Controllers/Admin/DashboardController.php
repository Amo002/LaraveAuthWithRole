<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();
        $totalUsers = User::count(); 

        return view('admin.dashboard', compact('authUser','totalUsers'));
    }
}
