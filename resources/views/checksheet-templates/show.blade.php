@extends('layouts.app')

@section('title', 'Checksheet Template Details')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Checksheet Template Details</h3>
                <p class="text-muted mb-0">View detailed information about this checksheet template</p>
            </div>
            <div class="d-flex gap-2">
                @can('update', $checksheetTemplate)
                    <a href="{{ route('checksheet-templates.edit', $checksheetTemplate) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                @endcan
                <a href="{{ route('checksheet-templates.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Checksheet Template Information -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Template Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label text-muted">Item Name</label>
                                    <div class="h5 text-primary">{{ $checksheetTemplate->item_name }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label text-muted">Equipment Type</label>
                                    <div>
                                        @if ($checksheetTemplate->equipmentType)
                                            <span
                                                class="badge bg-info fs-6">{{ $checksheetTemplate->equipmentType->equipment_name }}</span>
                                        @else
                                            <span class="text-muted fst-italic">No equipment type assigned</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label text-muted">Order Number</label>
                                    <div class="h6 text-info">#{{ $checksheetTemplate->order_number }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label text-muted">Status</label>
                                    <div>
                                        @if ($checksheetTemplate->is_active)
                                            <span class="badge bg-success fs-6">
                                                <i class="fas fa-check me-1"></i>Active
                                            </span>
                                        @else
                                            <span class="badge bg-secondary fs-6">
                                                <i class="fas fa-times me-1"></i>Inactive
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($checksheetTemplate->standar_condition)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-4">
                                        <label class="form-label text-muted">Standard Condition</label>
                                        <div class="border rounded p-3">
                                            <p class="mb-0">{{ $checksheetTemplate->standar_condition }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($checksheetTemplate->standar_picture)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-4">
                                        <label class="form-label text-muted">Standard Picture</label>
                                        <div class="border rounded p-3 text-center">
                                            <img src="{{ url('storage/' . $checksheetTemplate->standar_picture) }}"
                                                alt="Standard Picture" class="img-fluid rounded border"
                                                style="max-width: 300px; cursor: pointer;" data-bs-toggle="modal"
                                                data-bs-target="#imageModal">
                                            <div class="mt-2">
                                                <small class="text-muted">Click to view full size</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Template ID</label>
                                    <div class="text-primary fw-bold">#{{ $checksheetTemplate->id }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                @can('update', $checksheetTemplate)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-cogs me-2"></i>Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-2 flex-wrap">
                                @can('create', App\Models\ChecksheetTemplate::class)
                                    <a href="{{ route('checksheet-templates.create') }}" class="btn btn-success">
                                        <i class="fas fa-plus me-2"></i>Create New Template
                                    </a>
                                @endcan

                                @can('update', $checksheetTemplate)
                                    <a href="{{ route('checksheet-templates.edit', $checksheetTemplate) }}" class="btn btn-warning">
                                        <i class="fas fa-edit me-2"></i>Edit Template
                                    </a>
                                @endcan

                                @can('delete', $checksheetTemplate)
                                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                        <i class="fas fa-trash me-2"></i>Delete Template
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                @endcan
            </div>

            <!-- Sidebar Information -->
            <div class="col-lg-4">
                <!-- Metadata Card -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>Metadata
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Created Date:</span>
                                <span class="fw-bold">{{ $checksheetTemplate->created_at->format('d M Y') }}</span>
                            </div>
                            <small class="text-muted">{{ $checksheetTemplate->created_at->format('H:i:s') }}
                                ({{ $checksheetTemplate->created_at->diffForHumans() }})</small>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Last Updated:</span>
                                <span class="fw-bold">{{ $checksheetTemplate->updated_at->format('d M Y') }}</span>
                            </div>
                            <small class="text-muted">{{ $checksheetTemplate->updated_at->format('H:i:s') }}
                                ({{ $checksheetTemplate->updated_at->diffForHumans() }})</small>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Record ID:</span>
                                <span class="fw-bold text-primary">#{{ $checksheetTemplate->id }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Equipment Type Information Card -->
                @if ($checksheetTemplate->equipmentType)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-cogs me-2"></i>Equipment Type
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Name:</span>
                                    <span class="fw-bold">{{ $checksheetTemplate->equipmentType->equipment_name }}</span>
                                </div>
                                <small class="text-muted">Equipment name</small>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Type:</span>
                                    <span
                                        class="badge bg-info">{{ $checksheetTemplate->equipmentType->equipment_type }}</span>
                                </div>
                                <small class="text-muted">Equipment category</small>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('equipment-types.show', $checksheetTemplate->equipmentType) }}"
                                    class="btn btn-outline-primary btn-sm w-100">
                                    <i class="fas fa-eye me-2"></i>View Equipment Type
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Quick Stats Card -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Quick Stats
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <div class="h4 text-primary mb-1">{{ $checksheetTemplate->order_number }}</div>
                                    <small class="text-muted">Order</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="h4 text-info mb-1">{{ $checksheetTemplate->is_active ? 1 : 0 }}</div>
                                <small class="text-muted">Status</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    @if ($checksheetTemplate->standar_picture)
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel">Standard Picture</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ url('storage/' . $checksheetTemplate->standar_picture) }}" alt="Standard Picture"
                            class="img-fluid rounded">
                    </div>
                    <div class="modal-footer">
                        <a href="{{ url('storage/' . $checksheetTemplate->standar_picture) }}" target="_blank"
                            class="btn btn-primary">
                            <i class="fas fa-external-link-alt me-2"></i>Open Full Size
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                        <h5>Are you sure you want to delete this template?</h5>
                        <p class="text-muted">Template: <strong>{{ $checksheetTemplate->item_name }}</strong></p>
                        @if ($checksheetTemplate->standar_picture)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                The associated standard picture will also be deleted.
                            </div>
                        @endif
                        <p class="text-danger"><strong>This action cannot be undone.</strong></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('checksheet-templates.destroy', $checksheetTemplate) }}" method="POST"
                        style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>Delete Template
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .fs-6 {
                font-size: 1rem !important;
            }

            .bg-light {
                background-color: #f8f9fa !important;
            }

            .border-end {
                border-right: 1px solid #dee2e6 !important;
            }

            @media print {

                .btn,
                .modal,
                .card-header {
                    display: none !important;
                }

                .card {
                    border: 1px solid #ddd !important;
                    box-shadow: none !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function confirmDelete() {
                const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                modal.show();
            }

            // Auto dismiss success messages
            document.addEventListener('DOMContentLoaded', function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    setTimeout(function() {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }, 5000);
                });
            });
        </script>
    @endpush
@endsection
