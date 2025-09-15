<nav class="menu" id="app_menu">
        <div class="menu-item menu-accordion {{ Request::is('dashboard*', 'dashboard/location*', 'dashboard/master*', 'dashboard/check-sheet*', 'dashboard/report*') ? 'show' : '' }}">
            <div class="menu-link {{ Request::is('dashboard*', 'dashboard/location*', 'dashboard/master*', 'dashboard/check-sheet*', 'dashboard/report*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#checksheetSafetySubmenu">
                <span class="menu-icon">
                    <i class="bi bi-shield-check"></i>
                </span>
                <span class="menu-title">Checksheet Safety</span>
                <span class="menu-arrow">
                    <i class="fas fa-chevron-down"></i>
                </span>
            </div>
            
            <div class="menu-sub menu-sub-accordion collapse {{ Request::is('dashboard*', 'dashboard/location*', 'dashboard/master*', 'dashboard/check-sheet*', 'dashboard/report*') ? 'show' : '' }}" id="checksheetSafetySubmenu">
                {{-- Dashboard Link --}}
                <div class="menu-item">
                    <a class="menu-link {{ Request::is('dashboard') ? 'active' : '' }}" href="/dashboard">
                        <span class="menu-icon">
                            <i class="bi bi-speedometer2"></i>
                        </span>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </div>
                
                <!-- Location Section -->
                <div class="menu-item menu-accordion {{ Request::is('dashboard/location*') ? 'show' : '' }}">
                    <div class="menu-link {{ Request::is('dashboard/location*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#locationSubmenu">
                        <span class="menu-icon">
                            <i class="bi bi-geo-alt-fill"></i>
                        </span>
                        <span class="menu-title">Location</span>
                        <span class="menu-arrow">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </div>
                    
                    <div class="menu-sub menu-sub-accordion collapse {{ Request::is('dashboard/location*') ? 'show' : '' }}" id="locationSubmenu">
                        @if (Auth::check() && Auth::user()->role === 'Admin')
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/location/all-equipment*') ? 'active' : '' }}" href="/dashboard/location/all-equipment">
                                    <span class="menu-title">All Equipment</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/location/apar*') ? 'active' : '' }}" href="/dashboard/location/apar">
                                    <span class="menu-title">Apar</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/location/hydrant*') ? 'active' : '' }}" href="/dashboard/location/hydrant">
                                    <span class="menu-title">Hydrant</span>
                                </a>
                            </div>
                        @endif
                        @if (Auth::check() && (Auth::user()->role === 'Admin' || Auth::user()->role === 'MTE'))
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/location/headcrane*') ? 'active' : '' }}" href="/dashboard/location/headcrane">
                                    <span class="menu-title">HeadCrane</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Master Section -->
                <div class="menu-item menu-accordion {{ Request::is('dashboard/master*') ? 'show' : '' }}">
                    <div class="menu-link {{ Request::is('dashboard/master*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#masterSubmenu">
                        <span class="menu-icon">
                            <i class="bi bi-database-fill-gear"></i>
                        </span>
                        <span class="menu-title">Master</span>
                        <span class="menu-arrow">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </div>
                    
                    <div class="menu-sub menu-sub-accordion collapse {{ Request::is('dashboard/master*') ? 'show' : '' }}" id="masterSubmenu">
                        @if (Auth::user()->role == 'MTE')
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/master/location*') ? 'active' : '' }}" href="/dashboard/master/location">
                                    <span class="menu-title">Location</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/master/head-crane*') ? 'active' : '' }}" href="/dashboard/master/head-crane">
                                    <span class="menu-title">Head Crane</span>
                                </a>
                            </div>
                        @else
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/master/location*') ? 'active' : '' }}" href="/dashboard/master/location">
                                    <span class="menu-title">Location</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/master/apar*') ? 'active' : '' }}" href="/dashboard/master/apar">
                                    <span class="menu-title">Apar</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/master/hydrant*') ? 'active' : '' }}" href="/dashboard/master/hydrant">
                                    <span class="menu-title">Hydrant</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/master/nitrogen*') ? 'active' : '' }}" href="/dashboard/master/nitrogen">
                                    <span class="menu-title">Nitrogen</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/master/co2*') ? 'active' : '' }}" href="/dashboard/master/co2">
                                    <span class="menu-title">Co2</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/master/tandu*') ? 'active' : '' }}" href="/dashboard/master/tandu">
                                    <span class="menu-title">Tandu</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/master/eye-washer*') ? 'active' : '' }}" href="/dashboard/master/eye-washer">
                                    <span class="menu-title">Eye Washer</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/master/sling*') ? 'active' : '' }}" href="/dashboard/master/sling">
                                    <span class="menu-title">Sling</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/master/tembin*') ? 'active' : '' }}" href="/dashboard/master/tembin">
                                    <span class="menu-title">Tembin</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/master/chain-block*') ? 'active' : '' }}" href="/dashboard/master/chain-block">
                                    <span class="menu-title">Chain Block</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/master/body-harnest*') ? 'active' : '' }}" href="/dashboard/master/body-harnest">
                                    <span class="menu-title">Body Harnest</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/master/safety-belt*') ? 'active' : '' }}" href="/dashboard/master/safety-belt">
                                    <span class="menu-title">Safety Belt</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/master/facp*') ? 'active' : '' }}" href="/dashboard/master/facp">
                                    <span class="menu-title">FACP</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/master/head-crane*') ? 'active' : '' }}" href="/dashboard/master/head-crane">
                                    <span class="menu-title">Head Crane</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Check Sheet Section -->
                <div class="menu-item menu-accordion {{ Request::is('dashboard/check-sheet*') ? 'show' : '' }}">
                    <div class="menu-link {{ Request::is('dashboard/check-sheet*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#checksheetSubmenu">
                        <span class="menu-icon">
                            <i class="bi bi-clipboard2-check-fill"></i>
                        </span>
                        <span class="menu-title">Check Sheet</span>
                        <span class="menu-arrow">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </div>
                    
                    <div class="menu-sub menu-sub-accordion collapse {{ Request::is('dashboard/check-sheet*') ? 'show' : '' }}" id="checksheetSubmenu">
                        @if (Auth::user()->role == 'MTE')
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/check-sheet/head-crane*') ? 'active' : '' }}" href="/dashboard/check-sheet/head-crane">
                                    <span class="menu-title">Head Crane</span>
                                </a>
                            </div>
                        @else
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/check-sheet/apar*') ? 'active' : '' }}" href="/dashboard/check-sheet/apar">
                                    <span class="menu-title">Apar</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/check-sheet/hydrant*') ? 'active' : '' }}" href="/dashboard/check-sheet/hydrant">
                                    <span class="menu-title">Hydrant</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/check-sheet/nitrogen*') ? 'active' : '' }}" href="/dashboard/check-sheet/nitrogen">
                                    <span class="menu-title">Nitrogen</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/check-sheet/co2*') ? 'active' : '' }}" href="/dashboard/check-sheet/co2">
                                    <span class="menu-title">Co2</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/check-sheet/tandu*') ? 'active' : '' }}" href="/dashboard/check-sheet/tandu">
                                    <span class="menu-title">Tandu</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/check-sheet/eye-washer*') ? 'active' : '' }}" href="/dashboard/check-sheet/eye-washer">
                                    <span class="menu-title">Eye Washer</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/check-sheet/sling*') ? 'active' : '' }}" href="/dashboard/check-sheet/sling">
                                    <span class="menu-title">Sling</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/check-sheet/tembin*') ? 'active' : '' }}" href="/dashboard/check-sheet/tembin">
                                    <span class="menu-title">Tembin</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/check-sheet/chainblock*') ? 'active' : '' }}" href="/dashboard/check-sheet/chainblock">
                                    <span class="menu-title">Chain Block</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/check-sheet/bodyharnest*') ? 'active' : '' }}" href="/dashboard/check-sheet/bodyharnest">
                                    <span class="menu-title">Body Harnest</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/check-sheet/safetybelt*') ? 'active' : '' }}" href="/dashboard/check-sheet/safetybelt">
                                    <span class="menu-title">Safety Belt</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/check-sheet/facp*') ? 'active' : '' }}" href="/dashboard/check-sheet/facp">
                                    <span class="menu-title">FACP</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/check-sheet/head-crane*') ? 'active' : '' }}" href="/dashboard/check-sheet/head-crane">
                                    <span class="menu-title">Head Crane</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Report Section -->
                <div class="menu-item menu-accordion {{ Request::is('dashboard/report*') ? 'show' : '' }}">
                    <div class="menu-link {{ Request::is('dashboard/report*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#reportSubmenu">
                        <span class="menu-icon">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </span>
                        <span class="menu-title">Report</span>
                        <span class="menu-arrow">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </div>
                    
                    <div class="menu-sub menu-sub-accordion collapse {{ Request::is('dashboard/report*') ? 'show' : '' }}" id="reportSubmenu">
                        @if (Auth::user()->role == 'MTE')
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/report/headcrane*') ? 'active' : '' }}" href="/dashboard/report/headcrane">
                                    <span class="menu-title">Head Crane</span>
                                </a>
                            </div>
                        @else
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/report/apar*') ? 'active' : '' }}" href="/dashboard/report/apar">
                                    <span class="menu-title">Apar</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/report/hydrant*') ? 'active' : '' }}" href="/dashboard/report/hydrant">
                                    <span class="menu-title">Hydrant</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/report/nitrogen*') ? 'active' : '' }}" href="/dashboard/report/nitrogen">
                                    <span class="menu-title">Nitrogen</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/report/co2*') ? 'active' : '' }}" href="/dashboard/report/co2">
                                    <span class="menu-title">Co2</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/report/tandu*') ? 'active' : '' }}" href="/dashboard/report/tandu">
                                    <span class="menu-title">Tandu</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/report/eyewasher*') ? 'active' : '' }}" href="/dashboard/report/eyewasher">
                                    <span class="menu-title">Eye Washer</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/report/sling*') ? 'active' : '' }}" href="/dashboard/report/sling">
                                    <span class="menu-title">Sling</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/report/tembin*') ? 'active' : '' }}" href="/dashboard/report/tembin">
                                    <span class="menu-title">Tembin</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/report/chainblock*') ? 'active' : '' }}" href="/dashboard/report/chainblock">
                                    <span class="menu-title">Chain Block</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/report/bodyharnest*') ? 'active' : '' }}" href="/dashboard/report/bodyharnest">
                                    <span class="menu-title">Body Harnest</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/report/safetybelt*') ? 'active' : '' }}" href="/dashboard/report/safetybelt">
                                    <span class="menu-title">Safety Belt</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/report/facp*') ? 'active' : '' }}" href="/dashboard/report/facp">
                                    <span class="menu-title">FACP</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ Request::is('dashboard/report/headcrane*') ? 'active' : '' }}" href="/dashboard/report/headcrane">
                                    <span class="menu-title">Head Crane</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @if (Auth::check() && Auth::user()->role === 'Admin')
        <div class="menu-item">
            <a class="menu-link" href="#">
                <i class="bi bi-people-fill menu-icon"></i>
                <span class="menu-title">User Management</span>
            </a>
        </div>
        @endif
</nav>