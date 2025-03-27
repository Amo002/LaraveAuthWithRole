<nav class="sidebar">
    <h4>Dashboard</h4>
    <ul class="nav flex-column">

        {{-- Dashboard --}}
        @can('dashboard')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    href="{{ route('dashboard') }}">Dashboard</a>
            </li>
        @endcan

        {{-- Admin Users (Only for Admin) --}}
        @can('admin')
            {{-- Users --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}"
                    href="{{ route('admin.users.index') }}">
                    Users
                </a>
            </li>

            {{-- Merchants --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.merchants.index') ? 'active' : '' }}"
                    href="{{ route('admin.merchants.index') }}">
                    Merchants
                </a>
            </li>

            {{-- Invites --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('invites.index') ? 'active' : '' }}"
                    href="{{ route('invites.index') }}">
                    Invites
                </a>
            </li>
        @endcan

        {{-- Yahala Merchant Users --}}
        @can('yahala-users')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('merchant.users.index') ? 'active' : '' }}"
                    href="{{ route('merchant.users.index') }}">Users</a>
            </li>
        @endcan

        {{-- ZeroGame Merchant Users --}}
        @can('zerogame-users')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('merchant.users.index') ? 'active' : '' }}"
                    href="{{ route('merchant.users.index') }}">Users</a>
            </li>
        @endcan

        {{-- Profile --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}"
                href="{{ route('profile') }}">Profile</a>
        </li>

        {{-- Logout --}}
        <li class="nav-item">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger mt-3 w-100">Logout</button>
            </form>
        </li>
    </ul>
</nav>
