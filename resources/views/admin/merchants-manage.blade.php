@extends('layouts.dashboard-layout')

@section('title', 'Merchant Management')

@section('content')
<div class="container mt-4">
    <h1 class="mb-3">Manage Merchant: {{ $merchant->name }}</h1>
    <p><strong>Address:</strong> {{ $merchant->address }}</p>

    @if ($superAdmin)
        <p><strong>Super Admin:</strong> {{ $superAdmin->name }} ({{ $superAdmin->email }})</p>
    @else
        <p class="text-muted"><em>No super admin user found.</em></p>
    @endif

    <!-- Roles Card -->
    <div class="card mb-4">
        <div class="card-header">Roles ({{ $roles->count() }})</div>
        <div class="card-body p-0">
            @if ($roles->isEmpty())
                <p class="p-3 text-muted mb-0">No roles found for this merchant.</p>
            @else
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 25%">Role Name</th>
                            <th>Permissions</th>
                            <th style="width: 35%">Assign New Permissions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td class="align-middle">{{ $role->name }}</td>
                                <td>
                                    @if ($role->permissions->isEmpty())
                                        <em>None</em>
                                    @else
                                        <ul class="list-unstyled mb-0">
                                            @foreach ($role->permissions as $perm)
                                                <li class="d-flex justify-content-between align-items-center py-1">
                                                    <span>{{ $perm->name }}</span>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#confirmRevokeModal"
                                                        data-action="{{ route('admin.merchants.roles.revokePermission', [$merchant->id, $role->id, $perm->id]) }}"
                                                        data-permission="{{ $perm->name }}">
                                                        Remove
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $unassigned = $permissions->diff($role->permissions);
                                    @endphp

                                    @if ($unassigned->isEmpty())
                                        <span class="text-muted">No unassigned permissions.</span>
                                    @else
                                        <form action="{{ route('admin.merchants.roles.assignPermission', [$merchant->id, $role->id]) }}" method="POST">
                                            @csrf
                                            <div class="mb-2">
                                                <label for="permission_ids_{{ $role->id }}" class="form-label fw-bold fs-6">
                                                    Select Permissions:
                                                </label>
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
                                                Assign Selected
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Merchant-Specific Permissions -->
    <div class="card mb-4">
        <div class="card-header">Merchant Permissions ({{ $permissions->count() }})</div>
        <div class="card-body">
            <form action="{{ route('admin.merchants.permissions.store', $merchant->id) }}" method="POST"
                class="row g-3 mb-3">
                @csrf
                <div class="col-auto">
                    <input type="text" name="permission_name" class="form-control" placeholder="New Permission Name" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-success">Add Permission</button>
                </div>
            </form>

            @if ($permissions->isEmpty())
                <p class="text-muted mb-0">No direct permissions found for this merchant.</p>
            @else
                <ul class="list-group">
                    @foreach ($permissions as $perm)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $perm->name }}
                            <button type="button"
                                class="btn btn-sm btn-outline-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmDeleteModal"
                                data-action="{{ route('admin.merchants.permissions.destroy', [$merchant->id, $perm->id]) }}"
                                data-permission="{{ $perm->name }}">
                                Delete
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <a href="{{ route('admin.merchants.index') }}" class="btn btn-secondary">
        Back to Merchants
    </a>
</div>

<!-- Revoke Confirmation Modal -->
<div class="modal fade" id="confirmRevokeModal" tabindex="-1" aria-labelledby="confirmRevokeModalLabel" aria-hidden="true">
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="delete-form" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title">Delete Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete permission: <strong id="delete-permission-name"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const revokeModal = document.getElementById('confirmRevokeModal');
        const revokeForm = document.getElementById('revoke-form');
        const revokeName = document.getElementById('revoke-permission-name');

        revokeModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            revokeForm.action = button.getAttribute('data-action');
            revokeName.textContent = button.getAttribute('data-permission');
        });

        const deleteModal = document.getElementById('confirmDeleteModal');
        const deleteForm = document.getElementById('delete-form');
        const deleteName = document.getElementById('delete-permission-name');

        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            deleteForm.action = button.getAttribute('data-action');
            deleteName.textContent = button.getAttribute('data-permission');
        });
    });
</script>
@endpush
