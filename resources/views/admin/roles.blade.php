@extends('layouts.dashboard-layout')

@section('title', 'Admin Roles & Permissions')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">All Roles Grouped by Merchant</h2>

        {{-- Create Role --}}
        @can('create-roles')
            <form method="POST" action="{{ route('admin.roles.store') }}" class="card card-body mb-4">
                @csrf
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <label>Role Name</label>
                        <input type="text" name="role_name" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Merchant</label>
                        <select name="merchant_id" class="form-select" required>
                            @foreach ($merchants as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mt-4">
                        <button type="submit" class="btn btn-primary mt-2">Create Role</button>
                    </div>
                </div>
            </form>
        @endcan

        {{-- Roles Grouped by Merchant --}}
        @foreach ($merchants as $merchantId => $merchantName)
            @php
                $groupedRoles = $roles->where('merchant_id', $merchantId);
            @endphp

            @if ($groupedRoles->isNotEmpty())
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between">
                        <strong>{{ $merchantName }}</strong>
                        <span class="text-muted">{{ $groupedRoles->count() }} roles</span>
                    </div>
                    <div class="card-body p-0">
                        <div style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-bordered mb-0">
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
                                            <th>Delete</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($groupedRoles as $role)
                                        @php
                                            $isProtectedAdmin =
                                                strtolower($role->name) === 'admin' && $role->merchant_id === 1;
                                            $unassignedPermissions = $permissions->diff($role->permissions);
                                        @endphp
                                        <tr>
                                            <td>{{ $role->name }}</td>

                                            {{-- View Permissions --}}
                                            @can('view-permissions')
                                                <td style="max-width: 300px;">
                                                    <ul class="list-unstyled mb-0" style="max-height: 150px; overflow-y: auto;">
                                                        @foreach ($role->permissions as $perm)
                                                            <li class="d-flex justify-content-between align-items-center py-1">
                                                                <span>{{ $perm->name }}</span>
                                                                @can('edit-roles')
                                                                    @unless ($isProtectedAdmin)
                                                                        <form method="POST"
                                                                            action="{{ route('admin.roles.revokePermission', [$role->id, $perm->id]) }}">
                                                                            @csrf @method('DELETE')
                                                                            <button
                                                                                class="btn btn-sm btn-outline-danger">Remove</button>
                                                                        </form>
                                                                    @endunless
                                                                @endcan
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                            @endcan

                                            {{-- Assign New Permissions --}}
                                            @can('edit-roles')
                                                <td style="max-width: 260px;">
                                                    @if ($isProtectedAdmin)
                                                        <span class="text-muted">Protected</span>
                                                    @elseif($unassignedPermissions->isEmpty())
                                                        <span class="text-muted">All permissions assigned.</span>
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
                                                            <button type="submit" class="btn btn-sm btn-primary mt-2">Assign
                                                                Selected</button>
                                                        </form>
                                                    @endif
                                                </td>
                                            @endcan


                                            {{-- Delete Button --}}
                                            @can('delete-roles')
                                                <td>
                                                    @if ($isProtectedAdmin)
                                                        <span class="text-muted">Protected</span>
                                                    @else
                                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                            data-bs-target="#confirmDeleteRoleModal"
                                                            data-role-name="{{ $role->name }}"
                                                            data-action="{{ route('admin.roles.destroy', $role->id) }}"
                                                            data-users-count="{{ $role->users_count }}">
                                                            Delete
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

    {{-- Delete Role Confirmation Modal --}}
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
                        <div class="mt-2">
                            This role is assigned to <strong id="delete-role-users"></strong> user(s).
                        </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('confirmDeleteRoleModal');
            const form = document.getElementById('delete-role-form');
            const name = document.getElementById('delete-role-name');
            const count = document.getElementById('delete-role-users');

            modal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                form.action = button.getAttribute('data-action');
                name.textContent = button.getAttribute('data-role-name');
                count.textContent = button.getAttribute('data-users-count');
            });
        });
    </script>
@endpush
