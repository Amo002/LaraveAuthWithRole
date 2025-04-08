@extends('layouts.dashboard-layout')

@section('title', 'Merchants')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-shop me-2"></i>
            Merchants
        </h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMerchantModal">
            <i class="bi bi-plus-circle me-2"></i>
            Add Merchant
        </button>
    </div>

    {{-- Merchants Table --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($merchants as $merchant)
                            <tr>
                                <td>{{ $merchant->id }}</td>
                                <td>{{ $merchant->name }}</td>
                                <td>{{ $merchant->address }}</td>
                                <td>
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
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <!-- Manage button -->
                                        <a href="{{ route('admin.merchants.manage', $merchant->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-gear"></i>
                                        </a>

                                        {{-- Toggle Status --}}
                                        <form action="{{ route('admin.merchants.toggle', $merchant->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-sm {{ $merchant->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                                <i class="bi {{ $merchant->is_active ? 'bi-x-circle' : 'bi-check-circle' }}"></i>
                                            </button>
                                        </form>

                                        {{-- Delete Button --}}
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-merchant-btn"
                                            data-merchant-id="{{ $merchant->id }}" 
                                            data-merchant-name="{{ $merchant->name }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#confirmDeleteMerchantModal">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add Merchant Modal --}}
    <div class="modal fade" id="addMerchantModal" tabindex="-1" aria-labelledby="addMerchantModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.merchants.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-2"></i>
                        Add Merchant
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="merchant-name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="merchant-name" name="name"
                            value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="merchant-address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="merchant-address" name="address"
                            value="{{ old('address') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>
                        Add Merchant
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="confirmDeleteMerchantModal" tabindex="-1" aria-labelledby="confirmDeleteMerchantModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="delete-merchant-form">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                            Confirm Merchant Deletion
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">Are you sure you want to delete merchant <strong id="merchant-name-placeholder"></strong>? This action cannot be undone.</p>
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
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.delete-merchant-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const id = button.getAttribute('data-merchant-id');
                    const name = button.getAttribute('data-merchant-name');

                    document.getElementById('merchant-name-placeholder').textContent = name;
                    document.getElementById('delete-merchant-form').action =
                        `/admin/merchants/${id}`;
                });
            });

            document.getElementById('confirmDeleteMerchantModal').addEventListener('hidden.bs.modal', () => {
                document.getElementById('merchant-name-placeholder').textContent = '';
            });
        });
    </script>
@endpush
