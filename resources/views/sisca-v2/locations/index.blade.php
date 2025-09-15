@extends('sisca-v2.layouts.app')

@section('title', 'Locations Management')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Locations Management</h3>
                <p class="text-muted mb-0">Manage location data and coordinates</p>
            </div>
            @can('create', App\Models\SiscaV2\Location::class)
                <a href="{{ route('sisca-v2.locations.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Location
                </a>
            @endcan
        </div>

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i>Filters
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('sisca-v2.locations.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search Location Code</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}" placeholder="Search by location code...">
                        </div>
                        <div class="col-md-3">
                            <label for="plant_id" class="form-label">Plant</label>
                            <select class="form-select" id="plant_id" name="plant_id">
                                <option value="">All Plants</option>
                                @foreach ($plants as $plant)
                                    <option value="{{ $plant->id }}"
                                        {{ request('plant_id') == $plant->id ? 'selected' : '' }}>
                                        {{ $plant->plant_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="area_id" class="form-label">Area</label>
                            <select class="form-select" id="area_id" name="area_id">
                                <option value="">All Areas</option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}"
                                        {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                        {{ $area->area_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="fas fa-search me-1"></i>Search
                            </button>
                            <a href="{{ route('sisca-v2.plants.index') }}" class="btn btn-outline-danger">
                                <i class="fas fa-times me-1"></i>Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Section -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Locations List</h5>
                    <span class="badge bg-info">{{ $locations->total() }} Total</span>
                </div>
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
                @if ($locations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle animate-in">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Location Code</th>
                                    <th>Plant</th>
                                    <th>Area</th>
                                    <th>Position</th>
                                    <th>Coordinates</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($locations as $location)
                                    <tr>
                                        <td>{{ ($locations->currentPage() - 1) * $locations->perPage() + $loop->iteration }}
                                        </td>
                                        <td>
                                            <div class="fw-bold text-primary">{{ $location->location_code }}</div>
                                        </td>
                                        <td>
                                            @if ($location->plant)
                                                <span class="badge bg-info">{{ $location->plant->plant_name }}</span>
                                            @else
                                                <span class="badge">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($location->area)
                                                <div class="fw-bold">{{ $location->area->area_name }}</div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($location->pos)
                                                <span class="badge bg-light text-dark">{{ $location->pos }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($location->coordinate_x && $location->coordinate_y)
                                                <small class="text-muted">
                                                    X: {{ number_format($location->coordinate_x, 2) }}<br>
                                                    Y: {{ number_format($location->coordinate_y, 2) }}
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($location->is_active)
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
                                                {{ $location->created_at->format('d M Y') }}<br>
                                                {{ $location->created_at->format('H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                @can('view', $location)
                                                    <a href="{{ route('sisca-v2.locations.show', $location) }}"
                                                        class="btn btn-outline-info" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan

                                                @can('update', $location)
                                                    <a href="{{ route('sisca-v2.locations.edit', $location) }}"
                                                        class="btn btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('delete', $location)
                                                    <button type="button" class="btn btn-outline-danger"
                                                        onclick="confirmDelete({{ $location->id }})" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Showing {{ $locations->firstItem() ?? 0 }} to {{ $locations->lastItem() ?? 0 }} of
                            {{ $locations->total() }} results
                        </div>
                        {{ $locations->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No locations found</h5>
                        <p class="text-muted">Start by creating your first location.</p>
                        @can('create', App\Models\SiscaV2\Location::class)
                            <a href="{{ route('sisca-v2.locations.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add Location
                            </a>
                        @endcan
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
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                        <h5>Are you sure you want to delete this location?</h5>
                        <p class="text-muted">Location: <strong id="locationName"></strong></p>
                        <p class="text-danger"><strong>This action cannot be undone.</strong></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
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
            function confirmDelete(id) {
                const form = document.getElementById('deleteForm');
                form.action = `{{ route('sisca-v2.locations.index') }}/${id}`;
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
@endsection
