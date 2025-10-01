@extends('layouts.app')

@section('title', 'Access Denied')

@section('content')
    <div class="container-fluid d-flex justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="bi bi-shield-exclamation text-warning" style="font-size: 5rem;"></i>
                        </div>
                        <h1 class="display-5 mb-3">Access Denied</h1>
                        <p class="lead text-muted mb-4">
                            Sorry, you don't have permission to access this resource.
                        </p>
                        <div class="mb-4">
                            <div class="alert alert-light border">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Current Role:</strong>
                                <span class="badge bg-primary">{{ Auth()->user()->role ?? 'Unknown' }}</span>
                            </div>
                        </div>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                <i class="bi bi-house me-1"></i>Back to Dashboard
                            </a>
                            <button onclick="history.back()" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Go Back
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-question-circle me-2"></i>Need Help?
                    </h5>
                </div>
                <div class="card-body">
                    <p>If you believe you should have access to this resource, please contact your administrator.</p>
                    <div class="row">
                        <div class="col-md-4">
                            <h6>Admin Access</h6>
                            <p class="text-muted small">Full system access including user management and system
                                configuration.</p>
                        </div>
                        <div class="col-md-4">
                            <h6>Supervisor Access</h6>
                            <p class="text-muted small">Can manage equipment, templates, and perform inspections.</p>
                        </div>
                        <div class="col-md-4">
                            <h6>Management Access</h6>
                            <p class="text-muted small">Read-only access to view reports and equipment information.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
