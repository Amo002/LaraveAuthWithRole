<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Complete Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    </style>
</head>

<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="card" style="width: 100%; max-width: 450px;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <i class="bi bi-person-plus-fill text-primary" style="font-size: 2.5rem;"></i>
                <h3 class="mt-3 mb-0">Complete Registration</h3>
                <p class="text-muted">Create your account</p>
            </div>

            @if (session('status') === 'error')
                <div class="alert alert-danger d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('message') }}
                </div>
            @endif

            @if (session('status') === 'success')
                <div class="alert alert-success d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('message') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register.complete') }}">
                @csrf
                <input type="hidden" name="id" value="{{ $id }}">

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input type="email" name="email" class="form-control" value="{{ $email }}" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" name="password" class="form-control" placeholder="Enter a secure password" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock-fill"></i>
                        </span>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-person-plus me-2"></i>
                    Register
                </button>
            </form>
        </div>
    </div>
</body>

</html>
