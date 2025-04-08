<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Challenge</title>

    <!--  Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .form-control {
            border-radius: 8px;
            padding: 12px;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13,110,253,0.15);
        }
        .btn-primary {
            padding: 12px;
            border-radius: 8px;
            font-weight: 500;
        }
        .alert {
            border-radius: 8px;
            border: none;
        }
        .input-group-text {
            border-radius: 8px 0 0 8px;
        }
        .switch-link {
            text-decoration: none;
            transition: color 0.2s;
        }
        .switch-link:hover {
            color: #0d6efd !important;
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center vh-100">

    <div class="card" style="width: 100%; max-width: 420px;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <i class="bi bi-shield-lock-fill text-primary" style="font-size: 2.5rem;"></i>
                <h4 class="mt-3 mb-0">Two-Factor Authentication</h4>
                <p class="text-muted">Enter your verification code</p>
            </div>

            <!--  Success/Error Messages -->
            @if (session('error'))
                <div class="alert alert-danger d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            <!--  Authenticator Code Form -->
            <div id="authenticator-form" style="display: block;">
                <form method="POST" action="{{ route('2fa.verify.auth') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Authenticator Code</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-key"></i>
                            </span>
                            <input type="text" name="code" class="form-control" required autofocus>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-shield-check me-2"></i>
                        Verify
                    </button>
                </form>

                <!--  Switch to Recovery Code -->
                <p class="text-center mt-3">
                    <a href="#" class="switch-link text-primary" onclick="showRecoveryForm()">
                        <i class="bi bi-arrow-left-right me-1"></i>
                        Use recovery code instead
                    </a>
                </p>
            </div>

            <!--  Recovery Code Form -->
            <div id="recovery-form" style="display: none;">
                <form method="POST" action="{{ route('2fa.verify.recovery') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Recovery Code</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-key-fill"></i>
                            </span>
                            <input type="text" name="code" class="form-control" required autofocus>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-shield-check me-2"></i>
                        Verify
                    </button>
                </form>

                <!--  Switch back to Authenticator Code -->
                <p class="text-center mt-3">
                    <a href="#" class="switch-link text-primary" onclick="showAuthenticatorForm()">
                        <i class="bi bi-arrow-left-right me-1"></i>
                        Use authenticator code instead
                    </a>
                </p>
            </div>
        </div>
    </div>

    <!--  JS Logic -->
    <script>
        function showRecoveryForm() {
            document.getElementById('authenticator-form').style.display = 'none';
            document.getElementById('recovery-form').style.display = 'block';
        }

        function showAuthenticatorForm() {
            document.getElementById('recovery-form').style.display = 'none';
            document.getElementById('authenticator-form').style.display = 'block';
        }
    </script>

    <!--  Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
