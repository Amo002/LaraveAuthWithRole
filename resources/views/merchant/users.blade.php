@extends('layouts.dashboard-layout')

@section('title', 'Merchant Users')

@section('content')
<<<<<<< HEAD
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Merchant Users</h2>
        @can('manage-users')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
        @endcan
    </div>

    {{-- Users Table --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
=======
    <h2>Merchant User List</h2>


    {{-- Users Table --}}
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Name</th>
>>>>>>> 5facc614503652ba13d316d933c77bc46416dbd2
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
<<<<<<< HEAD
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if (auth()->id() !== $user->id)
                            @can('edit-content')
                                <button type="button" class="btn btn-sm btn-danger delete-user-btn"
                                    data-user-id="{{ $user->id }}" data-bs-toggle="modal"
                                    data-bs-target="#confirmDeleteUserModal">
                                    Delete
                                </button>
                            @else
                                <span class="badge bg-secondary">No Access</span>
                            @endcan
                        @else
                            <span class="badge bg-secondary">You</span>
                        @endif

=======
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->name }}</td>
                    <td>
                        {{-- Delete Button --}}
                        <button type="button" class="btn btn-sm btn-danger delete-user-btn" data-user-id="{{ $user->id }}"
                            data-bs-toggle="modal" data-bs-target="#confirmDeleteUserModal">
                            Delete
                        </button>
>>>>>>> 5facc614503652ba13d316d933c77bc46416dbd2
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

<<<<<<< HEAD
    {{-- Add User Modal --}}
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('merchant.users.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Merchant User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="confirmDeleteUserModal" tabindex="-1" aria-labelledby="confirmDeleteUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="delete-user-form" class="modal-content">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Confirm User Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
=======

    <!-- Confirm Delete Modal -->
    <div class="modal fade" id="confirmDeleteUserModal" tabindex="-1" aria-labelledby="confirmDeleteUserLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="delete-user-form" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this user?
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
>>>>>>> 5facc614503652ba13d316d933c77bc46416dbd2
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
<<<<<<< HEAD
            const deleteButtons = document.querySelectorAll('.delete-user-btn');
            const deleteForm = document.getElementById('delete-user-form');

            deleteButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const userId = button.getAttribute('data-user-id');
                    deleteForm.action = `/merchant/users/${userId}`;
=======
            let deleteUserId = null;

            // Handle delete user
            document.querySelectorAll('.delete-user-btn').forEach(button => {
                button.addEventListener('click', () => {
                    deleteUserId = button.getAttribute('data-user-id');
                    const form = document.getElementById('delete-user-form');
                    form.action = `/merchant/users/${deleteUserId}`;
>>>>>>> 5facc614503652ba13d316d933c77bc46416dbd2
                });
            });
        });
    </script>
@endpush
