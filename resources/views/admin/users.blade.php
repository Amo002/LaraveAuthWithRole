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
                    <td>{{ $user['id'] }}</td>
                    <td>{{ $user['name'] }}</td>
                    <td>{{ $user['email'] }}</td>
                    <td>{{ implode(', ', $user['roles']) }}</td>
                    <td>
                        @if (auth()->id() !== $user['id'] && $user['id'] !== 1)
                            {{-- Delete Button --}}
                            <button type="button" class="btn btn-sm btn-danger delete-user-btn"
                                data-user-id="{{ $user['id'] }}" data-bs-toggle="modal"
                                data-bs-target="#confirmDeleteUserModal">
                                Delete
                            </button>

                            {{-- Update Role Button --}}
                            <button type="button" class="btn btn-sm btn-warning update-role-btn"
                                data-user-id="{{ $user['id'] }}" data-current-role="{{ $user['roles'][0] ?? '' }}"
                                data-merchant-id="{{ $user['merchant_id'] }}" data-bs-toggle="modal"
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


    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="confirmDeleteUserModal" tabindex="-1" aria-labelledby="confirmDeleteUserLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="delete-user-form" method="POST">
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
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Update Role Modal --}}
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
                        <select class="form-select" name="role" id="user-role"></select>
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

<script>
    const availableRoles = @json($availableRoles);
</script>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let deleteUserId = null;
            let updateUserId = null;

            // Delete user
            document.querySelectorAll('.delete-user-btn').forEach(button => {
                button.addEventListener('click', () => {
                    deleteUserId = button.getAttribute('data-user-id');
                    document.getElementById('delete-user-form').action =
                        `/admin/users/${deleteUserId}`;
                });
            });

            document.getElementById('confirmDeleteUserModal').addEventListener('hidden.bs.modal', () => {
                deleteUserId = null;
            });

            // Update role
            document.querySelectorAll('.update-role-btn').forEach(button => {
                button.addEventListener('click', () => {
                    updateUserId = button.getAttribute('data-user-id');
                    const currentRole = button.getAttribute('data-current-role');
                    const merchantId = button.getAttribute('data-merchant-id');

                    const form = document.getElementById('update-role-form');
                    form.action = `/admin/users/${updateUserId}/update-role`;

                    const roleSelect = document.getElementById('user-role');
                    roleSelect.innerHTML = '';

                    const placeholder = document.createElement('option');
                    placeholder.disabled = true;
                    placeholder.selected = true;
                    placeholder.hidden = true;
                    placeholder.textContent = `Select a role for Merchant ${merchantId}`;
                    roleSelect.appendChild(placeholder);

                    const roles = availableRoles[merchantId];
                    if (roles && roles.length > 0) {
                        roles.forEach(role => {
                            const option = document.createElement('option');
                            option.value = role;
                            option.textContent = role.charAt(0).toUpperCase() + role.slice(
                                1);
                            if (role === currentRole) option.selected = true;
                            roleSelect.appendChild(option);
                        });
                    } else {
                        const noRoleOption = document.createElement('option');
                        noRoleOption.disabled = true;
                        noRoleOption.selected = true;
                        noRoleOption.textContent = 'No roles available';
                        roleSelect.appendChild(noRoleOption);
                    }
                });
            });

            document.getElementById('updateRoleModal').addEventListener('hidden.bs.modal', () => {
                updateUserId = null;
            });
        });
    </script>
@endpush
