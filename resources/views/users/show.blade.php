@extends('layouts.app')

@section('title', 'User Details')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">User Details</h3>
                <p class="text-muted mb-0">View complete user information</p>
            </div>
            <div class="d-flex gap-2">
                @can('update', $user)
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit User
                    </a>
                @endcan
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Users
                </a>
            </div>
        </div>

        <div class="row">
            <!-- User Profile Card -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <!-- User Avatar -->
                        <div class="mx-auto mb-3"
                            style="width: 120px; height: 120px; background-color: {{ $user->is_active ? '#1e3c72' : '#6c757d' }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem; font-weight: bold;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1)) }}
                        </div>

                        <h4 class="text-primary">{{ $user->name }}</h4>
                        <p class="text-muted mb-2">{{ $user->npk }}</p>

                        <!-- Status Badge -->
                        @if ($user->is_active)
                            <span class="badge bg-success mb-3">
                                <i class="fas fa-check me-1"></i>Active
                            </span>
                        @else
                            <span class="badge bg-secondary mb-3">
                                <i class="fas fa-times me-1"></i>Inactive
                            </span>
                        @endif

                        <!-- Role Badge -->
                        <div class="mb-3">
                            @switch($user->role)
                                @case('Admin')
                                    <span class="badge bg-danger">
                                        <i class="fas fa-crown me-1"></i>{{ $user->role }}
                                    </span>
                                @break

                                @case('Supervisor')
                                    <span class="badge bg-warning">
                                        <i class="fas fa-user-tie me-1"></i>{{ $user->role }}
                                    </span>
                                @break

                                @case('Management')
                                    <span class="badge bg-info">
                                        <i class="fas fa-briefcase me-1"></i>{{ $user->role }}
                                    </span>
                                @break

                                @case('Pic')
                                    <span class="badge bg-success">
                                        <i class="fas fa-tools me-1"></i>{{ $user->role }}
                                    </span>
                                @break

                                @default
                                    <span class="badge bg-primary">
                                        <i class="fas fa-user me-1"></i>{{ $user->role }}
                                    </span>
                            @endswitch
                        </div>

                        <!-- Company Info -->
                        @if ($user->company)
                            <div class="mb-3">
                                <small class="text-muted d-block">Assigned Company</small>
                                <span class="badge bg-info">{{ $user->company->company_name }}</span>
                            </div>
                        @endif

                        <!-- Contact Info -->
                        @if ($user->email)
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="fas fa-envelope text-muted me-2"></i>
                                <a href="mailto:{{ $user->email }}" class="text-decoration-none">{{ $user->email }}</a>
                            </div>
                        @endif

                        <!-- Quick Actions -->
                        @can('update', $user)
                            <div class="mt-4 pt-3 border-top">
                                <div class="d-grid gap-2">
                                    @if (auth()->user()->role === 'Admin' && auth()->id() !== $user->id)
                                        @if ($user->is_active)
                                            <button type="button" class="btn btn-outline-warning btn-sm"
                                                onclick="toggleUserStatus(0)">
                                                <i class="fas fa-user-slash me-1"></i>Deactivate User
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-outline-success btn-sm"
                                                onclick="toggleUserStatus(1)">
                                                <i class="fas fa-user-check me-1"></i>Activate User
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- User Information Details -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-circle me-2"></i>Basic Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Full Name</label>
                                <p class="fw-bold mb-0">{{ $user->name }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">NPK</label>
                                <p class="fw-bold mb-0">{{ $user->npk }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role & Permissions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-shield-alt me-2"></i>Role & Permissions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Current Role</label>
                                <div>
                                    @switch($user->role)
                                        @case('Admin')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-crown me-1"></i>{{ $user->role }}
                                            </span>
                                        @break

                                        @case('Supervisor')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-user-tie me-1"></i>{{ $user->role }}
                                            </span>
                                        @break

                                        @case('Management')
                                            <span class="badge bg-info">
                                                <i class="fas fa-briefcase me-1"></i>{{ $user->role }}
                                            </span>
                                        @break

                                        @case('Pic')
                                            <span class="badge bg-success">
                                                <i class="fas fa-tools me-1"></i>{{ $user->role }}
                                            </span>
                                        @break

                                        @default
                                            <span class="badge bg-primary">
                                                <i class="fas fa-user me-1"></i>{{ $user->role }}
                                            </span>
                                    @endswitch
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Assigned Company</label>
                                <p class="fw-bold mb-0">
                                    @if ($user->company)
                                        <span class="badge bg-info">{{ $user->company->company_name }}</span>
                                    @else
                                        <span class="text-muted fst-italic">No company assigned</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="alert alert-info">
                                <label class="form-label text-muted">Role Permissions</label>
                                @switch($user->role)
                                    @case('Admin')
                                        <ul class="mb-0">
                                            <li>Full system access and configuration</li>
                                            <li>User management and role assignments</li>
                                            <li>System administration tasks</li>
                                            <li>Access to all reports and analytics</li>
                                        </ul>
                                    @break

                                    @case('Supervisor')
                                        <ul class="mb-0">
                                            <li>Equipment management and oversight</li>
                                            <li>Checksheet supervision and approval</li>
                                            <li>Team management within assigned company</li>
                                            <li>Access to departmental reports</li>
                                        </ul>
                                    @break

                                    @case('Management')
                                        <ul class="mb-0">
                                            <li>Access to management reports and analytics</li>
                                            <li>Strategic oversight and planning tools</li>
                                            <li>High-level system monitoring</li>
                                            <li>Performance dashboard access</li>
                                        </ul>
                                    @break

                                    @case('Pic')
                                        <ul class="mb-0">
                                            <li>Person in charge of specific tasks</li>
                                            <li>Task management and tracking</li>
                                            <li>Collaboration with team members</li>
                                            <li>Access to relevant reports</li>
                                        </ul>

                                        @default
                                            <ul class="mb-0">
                                                <li>Basic checksheet operations</li>
                                                <li>Data entry and form submission</li>
                                                <li>View assigned equipment information</li>
                                                <li>Basic reporting access</li>
                                            </ul>
                                    @endswitch
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Status & Dates -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-clock me-2"></i>Account Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Account Status</label>
                                    <div>
                                        @if ($user->is_active)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Active
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-times me-1"></i>Inactive
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Account Created</label>
                                    <p class="fw-bold mb-0">{{ $user->created_at->format('d M Y, H:i') }}</p>
                                    <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Last Updated</label>
                                    <p class="fw-bold mb-0">{{ $user->updated_at->format('d M Y, H:i') }}</p>
                                    <small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Password Updated</label>
                                    <p class="fw-bold mb-0">
                                        @if ($user->updated_at)
                                            {{ $user->updated_at->format('d M Y, H:i') }}
                                            <small class="text-muted d-block">{{ $user->updated_at->diffForHumans() }}</small>
                                        @else
                                            <span class="text-muted fst-italic">Never changed</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                function toggleUserStatus(status) {
                    const action = status ? 'activate' : 'deactivate';
                    const message = status ?
                        'Are you sure you want to activate this user? They will regain access to the system.' :
                        'Are you sure you want to deactivate this user? They will lose access to the system.';

                    if (confirm(message)) {
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

                // Copy user ID to clipboard
                function copyUserId() {
                    navigator.clipboard.writeText('{{ $user->id }}').then(function() {
                        // Show toast or alert
                        alert('User ID copied to clipboard!');
                    });
                }

                // Copy email to clipboard
                function copyEmail() {
                    @if ($user->email)
                        navigator.clipboard.writeText('{{ $user->email }}').then(function() {
                            alert('Email copied to clipboard!');
                        });
                    @endif
                }
            </script>
        @endpush

    @endsection
