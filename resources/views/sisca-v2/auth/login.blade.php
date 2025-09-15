<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SISCA V2 PT. AISIN GROUP | Login</title>

    <!-- Logo only -->
    <link rel="icon" href="/foto/aii.ico">

    <!-- CSS & JS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="{{ asset('dist/css/bootstrap-icons.css') }}">

    <!-- CSS-->
    <link rel="stylesheet" href="/css/stylev2.css">
</head>

<body class="login sidebar-collapsed">
    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-md-6">
                <div class="card border-0 shadow rounded">
                    <div class="card-header d-flex justify-content-center align-items-center">
                    </div>
                    <div class="rounded">
                        <div class="d-flex justify-content-center align-items-center bg-white">
                            <img src="{{ url('foto/satu-aisin-final.png') }}" class="img-fluid" style="width: 70%"
                                alt="Logo Aisin">
                        </div>
                        <div class="m-4 p-4">
                            <h2 class="text-center">SISCA v2 LOGIN</h2>

                            @if (session()->has('message'))
                                <div class="alert alert-success">
                                    {{ session()->get('message') }}
                                </div>
                            @endif

                            @if (session()->has('success'))
                                <div class="alert alert-success">
                                    {{ session()->get('success') }}
                                </div>
                            @endif

                            @if (session()->has('loginError'))
                                <div class="alert alert-danger">
                                    {{ session()->get('loginError') }}
                                </div>
                            @endif

                            <form action="{{ route('sisca-v2.login.submit') }}" method="POST">
                                @csrf
                                <div class="mb-3 mt-3">
                                    <label for="npk" class="form-label">NPK <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="1" min="0"
                                        class="form-control @error('npk') is-invalid @enderror" name="npk"
                                        id="npk" aria-describedby="npk" value="{{ old('npk') }}"
                                        oninput="if(this.value.length > 5) this.value = this.value.slice(0,5);"
                                        maxlength="5" autofocus required>
                                    @error('npk')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password"
                                            class="form-control @error('password') is-invalid @enderror" id="password"
                                            name="password" required>
                                        <span class="input-group-text" id="toggle-password" style="cursor: pointer;">
                                            <i class="bi bi-eye-slash" id="password-icon"></i>
                                        </span>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div id="password-error" class="invalid-feedback"></div>
                                </div>

                                <button type="submit" class="btn btn-primary center-block w-100 mt-3 mb-3">LOG
                                    IN</button>
                                <div class="text-center">
                                    <p class="text-muted mb-0">
                                        <strong>SISCA V2</strong> - System Information Safety Checksheet Aisin
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button id="scrollToTopBtn">
        <i class="bi bi-chevron-up"></i>
    </button>

    <!-- JavaScript link -->
    <script src="/js/script.js"></script>

    <!-- Link ke JS Bootstrap (Popper.js dan jQuery) -->
    <script src="{{ asset('dist/js/jquery-3.5.1.slim.min.js') }}"></script>
    <script src="{{ asset('dist/js/popper.min.js') }}"></script>
    <script src="{{ asset('dist/js/bootstrap.min.js') }}"></script>

    <script>
        const passwordInput = document.getElementById('password');
        const passwordIcon = document.getElementById('password-icon');
        const passwordError = document.getElementById('password-error');

        document.getElementById('toggle-password').addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('bi-eye-slash');
                passwordIcon.classList.add('bi-eye');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('bi-eye');
                passwordIcon.classList.add('bi-eye-slash');
            }
        });

        // Remove error message when user clicks on input
        passwordInput.addEventListener('focus', function() {
            passwordError.textContent = '';
            passwordInput.classList.remove('is-invalid');
        });

        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

</body>

</html>
