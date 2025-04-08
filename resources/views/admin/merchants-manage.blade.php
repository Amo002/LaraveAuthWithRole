@extends('layouts.dashboard-layout')

@section('title', 'Merchant Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-gear me-2"></i>
                Manage Merchant
            </h2>
            <p class="text-muted mb-0">{{ $merchant->name }}</p>
        </div>
        <a href="{{ route('admin.merchants.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Back to Merchants
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-info-circle me-2"></i>
                        Merchant Information
                    </h5>
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Name</dt>
                        <dd class="col-sm-9">{{ $merchant->name }}</dd>
                        <dt class="col-sm-3">Address</dt>
                        <dd class="col-sm-9">{{ $merchant->address }}</dd>
                        <dt class="col-sm-3">Status</dt>
                        <dd class="col-sm-9">
                            @if ($merchant->is_active)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Active
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Disabled
                                </span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="bi bi-shield-lock me-2"></i>
                Roles ({{ $roles->count() }})
            </h5>
        </div>
        <div class="card-body p-0">
            @if ($roles->isEmpty())
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-shield-x mb-3" style="font-size: 2rem;"></i>
                    <p class="mb-0">No roles found for this merchant.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
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
                                    <td class="align-middle">
                                        <span class="fw-bold">{{ $role->name }}</span>
                                        @if ($isMerchantAdmin)
                                            <span class="badge bg-primary ms-2">Admin</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($role->permissions->isEmpty())
                                            <span class="text-muted">None</span>
                                        @else
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach ($role->permissions as $perm)
                                                    <div class="badge bg-light text-dark border">
                                                        {{ $perm->name }}
                                                        <button type="button" class="btn btn-link btn-sm text-danger p-0 ms-1"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#confirmRevokeModal"
                                                            data-action="{{ route('admin.merchants.roles.revokePermission', [$merchant->id, $role->id, $perm->id]) }}"
                                                            data-permission="{{ $perm->name }}">
                                                            <i class="bi bi-x"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
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
                                                    <label class="form-label fw-bold">Select Permissions:</label>
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
                                                    <i class="bi bi-plus-circle me-1"></i>
                                                    Assign Selected
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($canDelete)
                                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                data-bs-target="#confirmDeleteRoleModal"
                                                data-action="{{ route('admin.merchants.roles.destroy', [$merchant->id, $role->id]) }}"
                                                data-role="{{ $role->name }}"
                                                data-users="{{ $role->users->count() }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">Protected</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Revoke Modal -->
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

    <!-- Delete Role Modal -->
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
