<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SISCA V2')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ url('dist/css/bootstrap-icons.css') }}">
    <!-- Custom CSS V2 -->
    <link rel="stylesheet" href="{{ url('css/stylev2.css') }}">
    <!-- Favicon -->
    <link rel="icon" href="{{ url('foto/aii.ico') }}">

    @stack('styles')
</head>

<body>
    <!-- Sidebar -->
    @include('sisca-v2.layouts.partials.sidebar')

    <!-- Main Content Wrapper -->
    <div id="content" class="d-flex flex-column">
        <!-- Top Navigation -->
        @include('sisca-v2.layouts.partials.navbar')

        <!-- Page Content -->
        <div class="container-fluid flex-grow-1">
            @yield('content')
        </div>

        <!-- Scroll to Top Button -->
        <button id="scrollToTopBtn" title="Back to Top">
            <i class="fas fa-chevron-up"></i>
        </button>

        <!-- Footer -->
        @include('sisca-v2.layouts.partials.footer')
    </div>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JavaScript V2 -->
    <script src="{{ url('js/scriptv2.js') }}"></script>

    @stack('scripts')
</body>

</html>
