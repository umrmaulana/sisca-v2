<div class="sidebar" id="sidebar">
    <!-- Logo Section -->
    <div class="sidebar-logo">
        <img src="{{ url('foto/logo.png') }}" alt="AISIN Logo" style="max-width: 150px; height: auto;" class="logo-full">
        <img src="{{ url('foto/logo-mini.png') }}" alt="AISIN Mini" style="width: 40px; height: auto;"
            class="logo-mini d-none">
    </div>

    <!-- Navigation Menu -->
    @include('sisca-v2.layouts.partials.menu-content')

    <!-- User Info Section -->
    <div class="app-sidebar-user">
        <div class="user-profile-link" data-custom-dropdown="user-menu">
            <div class="symbol">
                {{ strtoupper(substr(auth('sisca-v2')->user()->name, 0, 1)) }}
            </div>
            <div class="user-info">
                <div class="user-name">{{ auth('sisca-v2')->user()->name }}</div>
                <div class="user-position">{{ auth('sisca-v2')->user()->role }}</div>
            </div>
            <div class="dropdown-arrow">
                <i class="fas fa-chevron-up"></i>
            </div>
        </div>

        <!-- User Info Dropdown -->
        <div class="user-dropdown-menu" id="user-menu" role="menu" aria-hidden="true">
            <a class="user-menu-item" href="{{ route('sisca-v2.profile.show') }}" role="menuitem">
                <span class="menu-icon"><i class="bi bi-person-circle me-2"></i></span>
                <span class="menu-title">Profile</span>
            </a>
            <div class="user-menu-divider"></div>
            <form method="POST" action="{{ route('sisca-v2.logout') }}" class="user-menu-form">
                @csrf
                <button type="submit" class="user-menu-item danger w-100 border-0" role="menuitem">
                    <span class="menu-icon"><i class="bi bi-box-arrow-right me-2"></i></span>
                    <span class="menu-title">Logout</span>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Floating Toggle FAB Button -->
<div class="sidebar-toggle-fab" id="sidebarToggle" title="Toggle Sidebar">
    <i class="fas fa-chevron-left" id="sidebarToggleIcon"></i>
</div>
