@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Edit User</h3>
                <p class="text-muted mb-0">Update user account information</p>
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Users
            </a>
        </div>

        <!-- Edit User Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-edit me-2"></i>User Information
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('users.update', $user) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">
                                        Full Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="npk" class="form-label">
                                        NPK <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('npk') is-invalid @enderror"
                                        id="npk" name="npk" value="{{ old('npk', $user->npk) }}" required>
                                    @error('npk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="role" class="form-label">
                                        Role <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('role') is-invalid @enderror" id="role"
                                        name="role" required>
                                        <option value="">Select Role</option>
                                        <option value="Admin" {{ old('role', $user->role) == 'Admin' ? 'selected' : '' }}>
                                            Admin</option>
                                        <option value="Supervisor"
                                            {{ old('role', $user->role) == 'Supervisor' ? 'selected' : '' }}>Supervisor
                                        </option>
                                        <option value="Management"
                                            {{ old('role', $user->role) == 'Management' ? 'selected' : '' }}>Management
                                        </option>
                                        <option value="Pic" {{ old('role', $user->role) == 'Pic' ? 'selected' : '' }}>
                                            Pic</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="company_id" class="form-label">
                                        Company
                                    </label>
                                    <select class="form-select @error('company_id') is-invalid @enderror" id="company_id"
                                        name="company_id">
                                        <option value="">Select Company</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}"
                                                {{ old('company_id', $user->company_id) == $company->id ? 'selected' : '' }}>
                                                {{ $company->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Optional. Assign user to specific company.</div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input @error('is_active') is-invalid @enderror"
                                            type="checkbox" id="is_active" name="is_active" value="1"
                                            {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            <strong>Active Status</strong>
                                        </label>
                                        @error('is_active')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Toggle to activate or deactivate this user</div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-primary mb-0">Password Settings</h6>
                                <small class="text-muted">Leave empty to keep current password</small>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">
                                        New Password
                                    </label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Minimum 8 characters. Leave empty to keep current password.
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">
                                        Confirm New Password
                                    </label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation">
                                    <div class="form-text">Re-enter the new password to confirm.</div>
                                </div>
                            </div>

                            <!-- User Information Display -->
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>User Details:</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">User ID:</small><br>
                                        <strong>{{ $user->id }}</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Created Date:</small><br>
                                        <strong>{{ $user->created_at->format('d M Y, H:i') }}</strong>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <small class="text-muted">Last Updated:</small><br>
                                        <strong>{{ $user->updated_at->format('d M Y, H:i') }}</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Current Company:</small><br>
                                        <strong>{{ $user->company ? $user->company->company_name : 'No company assigned' }}</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Role Information -->
                            <div class="alert">
                                <h6><i class="fas fa-shield-alt me-2"></i>Role Permissions:</h6>
                                <ul class="mb-0">
                                    <li><strong>Admin:</strong> Full system access and user management</li>
                                    <li><strong>Supervisor:</strong> Equipment management and checksheet oversight</li>
                                    <li><strong>Management:</strong> Reports and analytics access</li>
                                    <li><strong>Pic:</strong> Person in charge of specific tasks</li>
                                </ul>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('users.index') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Password Reset Card -->
                @if (auth()->user()->role === 'Admin' && auth()->id() !== $user->id)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-key me-2"></i>Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h6>Reset Password</h6>
                                    <p class="text-muted small">Generate a new temporary password for this user.</p>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="resetUserPassword()">
                                        <i class="fas fa-key me-1"></i>Reset Password
                                    </button>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6>Account Status</h6>
                                    <p class="text-muted small">Quickly activate or deactivate this user account.</p>
                                    @if ($user->is_active)
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                            onclick="toggleUserStatus(0)">
                                            <i class="fas fa-user-slash me-1"></i>Deactivate
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline-success btn-sm"
                                            onclick="toggleUserStatus(1)">
                                            <i class="fas fa-user-check me-1"></i>Activate
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Password confirmation validation
            document.getElementById('password_confirmation').addEventListener('input', function() {
                const password = document.getElementById('password').value;
                const confirmPassword = this.value;

                if (password !== confirmPassword && confirmPassword !== '') {
                    this.setCustomValidity('Passwords do not match');
                    this.classList.add('is-invalid');
                } else {
                    this.setCustomValidity('');
                    this.classList.remove('is-invalid');
                }
            });

            // Clear password confirmation when password is cleared
            document.getElementById('password').addEventListener('input', function() {
                const confirmPassword = document.getElementById('password_confirmation');
                if (this.value === '') {
                    confirmPassword.value = '';
                    confirmPassword.classList.remove('is-invalid');
                    confirmPassword.setCustomValidity('');
                }
            });

            // Auto dismiss alerts
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // NPK input formatting
            document.getElementById('npk').addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });

            // Quick action functions
            function resetUserPassword() {
                if (confirm('Are you sure you want to reset this user\'s password? A temporary password will be generated.')) {
                    // Create a form to reset password
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `${window.location.origin}/users.reset-password/${$user->id}`;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    document.body.appendChild(form);
                    form.submit();
                }
            }

            function toggleUserStatus(status) {
                const action = status ? 'activate' : 'deactivate';
                if (confirm(`Are you sure you want to ${action} this user?`)) {
                    // Create a form to toggle status
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `${window.location.origin}/users.toggle-status/${$user->id}`;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    const statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.name = 'is_active';
                    statusInput.value = status;
                    form.appendChild(statusInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            }

            // Warn if editing own role
            @if (auth()->id() === $user->id)
                document.getElementById('role').addEventListener('change', function() {
                    if (this.value !== '{{ $user->role }}') {
                        alert('Warning: You are changing your own role. This may affect your access permissions.');
                    }
                });
            @endif
        </script>
    @endpush

@endsection
