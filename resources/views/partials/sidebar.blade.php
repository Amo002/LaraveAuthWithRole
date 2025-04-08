<nav class="sidebar">
    <div class="p-3">
        <h4 class="mb-4">
            <i class="bi bi-speedometer2 me-2"></i>
            Dashboard
        </h4>
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
                        href="{{ route('dashboard') }}">
                        <i class="bi bi-house-door"></i>
                        Dashboard
                    </a>
                </li>
            @endcan

            {{-- Admin Only Section --}}
            @can('admin')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}"
                        href="{{ route('admin.users.index') }}">
                        <i class="bi bi-people"></i>
                        Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.merchants.index') ? 'active' : '' }}"
                        href="{{ route('admin.merchants.index') }}">
                        <i class="bi bi-shop"></i>
                        Merchants
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('invites.index') ? 'active' : '' }}"
                        href="{{ route('invites.index') }}">
                        <i class="bi bi-envelope-plus"></i>
                        Invites
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.roles.index') ? 'active' : '' }}"
                        href="{{ route('admin.roles.index') }}">
                        <i class="bi bi-shield-lock"></i>
                        Role & Permission
                    </a>
                </li>
            @endcan

            {{-- Merchant Section --}}
            @if ($merchantIsActive && !$isGlobalAdmin)
                {{-- Merchant Users --}}
                @can('view-merchant-users')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('merchant.users.index') ? 'active' : '' }}"
                            href="{{ route('merchant.users.index') }}">
                            <i class="bi bi-person-badge"></i>
                            Merchant Users
                        </a>
                    </li>
                @endcan

                {{-- Role & Permission --}}
                @can('view-roles')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('merchant.roles.index') ? 'active' : '' }}"
                            href="{{ route('merchant.roles.index') }}">
                            <i class="bi bi-shield-check"></i>
                            Role & Permission
                        </a>
                    </li>
                @endcan
            @endif

            {{-- Profile --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}"
                    href="{{ route('profile') }}">
                    <i class="bi bi-person-circle"></i>
                    Profile
                </a>
            </li>

            {{-- Logout --}}
            <li class="nav-item mt-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-box-arrow-right me-2"></i>
                        Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</nav>
