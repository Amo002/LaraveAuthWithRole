<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Challenge</title>

    <!--  Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex justify-content-center align-items-center vh-100 bg-light">

    <div class="card shadow-sm border-0" style="width: 100%; max-width: 420px;">
        <div class="card-body">
            <h4 class="text-center mb-4">Two-Factor Authentication</h4>

            <!--  Success/Error Messages -->
            @if (session('error'))
                <div class="alert alert-danger text-center">{{ session('error') }}</div>
            @endif

            <!--  Authenticator Code Form -->
            <div id="authenticator-form" style="display: block;">
                <form method="POST" action="{{ route('2fa.verify.auth') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Authenticator Code:</label>
                        <input type="text" name="code" class="form-control" required autofocus>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Verify</button>
                </form>

                <!--  Switch to Recovery Code -->
                <p class="text-center mt-3">
                    <a href="#" class="text-primary text-decoration-underline" onclick="showRecoveryForm()">Use recovery code instead</a>
                </p>
            </div>

            <!--  Recovery Code Form -->
            <div id="recovery-form" style="display: none;">
                <form method="POST" action="{{ route('2fa.verify.recovery') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Recovery Code:</label>
                        <input type="text" name="code" class="form-control" required autofocus>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Verify</button>
                </form>

                <!--  Switch back to Authenticator Code -->
                <p class="text-center mt-3">
                    <a href="#" class="text-primary text-decoration-underline" onclick="showAuthenticatorForm()">Use authenticator code instead</a>
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
