@extends('layouts.dashboard-layout')

@section('title', 'Merchant Roles')

@section('content')
    <div class="container mt-4">
        <h2>Roles Management - {{ $merchant->name }}</h2>

        {{-- Create Role --}}
        @can('create-roles')
            <div class="card mb-4">
                <div class="card-header">Create New Role</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('merchant.roles.store') }}">
                        @csrf
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <label for="role_name" class="col-form-label">Role Name</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" id="role_name" name="role_name" class="form-control" required>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">Create Role</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endcan

        {{-- Roles List --}}
        <div class="card mb-4">
            <div class="card-header">Roles ({{ $roles->count() }})</div>
            <div class="card-body p-0" style="max-height: 500px; overflow-y: auto;">
                @if ($roles->isEmpty())
                    <p class="p-3 text-muted mb-0">No roles found for this merchant.</p>
                @else
                    <table class="table mb-0">
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
                                    <td class="align-middle">{{ $role->name }}</td>

                                    {{-- Permissions List --}}
                                    @can('view-permissions')
                                        <td>
                                            @if ($role->permissions->isEmpty())
                                                <em>None</em>
                                            @else
                                                <ul class="list-unstyled mb-0" style="max-height: 150px; overflow-y: auto;">
                                                    @foreach ($role->permissions as $perm)
                                                        <li class="d-flex justify-content-between align-items-center py-1">
                                                            <span>{{ $perm->name }}</span>
                                                            @can('edit-roles')
                                                                @unless ($isMerchantAdminRole)
                                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                                        data-bs-toggle="modal" data-bs-target="#confirmRevokeModal"
                                                                        data-action="{{ route('merchant.roles.revokePermission', [$role->id, $perm->id]) }}"
                                                                        data-permission="{{ $perm->name }}">
                                                                        Remove
                                                                    </button>
                                                                @endunless
                                                            @endcan
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </td>
                                    @endcan

                                    {{-- Assign Permissions --}}
                                    @can('edit-roles')
                                        <td>
                                            @if ($isMerchantAdminRole)
                                                <span class="text-muted">Merchant Admin role cannot be modified.</span>
                                            @elseif ($unassigned->isEmpty())
                                                <span class="text-muted">All permissions assigned.</span>
                                            @else
                                                <form method="POST"
                                                    action="{{ route('merchant.roles.assignPermission', $role->id) }}">
                                                    @csrf
                                                    <div class="mb-2">
                                                        <label class="form-label fw-bold fs-6">Select Permissions:</label>
                                                        <div class="border rounded p-2"
                                                            style="max-height: 160px; overflow-y: auto;">
                                                            @foreach ($unassigned as $p)
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="permission_ids[]" value="{{ $p->id }}"
                                                                        id="perm_{{ $role->id }}_{{ $p->id }}">
                                                                    <label class="form-check-label"
                                                                        for="perm_{{ $role->id }}_{{ $p->id }}">
                                                                        {{ $p->name }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-primary">Assign
                                                        Selected</button>
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
                                                    Delete
                                                </button>
                                            @else
                                                <span class="text-muted">Protected</span>
                                            @endunless
                                        </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>


        {{-- Revoke Permission Modal --}}
        @can('edit-roles')
            <div class="modal fade" id="confirmRevokeModal" tabindex="-1" aria-labelledby="confirmRevokeModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" id="revoke-form" class="modal-content">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title">Revoke Permission</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to revoke permission: <strong id="revoke-permission-name"></strong>?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Revoke</button>
                        </div>
                    </form>
                </div>
            </div>
        @endcan

        {{-- Delete Role Modal --}}
        @can('delete-roles')
            <div class="modal fade" id="confirmDeleteRoleModal" tabindex="-1" aria-labelledby="confirmDeleteRoleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" id="delete-role-form" class="modal-content">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title">Delete Role</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete role: <strong id="delete-role-name"></strong>?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        @endcan
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
