@extends('sisca-v2.layouts.app')

@section('title', 'Area Details')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Area Details</h3>
                <p class="text-muted mb-0">View detailed information about this area</p>
            </div>
            <div class="d-flex gap-2">
                @can('update', $area)
                    <a href="{{ route('sisca-v2.areas.edit', $area) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                @endcan
                <a href="{{ route('sisca-v2.areas.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Area Information -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Area Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label text-muted">Area Name</label>
                                    <div class="h5 text-primary">{{ $area->area_name }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label text-muted">Company</label>
                                    <div class="h6">
                                        @if ($area->plant)
                                            {{ $area->plant->plant_name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label text-muted">Status</label>
                                    <div>
                                        @if ($area->is_active)
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
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label text-muted">Mapping Picture</label>
                                    <div>
                                        @if ($area->mapping_picture)
                                            <img src="{{ url('storage/' . $area->mapping_picture) }}" alt="Area mapping"
                                                class="img-thumbnail"
                                                style="max-width: 120px; max-height: 120px; cursor: pointer;"
                                                data-bs-toggle="modal" data-bs-target="#mappingImageModal">
                                        @else
                                            <span class="text-muted">No mapping picture available</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Area ID</label>
                                    <div class="text-primary fw-bold">#{{ $area->id }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                @can('update', $area)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-cogs me-2"></i>Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-2 flex-wrap">
                                @can('create', App\Models\SiscaV2\Area::class)
                                    <a href="{{ route('sisca-v2.areas.create') }}" class="btn btn-success">
                                        <i class="fas fa-plus me-2"></i>Create New Area
                                    </a>
                                @endcan

                                @can('update', $area)
                                    <a href="{{ route('sisca-v2.areas.edit', $area) }}" class="btn btn-warning">
                                        <i class="fas fa-edit me-2"></i>Edit Area
                                    </a>
                                @endcan

                                @can('delete', $area)
                                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                        <i class="fas fa-trash me-2"></i>Delete Area
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
                                <span class="fw-bold">{{ $area->created_at->format('d M Y') }}</span>
                            </div>
                            <small class="text-muted">{{ $area->created_at->format('H:i:s') }}
                                ({{ $area->created_at->diffForHumans() }})</small>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Last Updated:</span>
                                <span class="fw-bold">{{ $area->updated_at->format('d M Y') }}</span>
                            </div>
                            <small class="text-muted">{{ $area->updated_at->format('H:i:s') }}
                                ({{ $area->updated_at->diffForHumans() }})</small>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Record ID:</span>
                                <span class="fw-bold text-primary">#{{ $area->id }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Related Information Card -->
                @if (isset($area->locations))
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-link me-2"></i>Related Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Total Locations:</span>
                                    <span class="badge bg-info">{{ $area->locations->count() ?? 0 }}</span>
                                </div>
                                <small class="text-muted">Locations in this area</small>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Active Status:</span>
                                    @if ($area->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </div>
                                <small class="text-muted">Current operational status</small>
                            </div>

                            @if (isset($area->locations) && $area->locations->count() > 0)
                                <div class="mt-3">
                                    <a href="{{ route('sisca-v2.locations.index', ['area_id' => $area->id]) }}"
                                        class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-list me-2"></i>View Related Locations
                                    </a>
                                </div>
                            @endif
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
                            <div class="col-12">
                                <div class="h4 text-info mb-1">
                                    {{ isset($area->locations) ? $area->locations->count() : 0 }}</div>
                                <small class="text-muted">Locations</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mapping Image Modal -->
    @if ($area->mapping_picture)
        <div class="modal fade" id="mappingImageModal" tabindex="-1" aria-labelledby="mappingImageModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="mappingImageModalLabel">{{ $area->area_name }} - Mapping Picture</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ url('storage/' . $area->mapping_picture) }}" alt="Area mapping picture"
                            class="img-fluid">
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
                        <h5>Are you sure you want to delete this area?</h5>
                        <p class="text-muted">Area: <strong>{{ $area->area_name }}</strong></p>
                        @if (isset($area->locations) && $area->locations->count() > 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                This area has <strong>{{ $area->locations->count() }}</strong> related location(s).
                                Deleting this will affect those records.
                            </div>
                        @endif
                        <p class="text-danger"><strong>This action cannot be undone.</strong></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('sisca-v2.areas.destroy', $area) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>Delete Area
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
