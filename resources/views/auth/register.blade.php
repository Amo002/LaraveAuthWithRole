<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Complete Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card shadow-lg border-0" style="width: 400px; border-radius: 12px;">
        <div class="card-body p-4">
            <h3 class="text-center mb-4">Complete Registration</h3>

            @if (session('status') === 'error')
                <div class="alert alert-danger">
                    {{ session('message') }}
                </div>
            @endif

            @if (session('status') === 'success')
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif


            <form method="POST" action="{{ route('register.complete') }}">
                @csrf

                <input type="hidden" name="id" value="{{ $id }}">

                <div class="mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $email }}" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter a secure password"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-3">
                    Register
                </button>
            </form>

        </div>
    </div>
</body>

</html>
