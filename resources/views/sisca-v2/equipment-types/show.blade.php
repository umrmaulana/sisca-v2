@extends('sisca-v2.layouts.app')

@section('title', 'Equipment Type Details')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Equipment Type Details</h3>
                <p class="text-muted mb-0">View detailed information about this equipment type</p>
            </div>
            <div class="d-flex gap-2">
                @can('update', $equipmentType)
                    <a href="{{ route('sisca-v2.equipment-types.edit', $equipmentType) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                @endcan
                <a href="{{ route('sisca-v2.equipment-types.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Equipment Type Information -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Equipment Type Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label text-muted">Equipment Name</label>
                                    <div class="h5 text-primary">{{ $equipmentType->equipment_name }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label text-muted">Equipment Type</label>
                                    <div>
                                        <span class="badge bg-info fs-6">{{ $equipmentType->equipment_type }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <label class="form-label text-muted">Description</label>
                                    <div class="border rounded p-3">
                                        @if ($equipmentType->desc)
                                            <p class="mb-0">{{ $equipmentType->desc }}</p>
                                        @else
                                            <p class="text-muted mb-0 fst-italic">No description provided</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Status</label>
                                    <div>
                                        @if ($equipmentType->is_active)
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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Equipment Type ID</label>
                                    <div class="text-primary fw-bold">#{{ $equipmentType->id }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                @can('update', $equipmentType)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-cogs me-2"></i>Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-2 flex-wrap">
                                @can('create', App\Models\SiscaV2\EquipmentType::class)
                                    <a href="{{ route('sisca-v2.equipment-types.create') }}" class="btn btn-success">
                                        <i class="fas fa-plus me-2"></i>Create New Equipment Type
                                    </a>
                                @endcan

                                @can('update', $equipmentType)
                                    <a href="{{ route('sisca-v2.equipment-types.edit', $equipmentType) }}" class="btn btn-warning">
                                        <i class="fas fa-edit me-2"></i>Edit Equipment Type
                                    </a>
                                @endcan

                                @can('delete', $equipmentType)
                                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                        <i class="fas fa-trash me-2"></i>Delete Equipment Type
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
                                <span class="fw-bold">{{ $equipmentType->created_at->format('d M Y') }}</span>
                            </div>
                            <small class="text-muted">{{ $equipmentType->created_at->format('H:i:s') }}
                                ({{ $equipmentType->created_at->diffForHumans() }})</small>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Last Updated:</span>
                                <span class="fw-bold">{{ $equipmentType->updated_at->format('d M Y') }}</span>
                            </div>
                            <small class="text-muted">{{ $equipmentType->updated_at->format('H:i:s') }}
                                ({{ $equipmentType->updated_at->diffForHumans() }})</small>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Record ID:</span>
                                <span class="fw-bold text-primary">#{{ $equipmentType->id }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Related Equipment Card -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-link me-2"></i>Related Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Total Equipment:</span>
                                <span class="badge bg-primary">{{ $equipmentType->equipments->count() ?? 0 }}</span>
                            </div>
                            <small class="text-muted">Equipment using this type</small>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Active Status:</span>
                                @if ($equipmentType->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </div>
                            <small class="text-muted">Current operational status</small>
                        </div>

                        @if ($equipmentType->equipments->count() > 0)
                            <div class="mt-3">
                                <a href="{{ route('sisca-v2.equipments.index', ['equipment_type_id' => $equipmentType->id]) }}"
                                    class="btn btn-outline-primary btn-sm w-100">
                                    <i class="fas fa-list me-2"></i>View Related Equipment
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

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
                                    <div class="h4 text-primary mb-1">{{ $equipmentType->equipments->count() ?? 0 }}</div>
                                    <small class="text-muted">Equipment</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="h4 text-info mb-1">
                                    {{ $equipmentType->checksheetTemplates->count() ?? 0 }}
                                </div>
                                <small class="text-muted">Templates</small>
                            </div>
                        </div>
                    </div>
                </div>
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
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                        <h5>Are you sure you want to delete this equipment type?</h5>
                        <p class="text-muted">Equipment Type: <strong>{{ $equipmentType->equipment_name }}</strong></p>
                        @if ($equipmentType->equipments->count() > 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                This equipment type has <strong>{{ $equipmentType->equipments->count() }}</strong> related
                                equipment(s).
                                Deleting this will affect those records.
                            </div>
                        @endif
                        <p class="text-danger"><strong>This action cannot be undone.</strong></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('sisca-v2.equipment-types.destroy', $equipmentType) }}" method="POST"
                        style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>Delete Equipment Type
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
