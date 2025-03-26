@extends('layouts.dashboard-layout')

@section('title', 'Invites')

@section('content')
    <h2>Invites List</h2>

    <!-- Search Bar -->
    <form method="GET" action="{{ route('invites.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by email"
                value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-secondary">Search</button>
            @if (request('search'))
                <a href="{{ route('invites.index') }}" class="btn btn-outline-danger">Clear</a>
            @endif
        </div>
    </form>



    <!-- Invite Button -->
    <button class="btn btn-primary mb-3 float-end" data-bs-toggle="modal" data-bs-target="#inviteModal">
        Send Invite
    </button>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Expires At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invites as $invite)
                <tr>
                    <td>{{ $invite['id'] }}</td>
                    <td>{{ $invite['email'] }}</td>
                    <td>
                        {{ $invite['expires_at'] }}
                        @if ($invite['status'] === 'Valid')
                            <span class="badge bg-success ms-2">Valid</span>
                        @else
                            <span class="badge bg-danger ms-2">Expired</span>
                        @endif
                    </td>
                    <td>
                        <!-- Delete Button -->
                        <button type="button" class="btn btn-sm btn-danger delete-invite-btn"
                            data-invite-id="{{ $invite['id'] }}" data-bs-toggle="modal"
                            data-bs-target="#confirmDeleteInviteModal">
                            Delete
                        </button>

                        <!-- Resend Button -->
                        <form action="{{ route('invites.resend', $invite['id']) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning">Resend</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>

    <!-- Send Invite Modal -->
    <div class="modal fade" id="inviteModal" tabindex="-1" aria-labelledby="inviteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="send-invite-form" action="{{ route('invites.send') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="inviteModalLabel">Send Invite</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="invite-email" class="form-label">Email address</label>
                            <input type="email" name="email" id="invite-email" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Send Invite</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div class="modal fade" id="confirmDeleteInviteModal" tabindex="-1" aria-labelledby="confirmDeleteInviteLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteInviteLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this invite?
                </div>
                <div class="modal-footer">
                    <form id="delete-invite-form" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete-invite-btn">Delete</button>
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

            // Clear the ID on modal close (optional but cleaner)
            document.getElementById('confirmDeleteInviteModal').addEventListener('hidden.bs.modal', () => {
                deleteInviteId = null;
            });
        });
    </script>
@endpush
