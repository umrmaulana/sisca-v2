<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SISCA')</title>

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
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    {{-- JS for barcode generation --}}
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

    @stack('styles')
</head>

<body>
    <!-- Sidebar -->
    @include('.layouts.partials.sidebar')

    <!-- Main Content Wrapper -->
    <div id="content" class="d-flex flex-column">
        <!-- Top Navigation -->
        @include('.layouts.partials.navbar')

        <!-- Page Content -->
        <div class="container-fluid flex-grow-1">
            @yield('content')
        </div>

        <!-- Scroll to Top Button -->
        <button id="scrollToTopBtn" title="Back to Top">
            <i class="fas fa-chevron-up"></i>
        </button>

        <!-- Footer -->
        @include('.layouts.partials.footer')
    </div>

    <!-- jQuery (load first) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    {{-- Notification - Only for P3K module --}}
    @if (request()->is('p3k*') || str_contains(request()->path(), 'p3k'))
        <script>
            const notificationsUrl = @json(route('p3k.notifications'));
        </script>
    @endif
    <!-- Custom JavaScript V2 -->
    <script src="{{ url('js/scriptv2.js') }}"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @stack('scripts')
</body>

</html>
