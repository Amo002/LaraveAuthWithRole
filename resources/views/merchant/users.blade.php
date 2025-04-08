@extends('layouts.dashboard-layout')

@section('title', 'Merchant Users')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="bi bi-people me-2"></i>
                Merchant Users
            </h2>
            @can('create-merchant-users')
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-person-plus me-2"></i>
                    Add User
                </button>
            @endcan
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td class="text-muted">#{{ $user->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle me-2"></i>
                                            {{ $user->name }}
                                            @if (auth()->id() === $user->id)
                                                <span class="badge bg-secondary ms-2">You</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-envelope me-2"></i>
                                            {{ $user->email }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($user->getRoleNames() as $role)
                                                <span class="badge bg-primary">{{ $role }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        @if (auth()->id() !== $user->id)
                                            <div class="btn-group">
                                                @can('assign-merchant-roles')
                                                    <button type="button" class="btn btn-sm btn-outline-warning update-role-btn"
                                                        data-user-id="{{ $user->id }}"
                                                        data-current-role="{{ $user->roles->first()?->name ?? '' }}"
                                                        data-bs-toggle="modal" data-bs-target="#updateRoleModal">
                                                        <i class="bi bi-person-gear me-1"></i>
                                                        Role
                                                    </button>
                                                @endcan
                                                @can('delete-merchant-users')
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete-user-btn"
                                                        data-user-id="{{ $user->id }}" data-bs-toggle="modal"
                                                        data-bs-target="#confirmDeleteUserModal">
                                                        <i class="bi bi-trash me-1"></i>
                                                        Delete
                                                    </button>
                                                @endcan
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Add User Modal --}}
        @can('create-merchant-users')
            <div class="modal fade" id="addUserModal" tabindex="-1">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('merchant.users.store') }}" class="modal-content">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-person-plus me-2"></i>
                                Add Merchant User
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="text" name="name" class="form-control" required />
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" name="email" class="form-control" required />
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" name="password" class="form-control" required />
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i>
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>
                                Add User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endcan

        {{-- Delete Confirmation Modal --}}
        @can('delete-merchant-users')
            <div class="modal fade" id="confirmDeleteUserModal" tabindex="-1">
                <div class="modal-dialog">
                    <form method="POST" id="delete-user-form" class="modal-content">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                                Confirm Deletion
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0">Are you sure you want to delete this user? This action cannot be undone.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i>
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash me-1"></i>
                                Delete
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endcan

        {{-- Update Role Modal --}}
        @can('assign-merchant-roles')
            <div class="modal fade" id="updateRoleModal" tabindex="-1">
                <div class="modal-dialog">
                    <form id="update-role-form" method="POST" class="modal-content">
                        @csrf
                        @method('PATCH')
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-person-gear me-2"></i>
                                Update User Role
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Select Role</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-shield"></i>
                                    </span>
                                    <select name="role" class="form-select" required>
                                        <option value="">Select Role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">
                                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i>
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-circle me-1"></i>
                                Update Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endcan
    </div>
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
