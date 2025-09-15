@extends('sisca-v2.layouts.app')

@section('title', 'Change Password')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Change Password</h3>
                <p class="text-muted mb-0">Update your account password</p>
            </div>
            <a href="{{ route('sisca-v2.profile.show') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Profile
            </a>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6><i class="fas fa-exclamation-circle me-2"></i>Please correct the following errors:</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-key me-2"></i>Change Password
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('sisca-v2.profile.update-password') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Current Password -->
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        id="current_password" name="current_password" required>
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePassword('current_password')">
                                        <i class="fas fa-eye" id="current_password_icon"></i>
                                    </button>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">Enter your current password to verify your identity</div>
                            </div>

                            <!-- New Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" required minlength="8">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="password_icon"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">Password must be at least 8 characters long</div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Confirm New Password <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" required minlength="8">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePassword('password_confirmation')">
                                        <i class="fas fa-eye" id="password_confirmation_icon"></i>
                                    </button>
                                </div>
                                <div class="form-text">Re-enter your new password to confirm</div>
                            </div>

                            <!-- Password Requirements -->
                            <div class="alert alert-info" role="alert">
                                <h6><i class="fas fa-info-circle me-2"></i>Password Requirements:</h6>
                                <ul class="mb-0 small">
                                    <li>Minimum 8 characters long</li>
                                    <li>Use a combination of letters, numbers, and special characters</li>
                                    <li>Avoid using personal information</li>
                                    <li>Different from your current password</li>
                                </ul>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('sisca-v2.profile.show') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-key me-2"></i>Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '_icon');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Password confirmation validation
        document.getElementById('password_confirmation').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;

            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
@endsection
