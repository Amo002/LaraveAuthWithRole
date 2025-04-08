<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home</title>
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
        .btn {
            padding: 12px;
            border-radius: 8px;
            font-weight: 500;
        }
        .alert {
            border-radius: 8px;
            border: none;
        }
        .welcome-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .btn-logout {
            transition: all 0.2s;
        }
        .btn-logout:hover {
            transform: translateY(-2px);
        }
    </style>
</head>

<body class="d-flex flex-column justify-content-center align-items-center vh-100">
    <div class="container" style="max-width: 500px;">
        <div class="card p-4">
            <div class="text-center mb-4">
                @auth
                    <i class="bi bi-person-circle text-primary welcome-icon"></i>
                    <h2 class="mb-2">Welcome, {{ auth()->user()->name }}</h2>
                    <p class="text-muted">This is the home page for regular users.</p>
                @else
                    <i class="bi bi-globe text-primary welcome-icon"></i>
                    <h2 class="mb-2">Welcome to Amo World!</h2>
                    <p class="text-muted">Please check your email for registration link.</p>
                @endauth
            </div>

            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="d-grid gap-3">
                @auth
                    {{-- Go to Profile Button --}}
                    <a href="{{ route('profile') }}" class="btn btn-info">
                        <i class="bi bi-person me-2"></i>
                        Go to Profile
                    </a>

                    {{-- Logout Button --}}
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-logout">
                            <i class="bi bi-box-arrow-right me-2"></i>
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </div>
</body>

</html>
