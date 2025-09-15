@extends('sisca-v2.layouts.app')

@section('title', 'Edit Profile')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Edit Profile</h3>
                <p class="text-muted mb-0">Update your profile information</p>
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

        <form action="{{ route('sisca-v2.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Profile Information Form -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user-edit me-2"></i>Profile Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- Full Name -->
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $user->name) }}"
                                        {{ $canEditProfile ? '' : 'readonly' }}>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- NPK -->
                                <div class="col-md-6">
                                    <label for="npk" class="form-label">NPK <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('npk') is-invalid @enderror"
                                        id="npk" name="npk" value="{{ old('npk', $user->npk) }}"
                                        {{ $canEditProfile ? '' : 'readonly' }}>
                                    @error('npk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', $user->email) }}"
                                        {{ $canEditProfile ? '' : 'readonly' }}>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                        {{ $canEditProfile ? '' : 'readonly' }}>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Department -->
                                <div class="col-md-6">
                                    <label for="department" class="form-label">Department</label>
                                    <input type="text" class="form-control @error('department') is-invalid @enderror"
                                        id="department" name="department"
                                        value="{{ old('department', $user->department) }}"
                                        {{ $canEditProfile ? '' : 'readonly' }}>
                                    @error('department')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Position -->
                                <div class="col-md-6">
                                    <label for="position" class="form-label">Position</label>
                                    <input type="text" class="form-control @error('position') is-invalid @enderror"
                                        id="position" name="position" value="{{ old('position', $user->position) }}"
                                        {{ $canEditProfile ? '' : 'readonly' }}>
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Role -->
                                @if ($canEditProfile)
                                    <div class="col-md-6">
                                        <label for="role" class="form-label">Role <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('role') is-invalid @enderror" id="role"
                                            name="role">
                                            <option value="">Select Role</option>
                                            <option value="Admin"
                                                {{ old('role', $user->role) === 'Admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="Supervisor"
                                                {{ old('role', $user->role) === 'Supervisor' ? 'selected' : '' }}>
                                                Supervisor</option>
                                            <option value="Management"
                                                {{ old('role', $user->role) === 'Management' ? 'selected' : '' }}>
                                                Management</option>
                                            <option value="PIC"
                                                {{ old('role', $user->role) === 'PIC' ? 'selected' : '' }}>PIC</option>
                                        </select>
                                        @error('role')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @else
                                    <div class="col-md-6">
                                        <label class="form-label">Role</label>
                                        <input type="text" class="form-control" value="{{ $user->role }}" readonly>
                                        <input type="hidden" name="role" value="{{ $user->role }}">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Password Change Section -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-key me-2"></i>Change Password
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">Leave blank if you don't want to change password</p>

                            <!-- Current Password -->
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password"
                                    class="form-control @error('current_password') is-invalid @enderror"
                                    id="current_password" name="current_password">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- New Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation">
                            </div>

                            @if (!$canEditProfile)
                                <div class="alert alert-info small" role="alert">
                                    <i class="fas fa-info-circle me-1"></i>
                                    You can only change your password. Contact Admin to update other information.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('sisca-v2.profile.show') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Profile
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
