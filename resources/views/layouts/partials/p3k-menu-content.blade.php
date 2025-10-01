{{-- P3K --}}
<nav class="menu mt-3" id="app_menu">
    @if (auth()->user() && in_array(auth()->user()->role, ['Admin', 'GA', 'Supervisor', 'Management']))
        <div class="menu-item menu-accordion">
            <div class="menu-link {{ Request::is('p3k*') ? 'active' : '' }}" data-tooltip="P3K">
                <span class="menu-icon">
                    <i class="bi bi-plus-square"></i>
                </span>
                <span class="menu-title">P3K</span>
                <span class="menu-arrow">
                    <i class="fas fa-chevron-down"></i>
                </span>
            </div>
            <div class="menu-sub menu-sub-accordion menu-sub-dropdown">
                <!-- Dashboard -->
                <div class="menu-item">
                    <a class="menu-link {{ Request::is('p3k/dashboard*') ? 'active' : '' }}"
                        href="{{ route('p3k.dashboard') }}">
                        <span class="menu-icon">
                            <i class="bi bi-speedometer2"></i>
                        </span>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </div>

                <!-- Monitoring Stock -->
                <div class="menu-item">
                    <a class="menu-link {{ Request::is('p3k/monitoring-stock*') ? 'active' : '' }}"
                        href="{{ route('p3k.monitoring-stock.index') }}">
                        <span class="menu-icon">
                            <i class="bi bi-card-checklist"></i>
                        </span>
                        <span class="menu-title">Monitoring Stock</span>
                    </a>
                </div>

                <!-- Transaction and History -->
                <div class="menu-item">
                    <a class="menu-link {{ Request::is('p3k/transaction-history*') ? 'active' : '' }}"
                        href="{{ route('p3k.transaction-history.index') }}">
                        <span class="menu-icon">
                            <i class="bi bi-journal-text"></i>
                        </span>
                        <span class="menu-title">First Aid Services</span>
                    </a>
                </div>
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
                    <a class="menu-link" href="{{ route('p3k.master.index') }}">
                        <span class="menu-icon">
                            <i class="bi bi-plus-square"></i>
                        </span>
                        <span class="menu-title">P3K</span>
                    </a>
                </div>
            </div>
        </div>
    @endif
</nav>
