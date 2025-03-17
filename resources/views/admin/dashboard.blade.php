@extends('layouts.dashboard-layout')

@section('title', 'Dashboard')

@section('content')

    <div id="home-section">
        <h2>Welcome, {{ $authUser->name ?? 'Guest' }}</h2>
        <p>This is your full-page Bootstrap dashboard.</p>

        <div class="row">
            <div class="col-md-4">
                <div class="card bg-primary text-white p-3">
                    <h5>Total Users</h5>
                    <p>{{ $totalUsers ?? '-' }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white p-3">
                    <h5>Active Sessions</h5>
                    <p>5</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark p-3">
                    <h5>Pending Requests</h5>
                    <p>3</p>
                </div>
            </div>
        </div>
    </div>

@endsection
