@extends('layouts.dashboard-layout')

@section('title', 'Invites')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-envelope me-2"></i>
            Invites List
        </h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#inviteModal">
            <i class="bi bi-plus-circle me-2"></i>
            Send Invite
        </button>
    </div>

    <!-- Search Bar -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('invites.index') }}" class="row g-3 align-items-center">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control" placeholder="Search by email"
                            value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-search me-1"></i>
                            Search
                        </button>
                        @if (request('search'))
                            <a href="{{ route('invites.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>
                                Clear
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Invites Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Expires At</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invites as $invite)
                            <tr>
                                <td>{{ $invite['id'] }}</td>
                                <td>{{ $invite['email'] }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        {{ $invite['expires_at'] }}
                                        @if ($invite['status'] === 'Valid')
                                            <span class="badge bg-success ms-2">
                                                <i class="bi bi-check-circle me-1"></i>
                                                Valid
                                            </span>
                                        @else
                                            <span class="badge bg-danger ms-2">
                                                <i class="bi bi-x-circle me-1"></i>
                                                Expired
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <!-- Delete Button -->
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-invite-btn"
                                            data-invite-id="{{ $invite['id'] }}" data-bs-toggle="modal"
                                            data-bs-target="#confirmDeleteInviteModal">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                        <!-- Resend Button -->
                                        <form action="{{ route('invites.resend', $invite['id']) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Send Invite Modal -->
    <div class="modal fade" id="inviteModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="send-invite-form" action="{{ route('invites.send') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-envelope-plus me-2"></i>
                        Send Invite
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="invite-email" class="form-label">Email address</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" name="email" id="invite-email" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>
                        Send Invite
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div class="modal fade" id="confirmDeleteInviteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="delete-invite-form" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                        Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to delete this invite?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger" id="confirm-delete-invite-btn">
                        <i class="bi bi-trash me-1"></i>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let deleteInviteId = null;

            // Capture delete button click event and set ID
            document.querySelectorAll('.delete-invite-btn').forEach(button => {
                button.addEventListener('click', () => {
                    deleteInviteId = button.getAttribute('data-invite-id');
                });
            });

            // Handle delete confirmation
            document.getElementById('confirm-delete-invite-btn').addEventListener('click', () => {
                if (deleteInviteId) {
                    const form = document.getElementById('delete-invite-form');
                    form.action = `/admin/invites/${deleteInviteId}`;
                    form.submit();
                }
            });

            // Clear the ID on modal close
            document.getElementById('confirmDeleteInviteModal').addEventListener('hidden.bs.modal', () => {
                deleteInviteId = null;
            });
        });
    </script>
@endpush
