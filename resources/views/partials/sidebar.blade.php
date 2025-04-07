<nav class="sidebar">
    <h4>Dashboard</h4>
    <ul class="nav flex-column">
        @php
            $user = auth()->user();
            $merchant = $user->merchant;
            $merchantIsActive = $merchant && $merchant->is_active;
            $isGlobalAdmin = $user->merchant_id === 1;

        @endphp

        {{-- Dashboard --}}
        @can('dashboard')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    href="{{ route('dashboard') }}">Dashboard</a>
            </li>
        @endcan

        {{-- Admin Only Section --}}
        @can('admin')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}"
                    href="{{ route('admin.users.index') }}">Users</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.merchants.index') ? 'active' : '' }}"
                    href="{{ route('admin.merchants.index') }}">Merchants</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('invites.index') ? 'active' : '' }}"
                    href="{{ route('invites.index') }}">Invites</a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.roles.index') ? 'active' : '' }}"
                    href="{{ route('admin.roles.index') }}">Role & Permission</a>
            </li>
        @endcan

        {{-- Merchant Section --}}
        @if ($merchantIsActive && !$isGlobalAdmin)
            {{-- Merchant Users --}}
            @can('view-merchant-users')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('merchant.users.index') ? 'active' : '' }}"
                        href="{{ route('merchant.users.index') }}">Merchant Users</a>
                </li>
            @endcan

            {{-- Role & Permission --}}
            @can('view-roles')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('merchant.roles.index') ? 'active' : '' }}"
                        href="{{ route('merchant.roles.index') }}">Role & Permission</a>
                </li>
            @endcan
        @endif

        {{-- Profile --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}"
                href="{{ route('profile') }}">Profile</a>
        </li>

        {{-- Logout --}}
        <li class="nav-item mt-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger w-100">Logout</button>
            </form>
        </li>
    </ul>
</nav>
