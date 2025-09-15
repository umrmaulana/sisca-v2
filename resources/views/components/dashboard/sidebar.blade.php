<div id="kt_app_sidebar" class="app-sidebar">
    <!-- Logo -->
    <div class="app-sidebar-logo">
        <img src="{{ asset('foto/logo.png') }}" alt="Logo" class="logo-img">
        <img src="{{ asset('foto/logo-mini.png') }}" alt="Logo Mini" class="app-sidebar-logo-minimize">

        <!-- Toggle FAB inside logo area -->
        <div class="sidebar-toggle-fab">
            <i id="sidebarToggleIcon" class="fas fa-chevron-left"></i>
        </div>

    </div>

    <!-- Menu -->
    <x-dashboard.menu-content />

    <!-- User Info Section with Custom Dropup -->
    <div class="app-sidebar-user">
        <div class="user-profile-link" data-custom-dropdown="user-menu">
            <div class="symbol">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-position">{{ auth()->user()->role }}</div>
            </div>
            <div class="dropdown-arrow">
                <i class="fas fa-chevron-up"></i>
            </div>
        </div>
        
        <!-- Custom Dropup Menu (div-based for consistency) -->
        <div class="user-dropdown-menu" id="user-menu" role="menu" aria-hidden="true">
            <a class="user-menu-item" href="/dashboard/profile" role="menuitem">
                <span class="menu-icon"><i class="bi bi-person-circle me-2"></i></span>
                <span class="menu-title">Profile</span>
            </a>
            <div class="user-menu-divider"></div>
            <form method="POST" action="/logout" class="user-menu-form">
                @csrf
                <button type="submit" class="user-menu-item danger w-100 border-0" role="menuitem">
                    <span class="menu-icon"><i class="bi bi-box-arrow-right me-2"></i></span>
                    <span class="menu-title">Logout</span>
                </button>
            </form>
        </div>
    </div>
</div>
