@extends('layouts.dashboard-layout')

@section('title', 'Admin Roles & Permissions')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="bi bi-shield-lock me-2"></i>
                All Roles Grouped by Merchant
            </h2>
        </div>

        {{-- Create Role --}}
        @can('create-roles')
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i>
                        Create New Role
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.roles.store') }}">
                        @csrf
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Role Name</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-person-badge"></i>
                                    </span>
                                    <input type="text" name="role_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Merchant</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-shop"></i>
                                    </span>
                                    <select name="merchant_id" class="form-select" required>
                                        @foreach ($merchants as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 mt-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Create Role
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endcan

        {{-- Roles Grouped by Merchant --}}
        @foreach ($merchants as $merchantId => $merchantName)
            @php
                $groupedRoles = $roles->where('merchant_id', $merchantId);
            @endphp

            @if ($groupedRoles->isNotEmpty())
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-shop me-2"></i>
                            <strong>{{ $merchantName }}</strong>
                        </div>
                        <span class="badge bg-primary">
                            {{ $groupedRoles->count() }} roles
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Role</th>
                                        @can('view-permissions')
                                            <th>Permissions</th>
                                        @endcan
                                        @can('edit-roles')
                                            <th>Assign</th>
                                        @endcan
                                        @can('delete-roles')
                                            <th class="text-end">Delete</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($groupedRoles as $role)
                                        @php
                                            $isProtectedAdmin = strtolower($role->name) === 'admin' && $role->merchant_id === 1;
                                            $unassignedPermissions = $permissions->diff($role->permissions);
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-person-badge me-2"></i>
                                                    {{ $role->name }}
                                                    @if ($isProtectedAdmin)
                                                        <span class="badge bg-primary ms-2">
                                                            <i class="bi bi-shield-check me-1"></i>
                                                            Protected
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- View Permissions --}}
                                            @can('view-permissions')
                                                <td style="max-width: 300px;">
                                                    @if ($role->permissions->isEmpty())
                                                        <span class="text-muted">No permissions assigned</span>
                                                    @else
                                                        <div class="d-flex flex-wrap gap-2">
                                                            @foreach ($role->permissions as $perm)
                                                                <div class="badge bg-light text-dark border">
                                                                    {{ $perm->name }}
                                                                    @can('edit-roles')
                                                                        @unless ($isProtectedAdmin)
                                                                            <button type="button" class="btn btn-link btn-sm text-danger p-0 ms-1"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#confirmRevokeModal"
                                                                                data-action="{{ route('admin.roles.revokePermission', [$role->id, $perm->id]) }}"
                                                                                data-permission="{{ $perm->name }}">
                                                                                <i class="bi bi-x"></i>
                                                                            </button>
                                                                        @endunless
                                                                    @endcan
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </td>
                                            @endcan

                                            {{-- Assign New Permissions --}}
                                            @can('edit-roles')
                                                <td style="max-width: 260px;">
                                                    @if ($isProtectedAdmin)
                                                        <span class="text-muted">Protected</span>
                                                    @elseif($unassignedPermissions->isEmpty())
                                                        <span class="text-muted">All permissions assigned</span>
                                                    @else
                                                        <form method="POST"
                                                            action="{{ route('admin.roles.assignPermission', $role->id) }}">
                                                            @csrf
                                                            <div class="border rounded p-2"
                                                                style="max-height: 160px; overflow-y: auto;">
                                                                @foreach ($unassignedPermissions as $perm)
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            name="permission_ids[]" value="{{ $perm->id }}"
                                                                            id="perm_{{ $role->id }}_{{ $perm->id }}">
                                                                        <label class="form-check-label"
                                                                            for="perm_{{ $role->id }}_{{ $perm->id }}">
                                                                            {{ $perm->name }}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <button type="submit" class="btn btn-sm btn-primary mt-2">
                                                                <i class="bi bi-plus-circle me-1"></i>
                                                                Assign Selected
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            @endcan

                                            {{-- Delete Button --}}
                                            @can('delete-roles')
                                                <td class="text-end">
                                                    @if ($isProtectedAdmin)
                                                        <span class="text-muted">Protected</span>
                                                    @else
                                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                            data-bs-target="#confirmDeleteRoleModal"
                                                            data-role-name="{{ $role->name }}"
                                                            data-action="{{ route('admin.roles.destroy', $role->id) }}"
                                                            data-users-count="{{ $role->users_count }}">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            @endcan
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Revoke Permission Modal --}}
    <div class="modal fade" id="confirmRevokeModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" id="revoke-form" class="modal-content">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                        Revoke Permission
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to revoke permission: <strong id="revoke-permission-name"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-circle me-1"></i>
                        Revoke
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Role Modal --}}
    <div class="modal fade" id="confirmDeleteRoleModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" id="delete-role-form" class="modal-content">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                        Delete Role
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">
                        This role is assigned to <strong id="delete-role-users"></strong> user(s).<br>
                        Are you sure you want to delete role: <strong id="delete-role-name"></strong>?
                    </p>
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
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Revoke permission modal
            const revokeModal = document.getElementById('confirmRevokeModal');
            revokeModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                document.getElementById('revoke-form').action = button.getAttribute('data-action');
                document.getElementById('revoke-permission-name').textContent = button.getAttribute('data-permission');
            });

            // Delete role modal
            const deleteRoleModal = document.getElementById('confirmDeleteRoleModal');
            deleteRoleModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                document.getElementById('delete-role-form').action = button.getAttribute('data-action');
                document.getElementById('delete-role-name').textContent = button.getAttribute('data-role-name');
                document.getElementById('delete-role-users').textContent = button.getAttribute('data-users-count');
            });
        });
    </script>
@endpush
