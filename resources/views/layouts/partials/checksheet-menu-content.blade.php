<nav class="menu mt-3" id="app_menu">
    @if (auth()->user() && in_array(auth()->user()->role, ['Admin', 'Supervisor', 'Management', 'Pic']))
        <div
            class="menu-item menu-accordion {{ Request::is('dashboard*', 'summary-report*', 'checksheets*', 'mapping-area*', 'ng-history*') ? 'show' : '' }}">
            <div class="menu-link {{ Request::is('dashboard*', 'summary-report*', 'checksheets*', 'mapping-area*', 'ng-history*') ? 'active' : '' }}"
                data-tooltip="Checksheet Safety">
                <span class="menu-icon">
                    <i class="bi bi-shield-check"></i>
                </span>
                <span class="menu-title">Checkksheet Safety</span>
                <span class="menu-arrow">
                    <i class="fas fa-chevron-down"></i>
                </span>
            </div>

            <div class="menu-sub menu-sub-accordion menu-sub-dropdown">
                <!-- Dashboard -->
                <div class="menu-item">
                    <a class="menu-link {{ Request::is('dashboard*') ? 'active' : '' }}" href="{{ route('dashboard') }}"
                        data-tooltip="Dashboard">
                        <span class="menu-icon">
                            <i class="bi bi-speedometer2"></i>
                        </span>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </div>

                <!-- Checksheet Section -->
                <div class="menu-item">
                    <a class="menu-link {{ Request::is('checksheets*') ? 'active' : '' }}"
                        href="{{ route('checksheets.index') }}" data-tooltip="Checksheet">
                        <span class="menu-icon">
                            <i class="bi bi-clipboard2-check-fill"></i>
                        </span>
                        <span class="menu-title">Checksheet</span>
                    </a>
                </div>

                <!-- Mapping Area -->
                <div class="menu-item">
                    <a class="menu-link {{ Request::is('mapping-area*') ? 'active' : '' }}"
                        href="{{ route('mapping-area.index') }}" data-tooltip="Mapping Area">
                        <span class="menu-icon">
                            <i class="bi bi-geo-alt-fill"></i>
                        </span>
                        <span class="menu-title">Mapping Area</span>
                    </a>
                </div>

                <!-- Report Section (Admin, Supervisor, Management only) -->
                @if (auth()->user() && in_array(auth()->user()->role, ['Admin', 'Supervisor', 'Management']))
                    <div
                        class="menu-item menu-accordion {{ Request::is('report*', 'summary-report*', 'ng-history*') ? 'show' : '' }}">
                        <div
                            class="menu-link {{ Request::is('report*', 'summary-report*', 'ng-history*') ? 'active' : '' }}">
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
                                <a class="menu-link {{ Request::is('summary-report*') ? 'active' : '' }}"
                                    href="{{ route('summary-report.index') }}">
                                    <span class="sub-menu-title">Annual Summary Report</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('ng-history*') ? 'active' : '' }}"
                                    href="{{ route('ng-history.index') }}">
                                    <span class="sub-menu-title">NG History</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if (auth()->user() && auth()->user()->role === 'Admin')
        <div
            class="menu-item menu-accordion {{ Request::is('equipments*', 'equipment-types*', 'locations*', 'companies*', 'areas*', 'checksheet-templates*', 'period-checks*') ? 'show' : '' }}">
            <div
                class="menu-link {{ Request::is('equipments*', 'equipment-types*', 'locations*', 'companies*', 'areas*', 'checksheet-templates*', 'period-checks*') ? 'active' : '' }}">
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
                    <a class="menu-link {{ Request::is('areas*') ? 'active' : '' }}"
                        href="{{ route('areas.index') }}">
                        <span class="sub-menu-title">Area</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link {{ Request::is('checksheet-templates*') ? 'active' : '' }}"
                        href="{{ route('checksheet-templates.index') }}">
                        <span class="sub-menu-title">Checksheet Template</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link {{ Request::is('companies*') ? 'active' : '' }}"
                        href="{{ route('companies.index') }}">
                        <span class="sub-menu-title">Company</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link {{ Request::is('equipments*') ? 'active' : '' }}"
                        href="{{ route('equipments.index') }}">
                        <span class="sub-menu-title">Equipment</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link {{ Request::is('equipment-types*') ? 'active' : '' }}"
                        href="{{ route('equipment-types.index') }}">
                        <span class="sub-menu-title">Equipment Type</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link {{ Request::is('locations*') ? 'active' : '' }}"
                        href="{{ route('locations.index') }}">
                        <span class="sub-menu-title">Location</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link {{ Request::is('period-checks*') ? 'active' : '' }}"
                        href="{{ route('period-checks.index') }}">
                        <span class="sub-menu-title">Period</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- <!-- User Management (Admin only) -->
    @if (auth()->user() && auth()->user()->role === 'Admin')
        <div class="menu-item">
            <a class="menu-link {{ Request::is('users*') ? 'active' : '' }}"
                href="{{ route('users.index') }}">
                <span class="menu-icon">
                    <i class="bi bi-people-fill"></i>
                </span>
                <span class="menu-title">User Management</span>
            </a>
        </div>
    @endif --}}
</nav>
