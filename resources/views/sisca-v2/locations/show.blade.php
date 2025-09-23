@extends('sisca-v2.layouts.app')

@section('title', 'Location Details')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Location Details</h3>
                <p class="text-muted mb-0">View detailed information about this location</p>
            </div>
            <div class="d-flex gap-2">
                @can('update', $location)
                    <a href="{{ route('sisca-v2.locations.edit', $location) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                @endcan
                <a href="{{ route('sisca-v2.locations.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Location Information -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Location Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label text-muted">Location Code</label>
                                    <div class="h5 text-primary">{{ $location->location_code }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label text-muted">Position</label>
                                    <div class="h6">
                                        @if ($location->pos)
                                            {{ $location->pos }}
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
                                    <label class="form-label text-muted">Company</label>
                                    <div class="h6 text-info">
                                        @if ($location->plant)
                                            {{ $location->plant->plant_name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label text-muted">Area</label>
                                    <div class="h6">
                                        @if ($location->area)
                                            {{ $location->area->area_name }}
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
                                    <label class="form-label text-muted">X Coordinate</label>
                                    <div class="h6">
                                        @if ($location->coordinate_x)
                                            <span
                                                class="badge bg-light text-dark">{{ number_format($location->coordinate_x, 6) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label text-muted">Y Coordinate</label>
                                    <div class="h6">
                                        @if ($location->coordinate_y)
                                            <span
                                                class="badge bg-light text-dark">{{ number_format($location->coordinate_y, 6) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
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
                                        @if ($location->is_active)
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
                                <div class="mb-3">
                                    <label class="form-label text-muted">Location ID</label>
                                    <div class="text-primary fw-bold">#{{ $location->id }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Area Mapping -->
                @if ($location->area && $location->area->mapping_picture)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-map me-2"></i>Area Mapping
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="position-relative">
                                <img src="{{ url('storage/' . $location->area->mapping_picture) }}" alt="Area mapping"
                                    class="img-fluid border rounded" style="width: 100%; cursor: pointer;"
                                    data-bs-toggle="modal" data-bs-target="#mappingModal">

                                @if ($location->coordinate_x && $location->coordinate_y)
                                    <div class="position-absolute bg-danger rounded-circle"
                                        style="width: 12px; height: 12px; transform: translate(-50%, -50%); z-index: 10;
                                            left: {{ $location->coordinate_x * 100 }}%; 
                                            top: {{ $location->coordinate_y * 100 }}%;">
                                    </div>
                                    <div class="position-absolute bg-white border border-danger rounded px-2 py-1"
                                        style="transform: translate(-50%, -120%); z-index: 11; font-size: 0.75rem;
                                            left: {{ $location->coordinate_x * 100 }}%; 
                                            top: {{ $location->coordinate_y * 100 }}%;">
                                        <strong class="text-dark">{{ $location->location_code }}</strong>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Click to view larger image
                                    @if ($location->coordinate_x && $location->coordinate_y)
                                        • Red dot shows location coordinates
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                @can('update', $location)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-cogs me-2"></i>Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-2 flex-wrap">
                                @can('create', App\Models\SiscaV2\Location::class)
                                    <a href="{{ route('sisca-v2.locations.create') }}" class="btn btn-success">
                                        <i class="fas fa-plus me-2"></i>Create New Location
                                    </a>
                                @endcan

                                @can('update', $location)
                                    <a href="{{ route('sisca-v2.locations.edit', $location) }}" class="btn btn-warning">
                                        <i class="fas fa-edit me-2"></i>Edit Location
                                    </a>
                                @endcan

                                @can('delete', $location)
                                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                        <i class="fas fa-trash me-2"></i>Delete Location
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
                                <span class="fw-bold">{{ $location->created_at->format('d M Y') }}</span>
                            </div>
                            <small class="text-muted">{{ $location->created_at->format('H:i:s') }}
                                ({{ $location->created_at->diffForHumans() }})</small>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Last Updated:</span>
                                <span class="fw-bold">{{ $location->updated_at->format('d M Y') }}</span>
                            </div>
                            <small class="text-muted">{{ $location->updated_at->format('H:i:s') }}
                                ({{ $location->updated_at->diffForHumans() }})</small>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Record ID:</span>
                                <span class="fw-bold text-primary">#{{ $location->id }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Related Information Card -->
                @if (isset($location->equipments))
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-link me-2"></i>Related Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Total Equipments:</span>
                                    <span class="badge bg-info">{{ $location->equipments->count() ?? 0 }}</span>
                                </div>
                                <small class="text-muted">Equipment in this location</small>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Has Coordinates:</span>
                                    @if ($location->coordinate_x && $location->coordinate_y)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </div>
                                <small class="text-muted">Location mapping availability</small>
                            </div>

                            @if (isset($location->equipments) && $location->equipments->count() > 0)
                                <div class="mt-3">
                                    <a href="{{ route('sisca-v2.equipments.index', ['location_id' => $location->id]) }}"
                                        class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-list me-2"></i>View Related Equipment
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
                                    {{ isset($location->equipments) ? $location->equipments->count() : 0 }}</div>
                                <small class="text-muted">Equipment</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mapping Modal -->
    @if ($location->area && $location->area->mapping_picture)
        <div class="modal fade" id="mappingModal" tabindex="-1" aria-labelledby="mappingModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="mappingModalLabel">{{ $location->area->area_name }} - Mapping Picture
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="position-relative">
                            <img src="{{ url('storage/' . $location->area->mapping_picture) }}"
                                alt="Area mapping picture" class="img-fluid">

                            @if ($location->coordinate_x && $location->coordinate_y)
                                <div class="position-absolute bg-danger rounded-circle"
                                    style="width: 15px; height: 15px; transform: translate(-50%, -50%); z-index: 10;
                                        left: {{ $location->coordinate_x * 100 }}%; 
                                        top: {{ $location->coordinate_y * 100 }}%;">
                                </div>
                                <div class="position-absolute bg-white border border-danger rounded px-2 py-1"
                                    style="transform: translate(-50%, -120%); z-index: 11;
                                        left: {{ $location->coordinate_x * 100 }}%; 
                                        top: {{ $location->coordinate_y * 100 }}%;">
                                    <strong class="text-dark">{{ $location->location_code }}</strong>
                                </div>
                            @endif
                        </div>
                        @if ($location->coordinate_x && $location->coordinate_y)
                            <div class="mt-3">
                                <small class="text-muted">
                                    Coordinates: X={{ number_format($location->coordinate_x, 6) }},
                                    Y={{ number_format($location->coordinate_y, 6) }}
                                </small>
                            </div>
                        @endif
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
                        <h5>Are you sure you want to delete this location?</h5>
                        <p class="text-muted">Location: <strong>{{ $location->location_code }}</strong></p>
                        @if (isset($location->equipments) && $location->equipments->count() > 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                This location has <strong>{{ $location->equipments->count() }}</strong> related equipment.
                                Deleting this will affect those records.
                            </div>
                        @endif
                        <p class="text-danger"><strong>This action cannot be undone.</strong></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('sisca-v2.locations.destroy', $location) }}" method="POST"
                        style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>Delete Location
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
