<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
        {{-- Left: Title --}}
        @if (request()->is('p3k*'))
        <div class="d-flex align-items-center">
            <div class="ms-2">
                <h6 class="mb-0 fw-semibold" id="navbarTitle">@yield('title', '')</h6>
            </div>
        </div>
        @endif

        {{-- Tanggal dan Jam --}}
        <div class="d-flex align-items-center">
            <div id="datetime" class="fs-7 px-3 py-2">
                {{ now()->format('l, d F Y') }}
            </div>
        </div>

        {{-- Right: Navbar Content --}}
        <div class="d-flex align-items-center">
             <!-- Home Icon -->
            <a href="{{ route('index') }}" class="text-dark me-2">
                <i class="fas fa-home fa-lg"></i>
            </a>
            {{-- Notification --}}
            @if (Request::is('p3k*'))
                <div class="dropdown mx-3">
                    <a class="nav-link position-relative" href="#" id="notifDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-bell fa-lg text-dark"></i>
                        <span id="notifCount"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="notifDropdown"
                        style="max-height: 300px; overflow-y: auto;">
                        <h6 class="dropdown-header">Notifikasi</h6>
                        <div id="notifList">
                            <span class="dropdown-item text-muted">Load...</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Divider -->
            <div class="vr mx-3 d-none d-md-block"></div>

            <!-- Brand Logo -->
            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <img src="{{ url('foto/satu-aisin-final.png') }}" style="width: 100px; height: auto;" alt="AISIN Logo"
                    class="img-fluid">
            </a>
        </div>
    </div>
</nav>
