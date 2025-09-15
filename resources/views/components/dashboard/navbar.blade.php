<!-- Page Content  -->
<div style="margin-left: 0px">

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg dashboard-navbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            
            <!-- Active Menu Text - Left Side -->
            <div class="active-menu-text">
                <h5 class="mb-0 text-black fw-bold">
                    @hasSection('page-title')
                        @yield('page-title')
                    @else
                        @if(Request::is('dashboard'))
                            Dashboard
                        @elseif(Request::is('dashboard/location*'))
                            Location - {{ ucfirst(str_replace('-', ' ', request()->segment(3) ?? 'All')) }}
                        @elseif(Request::is('dashboard/master*'))
                            Master - {{ ucfirst(str_replace('-', ' ', request()->segment(3) ?? 'All')) }}
                        @elseif(Request::is('dashboard/check-sheet*'))
                            Check Sheet - {{ ucfirst(str_replace('-', ' ', request()->segment(3) ?? 'All')) }}
                        @elseif(Request::is('dashboard/report*'))
                            Report - {{ ucfirst(str_replace('-', ' ', request()->segment(3) ?? 'All')) }}
                        @else
                            Dashboard
                        @endif
                    @endif
                </h5>
            </div>

            <!-- Logo - Right Side -->
            <div class="navbar-logo">
                <a class="navbar-brand" href="/">
                    <img src="/foto/satu-aisin-final.png" alt="Logo SISCA" class="img-fluid" style="max-width: 150px; height: auto;" id="logo-navbar">
                </a>
            </div>

        </div>
    </nav>
