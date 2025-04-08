@extends('layouts.dashboard-layout')

@section('title', 'Users')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-people me-2"></i>
            Users List
        </h2>
        <div class="d-flex align-items-center">
            <label for="merchant-filter" class="me-2 mb-0">Filter by Merchant:</label>
            <select class="form-select form-select-sm" id="merchant-filter" style="width: 200px;">
                @foreach ($merchants as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Merchant</th>
                            <th>Roles</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr data-merchant-id="{{ $user['merchant_id'] }}">
                                <td>{{ $user['id'] }}</td>
                                <td>{{ $user['name'] }}</td>
                                <td>{{ $user['email'] }}</td>
                                <td>{{ $merchants[$user['merchant_id']] ?? 'Unknown' }}</td>
                                <td>
                                    @foreach ($user['roles'] as $role)
                                        <span class="badge bg-primary me-1">{{ $role }}</span>
                                    @endforeach
                                </td>
                                <td class="text-end">
                                    @if (auth()->id() !== $user['id'] && $user['id'] !== 1)
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-user-btn"
                                                data-user-id="{{ $user['id'] }}" data-bs-toggle="modal"
                                                data-bs-target="#confirmDeleteUserModal">
                                                <i class="bi bi-trash"></i>
                                            </button>

                                            <button type="button" class="btn btn-sm btn-outline-warning update-role-btn"
                                                data-user-id="{{ $user['id'] }}" 
                                                data-current-role="{{ $user['roles'][0] ?? '' }}"
                                                data-merchant-id="{{ $user['merchant_id'] }}" 
                                                data-bs-toggle="modal"
                                                data-bs-target="#updateRoleModal">
                                                <i class="bi bi-shield-lock"></i>
                                            </button>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary">You</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="confirmDeleteUserModal" tabindex="-1" aria-labelledby="confirmDeleteUserLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="delete-user-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                            Confirm User Deletion
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
    </div>

    {{-- Update Role Modal --}}
    <div class="modal fade" id="updateRoleModal" tabindex="-1" aria-labelledby="updateRoleLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="update-role-form" method="POST" action="">
                @csrf
                @method('PATCH')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-shield-lock me-2"></i>
                            Update User Role
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="user-role" class="form-label">Select Role</label>
                            <select class="form-select" name="role" id="user-role"></select>
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
            // Merchant filter functionality
            const merchantFilter = document.getElementById('merchant-filter');
            const userRows = document.querySelectorAll('tbody tr');

            merchantFilter.addEventListener('change', () => {
                const selectedMerchantId = merchantFilter.value;
                
                userRows.forEach(row => {
                    if (selectedMerchantId === 'all' || row.getAttribute('data-merchant-id') === selectedMerchantId) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Delete user functionality
            let deleteUserId = null;
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

            // Update role functionality
            let updateUserId = null;
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
                            option.textContent = role.charAt(0).toUpperCase() + role.slice(1);
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
