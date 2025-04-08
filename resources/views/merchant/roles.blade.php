@extends('layouts.dashboard-layout')

@section('title', 'Merchant Roles')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="bi bi-shield me-2"></i>
                Roles Management - {{ $merchant->name }}
            </h2>
        </div>

        {{-- Create Role --}}
        @can('create-roles')
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <i class="bi bi-plus-circle me-2"></i>
                    Create New Role
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('merchant.roles.store') }}">
                        @csrf
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <label for="role_name" class="col-form-label">Role Name</label>
                            </div>
                            <div class="col-auto">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-shield"></i>
                                    </span>
                                    <input type="text" id="role_name" name="role_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Create Role
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endcan

        {{-- Roles List --}}
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <i class="bi bi-list me-2"></i>
                Roles ({{ $roles->count() }})
            </div>
            <div class="card-body p-0">
                @if ($roles->isEmpty())
                    <div class="text-center p-4">
                        <i class="bi bi-shield-slash text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mb-0">No roles found for this merchant.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Role Name</th>
                                    @can('view-permissions')
                                        <th style="min-width: 200px;">Permissions</th>
                                    @endcan
                                    @can('edit-roles')
                                        <th style="min-width: 250px;">Assign Permissions</th>
                                    @endcan
                                    @can('delete-roles')
                                        <th style="min-width: 100px;">Delete</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $role)
                                    @php
                                        $isMerchantAdminRole = strtolower($role->name) === 'merchant_admin';
                                        $unassigned = $availablePermissions->diff($role->permissions);
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-shield me-2"></i>
                                                {{ $role->name }}
                                            </div>
                                        </td>

                                        {{-- Permissions List --}}
                                        @can('view-permissions')
                                            <td>
                                                @if ($role->permissions->isEmpty())
                                                    <span class="text-muted">None</span>
                                                @else
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach ($role->permissions as $perm)
                                                            <div class="d-flex align-items-center">
                                                                <span class="badge bg-primary">
                                                                    {{ $perm->name }}
                                                                </span>
                                                                @can('edit-roles')
                                                                    @unless ($isMerchantAdminRole)
                                                                        <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-1"
                                                                            data-bs-toggle="modal" data-bs-target="#confirmRevokeModal"
                                                                            data-action="{{ route('merchant.roles.revokePermission', [$role->id, $perm->id]) }}"
                                                                            data-permission="{{ $perm->name }}">
                                                                            <i class="bi bi-x-circle"></i>
                                                                        </button>
                                                                    @endunless
                                                                @endcan
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                        @endcan

                                        {{-- Assign Permissions --}}
                                        @can('edit-roles')
                                            <td>
                                                @if ($isMerchantAdminRole)
                                                    <span class="text-muted">
                                                        <i class="bi bi-lock me-1"></i>
                                                        Merchant Admin role cannot be modified.
                                                    </span>
                                                @elseif ($unassigned->isEmpty())
                                                    <span class="text-muted">
                                                        <i class="bi bi-check-circle me-1"></i>
                                                        All permissions assigned.
                                                    </span>
                                                @else
                                                    <form method="POST" action="{{ route('merchant.roles.assignPermission', $role->id) }}">
                                                        @csrf
                                                        <div class="mb-2">
                                                            <label class="form-label fw-bold">Select Permissions:</label>
                                                            <div class="border rounded p-2" style="max-height: 160px; overflow-y: auto;">
                                                                @foreach ($unassigned as $p)
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            name="permission_ids[]" value="{{ $p->id }}"
                                                                            id="perm_{{ $role->id }}_{{ $p->id }}">
                                                                        <label class="form-check-label" for="perm_{{ $role->id }}_{{ $p->id }}">
                                                                            {{ $p->name }}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <button type="submit" class="btn btn-sm btn-primary">
                                                            <i class="bi bi-check-circle me-1"></i>
                                                            Assign Selected
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        @endcan

                                        {{-- Delete Role --}}
                                        @can('delete-roles')
                                            <td>
                                                @unless ($isMerchantAdminRole)
                                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                        data-bs-target="#confirmDeleteRoleModal"
                                                        data-action="{{ route('merchant.roles.destroy', $role->id) }}"
                                                        data-role="{{ $role->name }}">
                                                        <i class="bi bi-trash me-1"></i>
                                                        Delete
                                                    </button>
                                                @else
                                                    <span class="text-muted">
                                                        <i class="bi bi-lock me-1"></i>
                                                        Protected
                                                    </span>
                                                @endunless
                                            </td>
                                        @endcan
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Revoke Permission Modal --}}
        @can('edit-roles')
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
        @endcan

        {{-- Delete Role Modal --}}
        @can('delete-roles')
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
                            <p class="mb-0">Are you sure you want to delete role: <strong id="delete-role-name"></strong>?</p>
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
    </div>
@endsection

@push('scripts')
    @can('edit-roles')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const revokeModal = document.getElementById('confirmRevokeModal');
                const revokeForm = document.getElementById('revoke-form');
                const revokeName = document.getElementById('revoke-permission-name');

                revokeModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    revokeForm.action = button.getAttribute('data-action');
                    revokeName.textContent = button.getAttribute('data-permission');
                });
            });
        </script>
    @endcan

    @can('delete-roles')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const deleteModal = document.getElementById('confirmDeleteRoleModal');
                const deleteForm = document.getElementById('delete-role-form');
                const roleName = document.getElementById('delete-role-name');

                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    deleteForm.action = button.getAttribute('data-action');
                    roleName.textContent = button.getAttribute('data-role');
                });
            });
        </script>
    @endcan
@endpush
