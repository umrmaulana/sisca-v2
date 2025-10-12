<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ url('dist/css/bootstrap-icons.css') }}">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ url('css/stylev2.css') }}">
    <!-- Favicon -->
    <link rel="icon" href="{{ url('foto/aii.ico') }}">
</head>

<body class="dark">
    <div class="glow"></div>
    @php
        $role = auth()->user()->role;
        $name = auth()->user()->name;
    @endphp

    <!-- Overlay Content -->
    <div class="hero-content">
        <!-- Greeting -->
        <div class="mb-5">
            <h3 class="text-title mb-2 fw-bold">Hi, {{ $name }}</h3>
            <p class="text-title">
                Choose your module
            </p>
        </div>

        <!-- Module Cards -->
        <div class="row justify-content-center g-4 mt-10">
            {{-- Checksheet Module - Show if user has checksheet access --}}
            @if (auth()->user()->hasModuleAccess('checksheet'))
                <div class="col-md-4 col-sm-6">
                    <a href="{{ route('dashboard') }}" class="card-clickable text-decoration-none">
                        <div id="cs" class="card card-link shadow-sm text-center py-4">
                            <div class="card-body">
                                <img src="{{ asset('foto/safety.png') }}" alt="Checksheet Logo" class="card-logo mb-3">
                                <h5 class="text-dark fw-bold mb-3">Checksheet</h5>
                                <p class="text-dark medium mt-2">
                                    Daily safety inspections, area compliance & checklist logs.
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            {{-- P3K Module - Show if user has p3k access --}}
            @if (auth()->user()->hasModuleAccess('p3k'))
                <div class="col-md-4 col-sm-6">
                    <a href="{{ route('p3k.dashboard') }}" class="card-clickable text-decoration-none">
                        <div class="card card-link shadow-sm text-center py-4">
                            <div class="card-body">
                                <img src="{{ asset('foto/p3k.png') }}" alt="P3K Logo" class="card-logo mb-3">
                                <h5 class="text-dark fw-bold mb-3">P3K</h5>
                                <p class="text-dark medium mt-2">
                                    Track first aid kits, restocking, and incident reports.
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            {{-- User Management - Show only for Admin --}}
            @if (auth()->user()->role === 'Admin')
                <div class="col-md-4 col-sm-6">
                    <a href="{{ route('users.index') }}" class="card-clickable text-decoration-none">
                        <div class="card card-link shadow-sm text-center py-4">
                            <div class="card-body">
                                <div class="fas fa-users fa-6x card-logo mb-3"></div>
                                <h5 class="text-dark fw-bold mb-3">User Management</h5>
                                <p class="text-dark medium mt-2">
                                    Manage users, roles, and permissions.
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Logout -->
    <div class="logout-btn">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-circle">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>

</body>

</html>
