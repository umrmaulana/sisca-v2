@extends('sisca-v2.layouts.app')

@section('title', 'My Profile')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">My Profile</h3>
                <p class="text-muted mb-0">View and manage your profile information</p>
            </div>
            <div class="d-flex gap-2">
                @if ($user->role === 'Admin')
                    <a href="{{ route('sisca-v2.profile.edit') }}" class="btn btn-outline-primary">
                        <i class="fas fa-edit me-2"></i>Edit Profile
                    </a>
                @endif
                <a href="{{ route('sisca-v2.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Profile Information -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user me-2"></i>Profile Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Full Name</label>
                                <p class="fw-bold mb-3">{{ $user->name ?: '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">NPK</label>
                                <p class="fw-bold mb-3">{{ $user->npk ?: '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Role</label>
                                <p class="fw-bold mb-3">
                                    <span
                                        class="badge 
                                        @if ($user->role === 'Admin') bg-danger
                                        @elseif($user->role === 'Supervisor') bg-warning
                                        @elseif($user->role === 'Management') bg-info
                                        @else bg-secondary @endif
                                    ">
                                        {{ $user->role }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Status</label>
                                <p class="fw-bold mb-3">
                                    @if ($user->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Summary -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>Account Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 80px; height: 80px;">
                                <i class="fas fa-user fa-2x text-white"></i>
                            </div>
                        </div>

                        <div class="text-center mb-3">
                            <h6 class="mb-1">{{ $user->name }}</h6>
                            <p class="text-muted small mb-0">{{ $user->role }}</p>
                        </div>

                        <hr>

                        <div class="small">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Account Created:</span>
                                <span class="fw-bold">{{ $user->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Last Updated:</span>
                                <span class="fw-bold">{{ $user->updated_at->format('M d, Y') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Login Time:</span>
                                <span class="fw-bold">{{ now()->format('H:i') }}</span>
                            </div>
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            @if ($user->role !== 'Admin')
                                <div class="alert alert-info small mb-2" role="alert">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Only Admin can edit profile information. You can change your password.
                                </div>
                            @endif

                            <a href="{{ route('sisca-v2.profile.change-password') }}"
                                class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-key me-1"></i>Change Password
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
