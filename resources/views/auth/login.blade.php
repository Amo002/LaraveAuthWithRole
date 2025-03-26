<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card p-4 shadow" style="width: 350px;">
        <h4 class="text-center mb-3">Login</h4>

        {{-- Success Message --}}
        @if (session('status') === 'success')
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        {{-- Error Message --}}
        @if (session('status') === 'error')
            <div class="alert alert-danger">{{ session('message') }}</div>
        @endif

        {{-- Validation Errors --}}
        @error('email')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror

        @error('password')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</body>
</html>
