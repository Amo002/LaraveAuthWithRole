@extends('layouts.dashboard-layout')

@section('title', 'Profile')

@section('content')
    <div class="container mt-4" style="max-width: 600px;">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h2 class="mb-0">
                    <i class="bi bi-shield-lock me-2"></i>
                    Two-Factor Authentication
                </h2>
            </div>
            <div class="card-body">
                {{-- QR Code Setup --}}
                @if (session('2fa:qrCode') && !auth()->user()->two_factor_secret)
                    <div class="mb-4">
                        <div class="text-center mb-4">
                            <p class="mb-3">Scan this QR Code with Google Authenticator:</p>
                            <div class="d-inline-block p-3 border rounded">
                                {!! session('2fa:qrCode') !!}
                            </div>
                            <div class="mt-3">
                                <p class="mb-2">Secret Key:</p>
                                <div class="input-group">
                                    <input type="text" class="form-control text-center" value="{{ session('2fa:secret') }}" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="copySecretKey()">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('2fa.enable') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Enter Code from Authenticator:</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-key"></i>
                                    </span>
                                    <input type="text" name="code" class="form-control" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-circle me-2"></i>
                                Verify and Enable
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Recovery Codes --}}
                @if (auth()->user()->two_factor_secret)
                    <div class="mb-4">
                        <h4 class="mb-3">
                            <i class="bi bi-key-fill me-2"></i>
                            Recovery Codes
                        </h4>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Store these recovery codes in a secure place. You can use them to access your account if you lose your authenticator device.
                        </div>
                        <div class="list-group">
                            @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true) as $code)
                                <div class="list-group-item text-center font-monospace">
                                    {{ $code }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Enable Button --}}
                @if (!auth()->user()->two_factor_secret && !session('2fa:qrCode'))
                    <form action="{{ route('2fa.setup') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-shield-check me-2"></i>
                            Enable 2FA
                        </button>
                    </form>
                @endif

                {{-- Disable & Regenerate --}}
                @if (auth()->user()->two_factor_secret)
                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-danger" onclick="openModal('authenticatorModal')">
                            <i class="bi bi-shield-x me-2"></i>
                            Disable 2FA
                        </button>
                        <button class="btn btn-warning" onclick="openModal('regenerateModal')">
                            <i class="bi bi-arrow-repeat me-2"></i>
                            Regenerate Codes
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Authenticator Modal -->
    <div id="authenticatorModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-shield-x me-2"></i>
                        Disable 2FA (Authenticator Code)
                    </h5>
                    <button type="button" class="btn-close" onclick="closeModal('authenticatorModal')"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('2fa.disable.auth') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Authenticator Code:</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-key"></i>
                                </span>
                                <input type="text" name="code" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-shield-x me-2"></i>
                            Disable
                        </button>
                    </form>
                    <p class="mt-3 text-center">
                        <a href="#" class="text-primary" onclick="switchToRecovery()">
                            <i class="bi bi-arrow-left-right me-1"></i>
                            Use recovery code instead
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recovery Modal -->
    <div id="recoveryModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-key-fill me-2"></i>
                        Disable 2FA (Recovery Code)
                    </h5>
                    <button type="button" class="btn-close" onclick="closeModal('recoveryModal')"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('2fa.disable.recovery') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Recovery Code:</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-key-fill"></i>
                                </span>
                                <input type="text" name="code" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-shield-x me-2"></i>
                            Disable
                        </button>
                    </form>
                    <p class="mt-3 text-center">
                        <a href="#" class="text-primary" onclick="switchToAuthenticator()">
                            <i class="bi bi-arrow-left-right me-1"></i>
                            Use authenticator code instead
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Regenerate Modal -->
    <div id="regenerateModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-arrow-repeat me-2"></i>
                        Regenerate Recovery Codes
                    </h5>
                    <button type="button" class="btn-close" onclick="closeModal('regenerateModal')"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        This will invalidate your existing recovery codes. Make sure to store the new codes in a secure place.
                    </div>
                    <form action="{{ route('2fa.recovery') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Authenticator Code:</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-key"></i>
                                </span>
                                <input type="text" name="code" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="bi bi-arrow-repeat me-2"></i>
                            Regenerate
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openModal(id) {
            const modal = new bootstrap.Modal(document.getElementById(id));
            modal.show();
        }

        function closeModal(id) {
            const modal = bootstrap.Modal.getInstance(document.getElementById(id));
            if (modal) modal.hide();
        }

        function switchToRecovery() {
            closeModal('authenticatorModal');
            openModal('recoveryModal');
        }

        function switchToAuthenticator() {
            closeModal('recoveryModal');
            openModal('authenticatorModal');
        }

        function copySecretKey() {
            const secretKey = "{{ session('2fa:secret') }}";
            navigator.clipboard.writeText(secretKey).then(() => {
                const button = event.target.closest('button');
                const originalIcon = button.innerHTML;
                button.innerHTML = '<i class="bi bi-check"></i>';
                setTimeout(() => {
                    button.innerHTML = originalIcon;
                }, 2000);
            });
        }
    </script>
@endpush
