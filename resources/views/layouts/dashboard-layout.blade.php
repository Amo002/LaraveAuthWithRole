<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Admin Dashboard')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    

    <style>
        body {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background: #343a40;
            color: white;
            padding: 15px;
            position: fixed;
        }

        .sidebar .nav-link {
            color: white;
            cursor: pointer;
        }

        .sidebar .nav-link.active {
            background-color: #007bff;
            border-radius: 5px;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            flex-grow: 1;
        }
    </style>
</head>
<body>

    {{-- Include Sidebar --}}
    @include('partials.sidebar')

    <div class="content">
        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>

    {{-- Inject Pushed Scripts --}}
    @stack('scripts')
</body>
</html>
