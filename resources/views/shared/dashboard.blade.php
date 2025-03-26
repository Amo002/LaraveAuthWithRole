@extends('layouts.dashboard-layout')

@section('title', 'Dashboard')

@section('content')

    <div id="home-section">
        <h2>Welcome, {{ $authUser->name ?? 'Guest' }}</h2>
        <p>This is your admin dashboard.</p>

        <div class="row">
            {{-- Show this section ONLY for Admin --}}
            @if ($authUser->hasRole('admin'))
                <div class="col-md-4">
                    <div class="card bg-primary text-white p-3">
                        <h5>Total Users</h5>
                        <p>{{ $totalUsers ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white p-3">
                        <h5>Active Sessions</h5>
                        <p>{{ $activeSessions ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-dark p-3">
                        <h5>Pending Requests</h5>
                        <p>{{ $pendingRequests ?? '-' }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
