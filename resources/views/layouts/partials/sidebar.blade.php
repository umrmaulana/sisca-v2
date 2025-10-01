<div class="sidebar" id="kt_app_sidebar">
    <!-- Logo Section -->
    <div class="sidebar-logo">
        <img src="{{ url('foto/logo.png') }}" alt="AISIN Logo" style="max-width: 150px; height: auto;" class="logo-full">
        <img src="{{ url('foto/logo-mini.png') }}" alt="AISIN Mini" style="width: 40px; height: auto;"
            class="logo-mini d-none">
    </div>

    <!-- Navigation Menu -->
    @if (request()->is('p3k*'))
        @include('.layouts.partials.p3k-menu-content')
    @elseif (request()->is('*'))
        @include('.layouts.partials.checksheet-menu-content')
    @else
        @include('.layouts.partials.menu-content')
    @endif

    <!-- User Info Section -->
    <div class="app-sidebar-user">
        <!-- User Info Dropdown - Positioned above user profile -->
        <div class="user-dropdown-menu" id="user-menu" role="menu" aria-hidden="true">
            <a class="user-menu-item" href="{{ route('profile.show') }}" role="menuitem">
                <span class="menu-icon"><i class="bi bi-person-circle me-2"></i></span>
                <span class="menu-title">Profile</span>
            </a>
            <div class="user-menu-divider"></div>
            <form method="POST" action="{{ route('logout') }}" class="user-menu-form">
                @csrf
                <button type="submit" class="user-menu-item danger w-100 border-0" role="menuitem">
                    <span class="menu-icon"><i class="bi bi-box-arrow-right me-2"></i></span>
                    <span class="menu-title">Logout</span>
                </button>
            </form>
        </div>

        <div class="user-profile-link" data-custom-dropdown="user-menu">
            <div class="symbol">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-position">{{ auth()->user()->role }}</div>
            </div>
            <div class="dropdown-arrow">
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>
    </div>
</div>

<!-- Floating Toggle FAB Button -->
<div class="sidebar-toggle-fab" id="sidebarToggle" title="Toggle Sidebar">
    <i class="fas fa-chevron-left" id="sidebarToggleIcon"></i>
</div>
