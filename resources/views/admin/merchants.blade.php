@extends('layouts.dashboard-layout')

@section('title', 'Merchants')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Merchants</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMerchantModal">Add Merchant</button>
    </div>

    {{-- Merchants Table --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Address</th>
                <th>Status</th>
                <th>Actions</th>
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
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Disabled</span>
                        @endif
                    </td>
                    <td class="d-flex gap-2">
                        {{-- Toggle Status --}}
                        <form action="{{ route('admin.merchants.toggle', $merchant->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="btn btn-sm {{ $merchant->is_active ? 'btn-warning' : 'btn-success' }}">
                                {{ $merchant->is_active ? 'Disable' : 'Enable' }}
                            </button>
                        </form>

                        {{-- Delete Button --}}
                        <button type="button" class="btn btn-sm btn-danger delete-merchant-btn"
                            data-merchant-id="{{ $merchant->id }}"
                            data-merchant-name="{{ $merchant->name }}"
                            data-bs-toggle="modal" data-bs-target="#confirmDeleteMerchantModal">
                            Delete
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Add Merchant Modal --}}
    <div class="modal fade" id="addMerchantModal" tabindex="-1" aria-labelledby="addMerchantModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.merchants.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Merchant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Merchant</button>
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
                        <h5 class="modal-title">Confirm Merchant Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete merchant <strong id="merchant-name-placeholder"></strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
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
                    document.getElementById('delete-merchant-form').action = `/admin/merchants/${id}`;
                });
            });

            document.getElementById('confirmDeleteMerchantModal').addEventListener('hidden.bs.modal', () => {
                document.getElementById('merchant-name-placeholder').textContent = '';
            });
        });
    </script>
@endpush
