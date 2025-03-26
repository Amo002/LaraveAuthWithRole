<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication</title>

    <!--  Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <!--  Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark py-3">
        <div class="container justify-content-center">
            <div class="d-flex align-items-center gap-4">
                <!--  Home Link -->
                <a class="nav-link text-light {{ request()->routeIs('home') ? 'active' : '' }}"
                    href="{{ route('home') }}" style="cursor: pointer;">
                    Home
                </a>

                <!--  Dashboard Link (Admin or Merchant Only) -->
                @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('merchant'))
                    <a class="nav-link text-light {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}" style="cursor: pointer;">
                        Dashboard
                    </a>
                @endif

                <!--  Logout Button -->
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-danger py-1 px-3"
                        style="border-radius: 5px; cursor: pointer;">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>



    <div class="container mt-5" style="max-width: 400px;">
        <h2 class="text-center mb-4">Two-Factor Authentication</h2>

        <!--  Success and Error Messages -->
        @foreach (['success', 'error'] as $type)
            @if (session($type))
                <div class="alert alert-{{ $type === 'success' ? 'success' : 'danger' }}">
                    {{ session($type) }}
                </div>
            @endif
        @endforeach

        <!--  QR Code -->
        @if (session('2fa:qrCode') && !auth()->user()->two_factor_secret)
            <div class="mb-4">
                <p>Scan this QR Code with Google Authenticator:</p>
                {!! session('2fa:qrCode') !!}
                <p>Secret Key: <strong>{{ session('2fa:secret') }}</strong></p>

                <form action="{{ route('2fa.enable') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Enter Code from Authenticator:</label>
                        <input type="text" name="code" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Verify and Enable</button>
                </form>
            </div>
        @endif

        <!--  Recovery Codes -->
        @if (auth()->user()->two_factor_secret)
            <div class="mb-4">
                <h4>Recovery Codes</h4>
                <ul class="list-group">
                    @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true) as $code)
                        <li class="list-group-item text-center">{{ $code }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!--  Enable Button -->
        @if (!auth()->user()->two_factor_secret && !session('2fa:qrCode'))
            <form action="{{ route('2fa.setup') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success w-100 mt-3">Enable 2FA</button>
            </form>
        @endif

        <!--  Disable and Regenerate Buttons -->
        @if (auth()->user()->two_factor_secret)
            <div class="d-grid gap-2">
                <button class="btn btn-danger" onclick="openModal('authenticatorModal')">Disable 2FA</button>
                <button class="btn btn-warning" onclick="openModal('regenerateModal')">Regenerate Codes</button>
            </div>
        @endif
    </div>

    <!--  Authenticator Modal -->
    <div id="authenticatorModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 350px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Disable 2FA (Authenticator Code)</h5>
                    <button type="button" class="btn-close" onclick="closeModal('authenticatorModal')"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('2fa.disable.authenticator') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Authenticator Code:</label>
                            <input type="text" name="code" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Disable</button>
                    </form>
                    <p class="mt-3 text-center">
                        <a href="#" class="text-primary" onclick="switchToRecovery()">Use recovery code
                            instead</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!--  Recovery Code Modal -->
    <div id="recoveryModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 350px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Disable 2FA (Recovery Code)</h5>
                    <button type="button" class="btn-close" onclick="closeModal('recoveryModal')"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('2fa.disable.recovery') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Recovery Code:</label>
                            <input type="text" name="code" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Disable</button>
                    </form>
                    <p class="mt-3 text-center">
                        <a href="#" class="text-primary" onclick="switchToAuthenticator()">Use authenticator code
                            instead</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!--  Regenerate Modal -->
    <div id="regenerateModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 350px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Regenerate Recovery Codes</h5>
                    <button type="button" class="btn-close" onclick="closeModal('regenerateModal')"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('2fa.recovery') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Authenticator Code:</label>
                            <input type="text" name="code" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-warning w-100">Regenerate</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--  Scripts -->
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
    </script>

    <!--  Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
