<nav class="menu mt-3" id="app_menu">

    <!-- Dashboard -->
    <div class="menu-item">
        <a class="menu-link {{ Request::is('sisca-v2/dashboard*') ? 'active' : '' }}"
            href="{{ route('sisca-v2.dashboard') }}">
            <span class="menu-icon">
                <i class="bi bi-speedometer2"></i>
            </span>
            <span class="menu-title">Dashboard</span>
        </a>
    </div>

    <!-- Checksheet Section -->
    <div class="menu-item">
        <a class="menu-link {{ Request::is('sisca-v2/checksheets*') ? 'active' : '' }}"
            href="{{ route('sisca-v2.checksheets.index') }}">
            <span class="menu-icon">
                <i class="bi bi-clipboard2-check-fill"></i>
            </span>
            <span class="menu-title">Checksheet</span>
        </a>
    </div>

    <!-- Mapping Area -->
    <div class="menu-item">
        <a class="menu-link {{ Request::is('sisca-v2/mapping-area*') ? 'active' : '' }}"
            href="{{ route('sisca-v2.mapping-area.index') }}">
            <span class="menu-icon">
                <i class="bi bi-geo-alt-fill"></i>
            </span>
            <span class="menu-title">Mapping Area</span>
        </a>
    </div>

    <!-- Report Section (Admin, Supervisor, Management only) -->
    @if (auth('sisca-v2')->user() && in_array(auth('sisca-v2')->user()->role, ['Admin', 'Supervisor', 'Management']))
        <div
            class="menu-item menu-accordion {{ Request::is('sisca-v2/report*', 'sisca-v2/summary-report*') ? 'show' : '' }}">
            <div class="menu-link {{ Request::is('sisca-v2/report*', 'sisca-v2/summary-report*') ? 'active' : '' }}">
                <span class="menu-icon">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </span>
                <span class="menu-title">Report</span>
                <span class="menu-arrow">
                    <i class="fas fa-chevron-down"></i>
                </span>
            </div>
            <div class="menu-sub menu-sub-accordion menu-sub-dropdown">
                <div class="menu-item">
                    <a class="menu-link {{ Request::is('sisca-v2/summary-report*') ? 'active' : '' }}"
                        href="{{ route('sisca-v2.summary-report.index') }}">
                        <span class="menu-title">Annual Summary Report</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link {{ Request::is('sisca-v2/ng-history*') ? 'active' : '' }}"
                        href="{{ route('sisca-v2.ng-history.index') }}">
                        <span class="menu-title">NG History</span>
                    </a>
                </div>
            </div>
        </div>

        <div
            class="menu-item menu-accordion {{ Request::is('sisca-v2/equipments*', 'sisca-v2/equipment-types*', 'sisca-v2/locations*', 'sisca-v2/companies*', 'sisca-v2/areas*', 'sisca-v2/checksheet-templates*', 'sisca-v2/period-checks*') ? 'show' : '' }}">
            <div
                class="menu-link {{ Request::is('sisca-v2/equipments*', 'sisca-v2/equipment-types*', 'sisca-v2/locations*', 'sisca-v2/companies*', 'sisca-v2/areas*', 'sisca-v2/checksheet-templates*', 'sisca-v2/period-checks*') ? 'active' : '' }}">
                <span class="menu-icon">
                    <i class="bi bi-database-fill-gear"></i>
                </span>
                <span class="menu-title">Master Data</span>
                <span class="menu-arrow">
                    <i class="fas fa-chevron-down"></i>
                </span>
            </div>
            <div class="menu-sub menu-sub-accordion menu-sub-dropdown">
                <div class="menu-item">
                    <a class="menu-link {{ Request::is('sisca-v2/equipments*') ? 'active' : '' }}"
                        href="{{ route('sisca-v2.equipments.index') }}">
                        <span class="menu-title">Equipment</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link {{ Request::is('sisca-v2/equipment-types*') ? 'active' : '' }}"
                        href="{{ route('sisca-v2.equipment-types.index') }}">
                        <span class="menu-title">Equipment Type</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link {{ Request::is('sisca-v2/checksheet-templates*') ? 'active' : '' }}"
                        href="{{ route('sisca-v2.checksheet-templates.index') }}">
                        <span class="menu-title">Checksheet Template</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link {{ Request::is('sisca-v2/locations*') ? 'active' : '' }}"
                        href="{{ route('sisca-v2.locations.index') }}">
                        <span class="menu-title">Location</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link {{ Request::is('sisca-v2/companies*') ? 'active' : '' }}"
                        href="{{ route('sisca-v2.companies.index') }}">
                        <span class="menu-title">Company</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link {{ Request::is('sisca-v2/areas*') ? 'active' : '' }}"
                        href="{{ route('sisca-v2.areas.index') }}">
                        <span class="menu-title">Area</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link {{ Request::is('sisca-v2/period-checks*') ? 'active' : '' }}"
                        href="{{ route('sisca-v2.period-checks.index') }}">
                        <span class="menu-title">Period</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- User Management (Admin only) -->
    @if (auth('sisca-v2')->user() && auth('sisca-v2')->user()->role === 'Admin')
        <div class="menu-item">
            <a class="menu-link {{ Request::is('sisca-v2/users*') ? 'active' : '' }}"
                href="{{ route('sisca-v2.users.index') }}">
                <span class="menu-icon">
                    <i class="bi bi-people-fill"></i>
                </span>
                <span class="menu-title">User Management</span>
            </a>
        </div>
    @endif

</nav>
