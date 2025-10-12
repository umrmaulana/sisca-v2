@extends('layouts.app')

@section('title', 'Add New User')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Add New User</h3>
                <p class="text-muted mb-0">Create a new user account</p>
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Users
            </a>
        </div>

        <!-- Create User Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">User Information</h5>
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

                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">
                                        Full Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="npk" class="form-label">
                                        NPK <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control @error('npk') is-invalid @enderror"
                                        id="npk" name="npk" value="{{ old('npk') }}" required maxlength="6"
                                        oninput="if(this.value.length > 6) this.value = this.value.slice(0,6);">
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
                                        @foreach ($roles as $role)
                                            <option value="{{ $role }}"
                                                {{ old('role') == $role ? 'selected' : '' }}>
                                                {{ $role }}
                                            </option>
                                        @endforeach
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
                                                {{ old('company_id') == $company->id ? 'selected' : '' }}>
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

                            <!-- Module Permissions -->
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-cogs me-2"></i>Module Permissions
                                </label>
                                <div class="row">
                                    @foreach ($availableModules as $key => $label)
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="module_{{ $key }}" name="module_permissions[]"
                                                    value="{{ $key }}"
                                                    {{ in_array($key, old('module_permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="module_{{ $key }}">
                                                    {{ $label }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-text">Select which modules this user can access (Admin automatically gets
                                    all modules)</div>
                                @error('module_permissions')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
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

                            <h6 class="text-primary mb-3">Password Settings</h6>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">
                                        Password <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Minimum 8 characters.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">
                                        Confirm Password <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" required>
                                    <div class="form-text">Re-enter the password to confirm.</div>
                                </div>
                            </div>

                            <!-- Role Information -->
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Role Permissions:</h6>
                                <ul class="mb-0">
                                    <li><strong>Admin:</strong> Full system access, user management, and all modules</li>
                                    <li><strong>Supervisor:</strong> Supervisory access with all modules</li>
                                    <li><strong>Pic:</strong> Person in charge - module access controlled by admin</li>
                                </ul>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('users.index') }}" class="btn btn-danger">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Create User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
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

            // Auto dismiss alerts
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 50000);

            // NPK input formatting
            document.getElementById('npk').addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        </script>
    @endpush

@endsection
