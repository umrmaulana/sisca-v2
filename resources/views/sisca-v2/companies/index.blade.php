@extends('sisca-v2.layouts.app')

@section('title', 'Companies Management')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Companies Management</h3>
                <p class="text-muted mb-0">Manage all companies in the system</p>
            </div>
            @can('create', App\Models\SiscaV2\Company::class)
                <a href="{{ route('sisca-v2.companies.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Company
                </a>
            @endcan
        </div>

        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('sisca-v2.companies.index') }}" class="row g-3">
                    <div class="col-md-5">
                        <label for="search" class="form-label">Search Company Name</label>
                        <input type="text" class="form-control" id="search" name="search"
                            value="{{ request('search') }}" placeholder="Enter company name...">
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary me-2">
                            <i class="fas fa-search me-1"></i>Search
                        </button>
                        <a href="{{ route('sisca-v2.companies.index') }}" class="btn btn-outline-danger">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Company Table -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Companies List</h5>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Company Name</th>
                                <th>Status</th>
                                <th>Locations Count</th>
                                <th>Users Count</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($companies as $index => $company)
                                <tr>
                                    <td>{{ $companies->firstItem() + $index }}</td>
                                    <td>
                                        <div class="fw-bold text-primary">{{ $company->company_name }}</div>
                                    </td>
                                    <td>
                                        @if ($company->is_active)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Active
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-times me-1"></i>Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-info">{{ $company->locations_count ?? $company->locations->count() }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-primary">{{ $company->users_count ?? $company->users->count() }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $company->created_at->format('d M Y, H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @can('view', $company)
                                                <a href="{{ route('sisca-v2.companies.show', $company) }}"
                                                    class="btn btn-outline-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan

                                            @can('update', $company)
                                                <a href="{{ route('sisca-v2.companies.edit', $company) }}"
                                                    class="btn btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan

                                            @can('delete', $company)
                                                <button type="button" class="btn btn-outline-danger"
                                                    onclick="confirmDelete({{ $company->id }})" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-industry fa-3x mb-3"></i>
                                            <p class="mb-0">No companies found</p>
                                            @can('create', App\Models\SiscaV2\Company::class)
                                                <a href="{{ route('sisca-v2.companies.create') }}"
                                                    class="btn btn-primary btn-sm mt-2">
                                                    <i class="fas fa-plus me-1"></i>Add First Company
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($companies->hasPages())
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 gap-3">
                        <div class="text-muted small">
                            Showing {{ $companies->firstItem() }} to {{ $companies->lastItem() }}
                            of {{ $companies->total() }} results
                        </div>
                        <div class="pagination-wrapper">
                            {{ $companies->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this company?</p>
                    <p class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmDelete(id) {
                const form = document.getElementById('deleteForm');
                form.action = `${window.location.origin}/sisca-v2.companies/${id}`;
                const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                modal.show();
            }

            // Auto dismiss alerts
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        </script>
    @endpush

    @push('styles')
        <style>
            /* Custom pagination styling for companies */
            .pagination-wrapper .pagination {
                margin: 0;
            }

            .pagination-wrapper .pagination .page-link {
                font-size: 0.875rem !important;
                padding: 0.5rem 0.75rem !important;
                line-height: 1.25;
                border-radius: 0.375rem !important;
                margin: 0 2px;
                border: 1px solid var(--border-color);
            }

            .pagination-wrapper .pagination .page-item:first-child .page-link,
            .pagination-wrapper .pagination .page-item:last-child .page-link {
                border-radius: 0.375rem !important;
            }

            .pagination-wrapper .pagination .page-link i,
            .pagination-wrapper .pagination .page-link svg {
                font-size: 0.75rem !important;
                width: 20px !important;
                height: 20px !important;
            }

            .pagination-wrapper .pagination .page-link:hover {
                text-decoration: none;
                background-color: var(--background-color);
                border-color: var(--primary-color);
                color: var(--primary-color);
            }

            /* Responsive pagination */
            @media (max-width: 768px) {
                .pagination-wrapper {
                    display: flex;
                    justify-content: center;
                    width: 100%;
                }

                .pagination-wrapper .pagination .page-link {
                    font-size: 0.75rem !important;
                    padding: 0.375rem 0.5rem !important;
                }

                .pagination-wrapper .pagination .page-link i,
                .pagination-wrapper .pagination .page-link svg {
                    font-size: 0.65rem !important;
                    width: 16px !important;
                    height: 16px !important;
                }

                .text-muted.small {
                    text-align: center;
                }
            }

            /* Table hover effect */
            .table tbody tr:hover {
                background-color: rgba(var(--bs-primary-rgb), 0.05);
                transform: scale(1.005);
                transition: all 0.2s ease;
            }

            /* Badge styling */
            .badge {
                font-size: 0.75rem;
            }

            /* Search form styling */
            .card .form-control:focus,
            .card .form-select:focus {
                border-color: var(--primary-color);
                box-shadow: 0 0 0 0.2rem rgba(30, 60, 114, 0.25);
            }

            /* Results info styling */
            .text-muted.small {
                font-size: 0.875rem;
            }

            /* Gap utility for mobile */
            .gap-3 {
                gap: 1rem !important;
            }
        </style>
    @endpush
@endsection
