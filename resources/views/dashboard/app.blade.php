<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SISCA PT. AISIN GROUP | @yield('title')</title>

    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Logo only -->
    <link rel="icon" href="/foto/aii.ico">

    {{-- CSS & JS Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js">
    </script>
    <link rel="stylesheet" href="{{ asset('dist/css/bootstrap-icons.css') }}">


    {{-- CSS & JS Self --}}
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/style1.css">
    <script src="/js/script.js"></script>

    <!-- Font Awesome JS -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js">
    </script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js">
    </script>

    <!-- jQuery CDN - Slim version (=without AJAX) -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js">
    </script>
    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js">
    </script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js">
    </script>
    {{-- Chart JS --}}
    <script src="{{ asset('dist/js/chart.js') }}"></script>

    {{-- ajax JS --}}
    <script src="{{ asset('dist/js/webcam.min.js') }}"></script>

    {{-- Data Table --}}
    <!-- File CSS -->
    <link rel="stylesheet" href="{{ asset('dist/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/responsive.bootstrap4.min.css') }}">

    <!-- File JavaScript -->
    <script src="{{ asset('dist/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('dist/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('dist/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('dist/js/responsive.bootstrap4.min.js') }}"></script>

    <!-- Feather Icons -->
    <script src="{{ asset('dist/js/feather.min.js') }}"></script>



</head>

<body>
    <div class="wrapper">
        <!-- Sidebar  -->
        <x-dashboard.sidebar />

        <div id="content">
            
            <x-dashboard.navbar />
            @yield('content')
            <button id="scrollToTopBtn">
                <i class="bi bi-arrow-up"></i>
            </button>
        </div>
        {{-- Footer --}}
        <x-dashboard.footer />
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const body = document.body;
            const sidebar = document.getElementById('kt_app_sidebar');
            const toggleBtn = document.querySelector('.sidebar-toggle-fab');
            const toggleIcon = document.getElementById('sidebarToggleIcon');
            const HIDE_DELAY = 200;

            const isMinimized = () => body.getAttribute('data-kt-app-sidebar-minimize') === 'on';
            const setMinimized = (on) => {
                if (on) {
                    body.setAttribute('data-kt-app-sidebar-minimize', 'on');
                } else {
                    body.removeAttribute('data-kt-app-sidebar-minimize');
                }
                updateToggleIcon();
            };

            function updateToggleIcon() {
                if (!toggleIcon) return;
                const rotation = isMinimized() ? 'rotate(180deg)' : 'rotate(0deg)';
                toggleIcon.style.transform = rotation;
                toggleIcon.style.transition = 'transform 0.3s ease';
            }

            /* Toggle */
            if (toggleBtn) {
                toggleBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const next = !isMinimized();
                    setMinimized(next);
                    if (!isMinimized()) {
                        document.querySelectorAll('#kt_app_sidebar .menu-sub.menu-sub-dropdown.show')
                            .forEach(el => el.classList.remove('show'));
                    }
                });
            }

            /* Accordions (expanded mode only) */
            function initAccordions() {
                const items = document.querySelectorAll('#kt_app_sidebar .menu-item.menu-accordion');
                
                items.forEach(acc => {
                    const link = acc.querySelector(':scope > .menu-link');
                    const sub = acc.querySelector(':scope > .menu-sub');
                    if (!link || !sub) return;

                    link.addEventListener('click', (ev) => {
                        if (isMinimized()) return; // Only work in expanded mode
                        ev.preventDefault();

                        // Close other accordions at the same level
                        const parentSub = acc.parentElement.closest('.menu-sub');
                        if (parentSub) {
                            parentSub.querySelectorAll(':scope > .menu-item.menu-accordion.show')
                                .forEach(sib => {
                                    if (sib !== acc) sib.classList.remove('show');
                                });
                        } else {
                            // If it's a top-level accordion, close other top-level accordions
                            document.querySelectorAll('#kt_app_sidebar > .menu > .menu-item.menu-accordion.show')
                                .forEach(sib => {
                                    if (sib !== acc) sib.classList.remove('show');
                                });
                        }
                        
                        // Toggle current accordion
                        acc.classList.toggle('show');
                    });
                });
            }

            /* Flyouts (minimized mode only) */
            function initFlyouts() {
                const accs = document.querySelectorAll('#kt_app_sidebar .menu-item.menu-accordion');

                accs.forEach(acc => {
                    const dd = acc.querySelector(':scope > .menu-sub.menu-sub-dropdown');
                    if (!dd) return;

                    let hideTimer = null;
                    const scheduleHide = (el) => {
                        clearTimeout(hideTimer);
                        hideTimer = setTimeout(() => hideFlyout(el), HIDE_DELAY);
                    };
                    const cancelHide = () => {
                        clearTimeout(hideTimer);
                        hideTimer = null;
                    };

                    // Only enable flyout behavior in minimized mode
                    acc.addEventListener('pointerenter', () => {
                        if (!isMinimized()) return; // Only work in minimized mode
                        cancelHide();
                        showFlyout(acc, dd);
                    });
                    acc.addEventListener('pointerleave', (e) => {
                        if (!isMinimized()) return; // Only work in minimized mode
                        const to = e.relatedTarget;
                        if (to && dd.contains(to)) return;
                        scheduleHide(dd);
                    });

                    dd.addEventListener('pointerenter', () => {
                        if (isMinimized()) cancelHide();
                    });
                    dd.addEventListener('pointerleave', (e) => {
                        if (!isMinimized()) return;
                        const to = e.relatedTarget;
                        if (to && dd.contains(to)) return;
                        scheduleHide(dd);
                    });
                });
            }

            // Expand active menus on load
            function expandActiveMenus() {
                const actives = document.querySelectorAll('#kt_app_sidebar .menu-link.active');

                actives.forEach(link => {
                    let sub = link.closest('.menu-sub');
                    while (sub) {
                        const acc = sub.closest('.menu-item.menu-accordion');
                        if (!acc) break;
                        acc.classList.add('show');

                        const accLink = acc.querySelector(':scope > .menu-link');
                        if (accLink) accLink.setAttribute('aria-expanded', 'true');

                        sub = acc.closest('.menu-sub');
                    }
                });
            }

            function clamp(v, min, max) {
                return Math.min(Math.max(v, min), max);
            }

            function showFlyout(parent, dd) {
                const linkEl = parent.querySelector(':scope > .menu-link') || parent;
                const linkRect = linkEl.getBoundingClientRect();

                const ancestorDD = parent.closest('.menu-sub.menu-sub-dropdown.show');
                const isNested = !!ancestorDD;

                const sidebarRect = sidebar.getBoundingClientRect();

                dd.classList.add('show');
                dd.style.visibility = 'hidden';

                const ddRect = dd.getBoundingClientRect();
                const rootStyles = getComputedStyle(document.documentElement);
                const GAP_ROOT = parseInt(rootStyles.getPropertyValue('--flyout-gap-root')) || 8;
                const GAP_NESTED = parseInt(rootStyles.getPropertyValue('--flyout-gap-nested')) || 2;

                const ancRect = (ancestorDD ? ancestorDD.getBoundingClientRect() : sidebarRect);
                const baseRight = ancRect.right;
                const baseLeft = ancRect.left;

                let left = baseRight + (isNested ? GAP_NESTED : GAP_ROOT);
                if (left + ddRect.width + 4 > window.innerWidth) {
                    left = baseLeft - ddRect.width - (isNested ? GAP_NESTED : GAP_ROOT);
                }

                let top = clamp(linkRect.top, 8, window.innerHeight - ddRect.height - 8);

                dd.style.left = left + 'px';
                dd.style.top = top + 'px';
                dd.style.visibility = '';

                requestAnimationFrame(() => {
                    const r = dd.getBoundingClientRect();
                    if (r.bottom > window.innerHeight - 8) {
                        dd.style.top = Math.max(8, top - (r.bottom - (window.innerHeight - 8))) + 'px';
                    }
                });
            }

            function hideFlyout(dd) {
                dd.classList.remove('show');
                dd.querySelectorAll('.menu-sub.menu-sub-dropdown.show').forEach(x => x.classList.remove('show'));
            }

            // Click outside handlers
            document.addEventListener('click', (e) => {
                if (isMinimized() && !sidebar.contains(e.target)) {
                    document.querySelectorAll('#kt_app_sidebar .menu-sub.menu-sub-dropdown.show').forEach(
                        hideFlyout);
                }
            });
            
            document.addEventListener('click', (e) => {
                if (!isMinimized() && !sidebar.contains(e.target)) {
                    document.querySelectorAll('#kt_app_sidebar .menu-item.menu-accordion.show').forEach(
                        item => item.classList.remove('show'));
                }
            });
            
            window.addEventListener('resize', () => {
                if (isMinimized()) {
                    document.querySelectorAll('#kt_app_sidebar .menu-sub.menu-sub-dropdown.show').forEach(
                        hideFlyout);
                }
            });

            // Initialize everything
            updateToggleIcon();
            initAccordions();
            initFlyouts();
            expandActiveMenus();
            
        });

        // Legacy function for backward compatibility
        function toggleSidebar() {
            const body = document.body;
            if (body.hasAttribute('data-kt-app-sidebar-minimize')) {
                body.removeAttribute('data-kt-app-sidebar-minimize');
            } else {
                body.setAttribute('data-kt-app-sidebar-minimize', 'on');
            }
            
            // Update icon manually for legacy function
            const toggleIcon = document.getElementById('sidebarToggleIcon');
            if (toggleIcon) {
                const isMin = body.hasAttribute('data-kt-app-sidebar-minimize');
                toggleIcon.style.transform = isMin ? 'rotate(180deg)' : 'rotate(0deg)';
                toggleIcon.style.transition = 'transform 0.3s ease';
            }
        }

        // test
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().substr(0, 10);
            const dateInput = document.getElementById('tanggal_pengecekan');
            if (dateInput) {
                dateInput.value = today;
            }
        });

        function previewImage(inputId, previewClass) {
            const image = document.querySelector(`#${inputId}`);
            const imgPreview = document.querySelector(`.${previewClass}`);

            imgPreview.style.display = 'block';

            const oFReader = new FileReader();
            oFReader.readAsDataURL(image.files[0]);

            oFReader.onload = function(oFREvent) {
                imgPreview.src = oFREvent.target.result;
            }
        }

        document.getElementById('scrollToTopBtn').addEventListener('click', function() {
            // Scroll kembali ke atas halaman
            window.scrollTo({
                top: 0,
                behavior: 'smooth' // Gunakan 'smooth' untuk animasi scroll
            });
        });

        function zoom(e) {
            var zoomer = e.currentTarget;
            e.offsetX ? offsetX = e.offsetX : offsetX = e.touches[0].pageX
            e.offsetY ? offsetY = e.offsetY : offsetX = e.touches[0].pageX
            x = offsetX / zoomer.offsetWidth * 100
            y = offsetY / zoomer.offsetHeight * 100
            zoomer.style.backgroundPosition = x + '% ' + y + '%';
        }

        $(document).ready(function() {
            var table = $('#dtBasicExample').DataTable();
        });

        // Custom User Dropdown Handler (without Bootstrap Popper.js)
        document.addEventListener('DOMContentLoaded', function() {
            const userProfileLink = document.querySelector('[data-custom-dropdown="user-menu"]');
            const userDropdownMenu = document.getElementById('user-menu');
            const userSection = document.querySelector('.app-sidebar-user');

            if (userProfileLink && userDropdownMenu && userSection) {
                // Toggle dropdown on click
                userProfileLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const isShown = userSection.classList.contains('show');
                    if (isShown) {
                        userSection.classList.remove('show');
                    } else {
                        userSection.classList.add('show');
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!userSection.contains(e.target)) {
                        userSection.classList.remove('show');
                    }
                });

                // Prevent dropdown from closing when clicking inside the menu
                userDropdownMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });

                // Close dropdown when sidebar is minimized
                const toggleBtn = document.querySelector('.sidebar-toggle-fab');
                if (toggleBtn) {
                    toggleBtn.addEventListener('click', function() {
                        userSection.classList.remove('show');
                    });
                }
            }
        });

        // Handle active menu state
        $(document).on('click', '.menu-link:not([data-custom-dropdown]), .menu-sub .menu-link', function(e) {
            $('.menu-link').removeClass('active');
            $(this).addClass('active');
        });

        feather.replace();
    </script>
</body>

</html>
