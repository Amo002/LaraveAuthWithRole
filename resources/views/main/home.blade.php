<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex flex-column justify-content-center align-items-center vh-100 bg-light">
    <div class="container text-center">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @auth
            <h2>Welcome, {{ auth()->user()->name }}</h2>
            <p class="text-muted">This is the home page for regular users.</p>

            {{-- Go to Profile Button --}}
            <a href="{{ route('profile') }}" class="btn btn-info mt-2">Go to Profile</a>

            {{-- Logout Button --}}
            <form action="{{ route('logout') }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        @else
            <h2>Welcome to Amo World!</h2>
            <p class="text-muted">Please check your email for registration link.</p>
            <form action="{{ route('login') }}" method="GET" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        @endauth
    </div>
</body>

</html>
