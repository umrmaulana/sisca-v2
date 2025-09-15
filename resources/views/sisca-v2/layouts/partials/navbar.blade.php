<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
        <!-- Brand Logo -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('sisca-v2.dashboard') }}">
            <img src="{{ url('foto/satu-aisin-final.png') }}" style="width: 150px; height: auto;" alt="AISIN Logo"
                class="img-fluid">
        </a>

        <!-- User Menu -->
        {{-- <div class="navbar-nav ms-auto">
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" id="userDropdown" 
                   role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="me-2">
                        <div class="symbol bg-white text-primary rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 35px; height: 35px; font-weight: 600;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </div>
                    <div class="d-none d-md-block">
                        <div style="font-size: 0.9rem; font-weight: 500;">{{ auth()->user()->name }}</div>
                        <div style="font-size: 0.75rem; opacity: 0.8;">{{ auth()->user()->role }}</div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" style="border-radius: 12px; border: none;">
                    <li>
                        <div class="dropdown-header">
                            <strong>{{ auth()->user()->name }}</strong><br>
                            <small class="text-muted">{{ auth()->user()->role }}</small>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="#" style="border-radius: 8px; margin: 2px;">
                            <i class="fas fa-user me-2"></i> Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" style="border-radius: 8px; margin: 2px;">
                            <i class="fas fa-cog me-2"></i> Settings
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('sisca-v2.logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger" style="border-radius: 8px; margin: 2px; border: none; background: none; width: 100%; text-align: left;">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div> --}}
        <div class="text-end">
            <div class="badge bg-primary fs-6 px-3 py-2">
                {{ now()->format('l, d F Y') }}
            </div>
        </div>
    </div>
</nav>
