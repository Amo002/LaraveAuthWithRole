<nav class="sidebar">
    <h4>Dashboard</h4>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                href="{{ route('dashboard') }}">Home</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}" 
                href="{{ route('users.index') }}">Users</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('invites.index') ? 'active' : '' }}" 
                href="{{ route('invites.index') }}">Invites</a>
        </li>
        <li class="nav-item">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger mt-3 w-100">Logout</button>
            </form>
        </li>
    </ul>
</nav>
