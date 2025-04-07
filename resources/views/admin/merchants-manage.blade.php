@extends('layouts.dashboard-layout')

@section('title', 'Merchant Management')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-3">Manage Merchant: {{ $merchant->name }}</h1>
        <p><strong>Address:</strong> {{ $merchant->address }}</p>

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
                                <th style="width: 10%">Delete Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                @php
                                    $isMerchantAdmin = strtolower($role->name) === 'merchant_admin';
                                    $isGlobalAdmin = auth()->user()->hasRole('admin') && auth()->user()->merchant_id === 1;
                                    $canDelete = !$isMerchantAdmin || $isGlobalAdmin;
                                    $unassigned = $permissions->diff($role->permissions);
                                @endphp
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
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
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
                                        @if ($unassigned->isEmpty())
                                            <span class="text-muted">No unassigned permissions.</span>
                                        @else
                                            <form method="POST"
                                                action="{{ route('admin.merchants.roles.assignPermission', [$merchant->id, $role->id]) }}">
                                                @csrf
                                                <div class="mb-2">
                                                    <label class="form-label fw-bold fs-6">Select Permissions:</label>
                                                    <div class="border rounded p-2" style="max-height: 160px; overflow-y: auto;">
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
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    Assign Selected
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($canDelete)
                                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                data-bs-target="#confirmDeleteRoleModal"
                                                data-action="{{ route('admin.merchants.roles.destroy', [$merchant->id, $role->id]) }}"
                                                data-role="{{ $role->name }}"
                                                data-users="{{ $role->users->count() }}">
                                                Delete
                                            </button>
                                        @else
                                            <span class="text-muted">Protected</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <a href="{{ route('admin.merchants.index') }}" class="btn btn-secondary mt-3">Back to Merchants</a>
    </div>

    <!-- Revoke Modal -->
    <div class="modal fade" id="confirmRevokeModal" tabindex="-1">
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

    <!-- Delete Role Modal -->
    <div class="modal fade" id="confirmDeleteRoleModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" id="delete-role-form" class="modal-content">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Delete Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    This role is assigned to <strong id="delete-role-users"></strong> user(s).<br>
                    Are you sure you want to delete role: <strong id="delete-role-name"></strong>?
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
        document.addEventListener('DOMContentLoaded', () => {
            // Revoke permission modal
            const revokeModal = document.getElementById('confirmRevokeModal');
            revokeModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                document.getElementById('revoke-form').action = button.getAttribute('data-action');
                document.getElementById('revoke-permission-name').textContent = button.getAttribute('data-permission');
            });

            // Delete role modal
            const deleteRoleModal = document.getElementById('confirmDeleteRoleModal');
            deleteRoleModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                document.getElementById('delete-role-form').action = button.getAttribute('data-action');
                document.getElementById('delete-role-name').textContent = button.getAttribute('data-role');
                document.getElementById('delete-role-users').textContent = button.getAttribute('data-users');
            });
        });
    </script>
@endpush
