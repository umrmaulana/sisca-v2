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
            <h3 class="text-dark mb-2 fw-bold">Hi, {{ $name }}</h3>
            <p class="text-dark fs-6">
                Welcome to the SISCA - System Information Safety Checksheet Aisin. <br>
                Choose your module to begin your checksheet or manage emergency resources.
            </p>
        </div>

        <!-- Module Cards -->
        <div class="row justify-content-center g-4 mt-10">
            {{-- Jika role = Pic, tampilkan Checksheet --}}
            @if ($role === 'Pic')
                <div class="col-md-4 col-sm-6">
                    <div id="cs" class="card card-link shadow-sm text-center py-4">
                        <div class="card-body">
                            <img src="{{ asset('foto/safety.png') }}" alt="Checksheet Logo" class="card-logo mb-3">
                            <p class="text-dark medium mt-2">
                                Daily safety inspections, area compliance & checklist logs.
                            </p>
                            <div>
                                <a href="{{ route('dashboard') }}" class="btn btn-primary">Checksheet</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Jika role = GA, tampilkan P3K --}}
            @if ($role === 'GA' || $role === 'PIC P3K')
                <div class="col-md-4 col-sm-6">
                    <div class="card card-link shadow-sm text-center py-4">
                        <div class="card-body">
                            <img src="{{ asset('foto/p3k.png') }}" alt="P3K Logo" class="card-logo mb-3">
                            <p class="text-dark medium mt-2">
                                Track first aid kits, restocking, and incident reports.
                            </p>
                            <div>
                                <a href="{{ route('p3k.dashboard') }}" class="btn btn-primary">P3K</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Jika role selain Pic & GA, tampilkan keduanya --}}
            @if ($role !== 'Pic' && $role !== 'GA' && $role !== 'PIC P3K')
                <div class="col-md-4 col-sm-6">
                    <div id="cs" class="card card-link shadow-sm text-center py-4">
                        <div class="card-body">
                            <img src="{{ asset('foto/safety.png') }}" alt="Checksheet Logo" class="card-logo mb-3">
                            <p class="text-dark medium mt-2">
                                Daily safety inspections, area compliance & checklist logs.
                            </p>
                            <div>
                                <a href="{{ route('dashboard') }}" class="btn btn-primary">Checksheet</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="card card-link shadow-sm text-center py-4">
                        <div class="card-body">
                            <img src="{{ asset('foto/p3k.png') }}" alt="P3K Logo" class="card-logo mb-3">
                            <p class="text-dark medium mt-2">
                                Track first aid kits, restocking, and incident reports.
                            </p>
                            <div>
                                <a href="{{ route('p3k.dashboard') }}" class="btn btn-primary">P3K</a>
                            </div>
                        </div>
                    </div>
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

    <!-- User Management (Admin only) -->
    @if (auth()->user() && auth()->user()->role === 'Admin')
        <a href="{{ route('users.index') }}" class="user-management-btn">
            <i class="bi bi-people-fill"></i> User Management
        </a>
    @endif


</body>

</html>
