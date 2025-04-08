@extends('layouts.dashboard-layout')

@section('title', 'Merchant Users')

@section('content')
    <h2>Merchant Users</h2>

    @can('create-merchant-users')
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
            Add User
        </button>
    @endcan

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Roles</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>
                        {{ $user->name }}
                        @if (auth()->id() === $user->id)
                            <span class="badge bg-secondary">You</span>
                        @endif
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>{{ implode(', ', $user->getRoleNames()->toArray()) }}</td>
                    <td>
                        @if (auth()->id() !== $user->id)
                            @can('delete-merchant-users')
                                <button type="button" class="btn btn-sm btn-danger delete-user-btn"
                                    data-user-id="{{ $user->id }}" data-bs-toggle="modal"
                                    data-bs-target="#confirmDeleteUserModal">
                                    Delete
                                </button>
                            @endcan

                            @can('assign-merchant-roles')
                                <button type="button" class="btn btn-sm btn-warning update-role-btn"
                                    data-user-id="{{ $user->id }}"
                                    data-current-role="{{ $user->roles->first()?->name ?? '' }}" data-bs-toggle="modal"
                                    data-bs-target="#updateRoleModal">
                                    Update Role
                                </button>
                            @endcan
                        @else
                            <span class="badge bg-secondary">—</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Add User Modal --}}
    @can('create-merchant-users')
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
                            <input type="text" name="name" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    {{-- Delete Confirmation Modal --}}
    @can('delete-merchant-users')
        <div class="modal fade" id="confirmDeleteUserModal" tabindex="-1" aria-labelledby="confirmDeleteUserModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" id="delete-user-form" class="modal-content">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this user?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    {{-- Update Role Modal --}}
    @can('assign-merchant-roles')
        <div class="modal fade" id="updateRoleModal" tabindex="-1" aria-labelledby="updateRoleLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="update-role-form" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Update User Role</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <select name="role" class="form-select" required>
                                <option value="">Select Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                    </option>
                                @endforeach
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
    @endcan
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Delete action
            document.querySelectorAll('.delete-user-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const userId = button.getAttribute('data-user-id');
                    document.getElementById('delete-user-form').action =
                        `/merchant/users/${userId}`;
                });
            });

            // Update role modal
            document.querySelectorAll('.update-role-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const userId = button.getAttribute('data-user-id');
                    const currentRole = button.getAttribute('data-current-role');
                    const form = document.getElementById('update-role-form');
                    const select = form.querySelector('select[name="role"]');

                    form.action = `/merchant/users/${userId}/update-role`;

                    Array.from(select.options).forEach(option => {
                        option.selected = option.value === currentRole;
                    });
                });
            });
        });
    </script>
@endpush
