@extends('layouts.app')

@section('title', 'Checksheet Templates Management')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Checksheet Templates Management</h3>
                <p class="text-muted mb-0">Manage all checksheet templates in the system</p>
            </div>
            @can('create', App\Models\ChecksheetTemplate::class)
                <a href="{{ route('checksheet-templates.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Template
                </a>
            @endcan
        </div>

        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('checksheet-templates.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search Template</label>
                        <input type="text" class="form-control" id="search" name="search"
                            value="{{ request('search') }}" placeholder="Enter item name or condition...">
                    </div>
                    <div class="col-md-3">
                        <label for="equipment_type" class="form-label">Equipment Type</label>
                        <select class="form-select" id="equipment_type" name="equipment_type">
                            <option value="">All Equipment Types</option>
                            @foreach ($equipmentTypes as $type)
                                <option value="{{ $type->id }}"
                                    {{ request('equipment_type') == $type->id ? 'selected' : '' }}>
                                    {{ $type->equipment_name }} - {{ $type->equipment_type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
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
                        <a href="{{ route('checksheet-templates.index') }}" class="btn btn-outline-danger">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Templates Table -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Checksheet Templates List</h5>
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
                                <th>Order</th>
                                <th>Equipment Type</th>
                                <th>Item Name</th>
                                <th>Standard Condition</th>
                                <th>Picture</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($checksheetTemplates as $index => $template)
                                <tr>
                                    <td>{{ $checksheetTemplates->firstItem() + $index }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $template->order_number }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <div class="fw-bold text-primary">
                                                    {{ $template->equipmentType->equipment_name }}</div>
                                                <span
                                                    class="badge bg-info small">{{ $template->equipmentType->equipment_type }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $template->item_name }}</div>
                                    </td>
                                    <td>
                                        @if ($template->standar_condition)
                                            <span class="text-muted">
                                                {{ Str::limit($template->standar_condition, 50) }}
                                            </span>
                                        @else
                                            <span class="text-muted fst-italic">No condition specified</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($template->standar_picture)
                                            <img src="{{ url('storage/' . $template->standar_picture) }}"
                                                alt="Standard Picture" class="img-thumbnail"
                                                style="width: 40px; height: 40px; object-fit: cover;"
                                                onclick="showImageModal('{{ url('storage/' . $template->standar_picture) }}', '{{ $template->item_name }}')"
                                                role="button" title="Click to view">
                                        @else
                                            <span class="text-muted small">No image</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($template->is_active)
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
                                        <small class="text-muted">
                                            {{ $template->created_at->format('d M Y, H:i') }}
                                            @if ($template->creator)
                                                <br><span class="text-muted">by {{ $template->creator->name }}</span>
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @can('view', $template)
                                                <a href="{{ route('checksheet-templates.show', $template) }}"
                                                    class="btn btn-outline-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan

                                            @can('update', $template)
                                                <a href="{{ route('checksheet-templates.edit', $template) }}"
                                                    class="btn btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan

                                            @can('delete', $template)
                                                <button type="button" class="btn btn-outline-danger"
                                                    onclick="confirmDelete({{ $template->id }})" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                            <p class="mb-0">No checksheet templates found</p>
                                            @can('create', App\Models\ChecksheetTemplate::class)
                                                <a href="{{ route('checksheet-templates.create') }}"
                                                    class="btn btn-primary btn-sm mt-2">
                                                    <i class="fas fa-plus me-1"></i>Add First Template
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
                @if ($checksheetTemplates->hasPages())
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 gap-3">
                        <div class="text-muted small">
                            Showing {{ $checksheetTemplates->firstItem() }} to {{ $checksheetTemplates->lastItem() }}
                            of {{ $checksheetTemplates->total() }} results
                        </div>
                        <div class="pagination-wrapper">
                            {{ $checksheetTemplates->links() }}
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
                    <p>Are you sure you want to delete this checksheet template?</p>
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

    <!-- Universal Image Modal -->
    <div class="modal fade" id="universalImageModal" tabindex="-1" aria-labelledby="universalImageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="universalImageModalLabel">
                        Standard Picture
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Standard Picture" class="img-fluid"
                        style="max-height: 70vh; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function showImageModal(imageSrc, itemName) {
                const modal = document.getElementById('universalImageModal');
                const modalImage = document.getElementById('modalImage');
                const modalTitle = document.getElementById('universalImageModalLabel');

                // Add loading state
                modalImage.classList.add('modal-image-loading');
                modalImage.src = '';

                // Set title immediately
                modalTitle.textContent = `Standard Picture - ${itemName}`;

                // Show modal
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();

                // Create new image to preload
                const img = new Image();
                img.onload = function() {
                    // When image is loaded, update modal image and remove loading state
                    modalImage.src = this.src;
                    modalImage.classList.remove('modal-image-loading');
                };
                img.onerror = function() {
                    // Handle error
                    modalImage.classList.remove('modal-image-loading');
                    modalImage.src =
                        'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkltYWdlIG5vdCBmb3VuZDwvdGV4dD48L3N2Zz4=';
                };
                img.src = imageSrc;
            }

            function confirmDelete(id) {
                const form = document.getElementById('deleteForm');
                form.action = `${window.location.origin}/checksheet-templates/${id}`;
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
            /* Custom pagination styling for checksheet templates */
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

            /* Image thumbnail hover effect */
            .img-thumbnail[role="button"]:hover {
                transform: scale(1.1);
                transition: transform 0.2s ease;
                cursor: pointer;
            }

            /* Universal Image Modal styling */
            #universalImageModal .modal-dialog {
                max-width: 90vw;
            }

            #universalImageModal .modal-body {
                padding: 1rem;
            }

            #modalImage {
                transition: opacity 0.3s ease;
                background: #f8f9fa;
                border-radius: 0.375rem;
            }

            /* Image thumbnail hover effect */
            .img-thumbnail[role="button"]:hover {
                transform: scale(1.1);
                transition: transform 0.2s ease;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                z-index: 1;
                position: relative;
            }

            /* Loading state for modal image */
            .modal-image-loading {
                opacity: 0.5;
                pointer-events: none;
            }
        </style>
    @endpush
@endsection
