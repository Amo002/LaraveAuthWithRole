@extends('layouts.dashboard-layout')

@section('title', 'Users')

@section('content')
    <h2>Users List</h2>

    {{-- Users Table --}}
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Roles</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                    <td>
                        @if (auth()->user()->id !== $user->id)
                            <button type="button" class="btn btn-sm btn-danger delete-user-btn"
                                data-user-id="{{ $user->id }}" data-bs-toggle="modal"
                                data-bs-target="#confirmDeleteUserModal">
                                Delete
                            </button>

                            <button type="button" class="btn btn-sm btn-warning update-role-btn"
                                data-user-id="{{ $user->id }}"
                                data-current-role="{{ $user->roles->pluck('name')->first() }}" data-bs-toggle="modal"
                                data-bs-target="#updateRoleModal">
                                Update Role
                            </button>
                        @else
                            <span class="badge bg-secondary">You</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteUserModal" tabindex="-1" aria-labelledby="confirmDeleteUserLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteUserLabel">Confirm User Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <form id="delete-user-form" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete-user-btn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Role Modal -->
    <div class="modal fade" id="updateRoleModal" tabindex="-1" aria-labelledby="updateRoleLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="update-role-form" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateRoleLabel">Update User Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <select class="form-select" name="role" id="user-role">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Update Role</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let deleteUserId = null;
            let updateUserId = null;

            // Open delete confirmation modal
            document.querySelectorAll('.delete-user-btn').forEach(button => {
                button.addEventListener('click', () => {
                    deleteUserId = button.getAttribute('data-user-id');
                });
            });

            document.getElementById('confirm-delete-user-btn').addEventListener('click', () => {
                if (deleteUserId) {
                    const form = document.getElementById('delete-user-form');
                    form.action = `/users/${deleteUserId}`;
                    form.submit();
                }
            });

            // Open Update Role Modal
            document.querySelectorAll('.update-role-btn').forEach(button => {
                button.addEventListener('click', () => {
                    updateUserId = button.getAttribute('data-user-id');
                    const currentRole = button.getAttribute('data-current-role');

                    // Set form action dynamically
                    const form = document.getElementById('update-role-form');
                    form.action = `/users/${updateUserId}/update-role`;

                    // Set the current role in the dropdown
                    document.getElementById('user-role').value = currentRole;
                });
            });
        });
    </script>
@endpush
