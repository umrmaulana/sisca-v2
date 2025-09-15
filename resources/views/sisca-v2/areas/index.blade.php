@extends('sisca-v2.layouts.app')

@section('title', 'Areas Management')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Area Management</h3>
                <p class="text-muted mb-0">Manage all areas in the system</p>
            </div>
            @can('create', App\Models\SiscaV2\Area::class)
                <a href="{{ route('sisca-v2.areas.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Area
                </a>
            @endcan
        </div>

        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('sisca-v2.areas.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search Area</label>
                        <input type="text" class="form-control" id="search" name="search"
                            value="{{ request('search') }}" placeholder="Enter area or plant name...">
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
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary me-2">
                            <i class="fas fa-search me-1"></i>Search
                        </button>
                        <a href="{{ route('sisca-v2.areas.index') }}" class="btn btn-outline-danger">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Areas Table -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Areas List</h5>
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
                                <th>Area</th>
                                <th>Plant</th>
                                <th>Mapping</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($areas as $index => $area)
                                <tr>
                                    <td>{{ $areas->firstItem() + $index }}</td>
                                    <td>
                                        <div class="fw-bold text-primary">{{ $area->area_name }}</div>
                                    </td>
                                    <td>
                                        @if ($area->plant)
                                            <span class="badge bg-info">{{ $area->plant->plant_name }}</span>
                                        @else
                                            <span class="text-muted fst-italic">No plant assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($area->mapping_picture)
                                            <img src="{{ asset('storage/' . $area->mapping_picture) }}" alt="Mapping"
                                                class="img-thumbnail"
                                                style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;"
                                                onclick="showImageModal('{{ asset('storage/' . $area->mapping_picture) }}', '{{ $area->area_name }} Mapping')">
                                        @else
                                            <span class="text-muted fst-italic">No mapping</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($area->is_active)
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
                                            {{ $area->created_at->format('d M Y, H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @can('view', $area)
                                                <a href="{{ route('sisca-v2.areas.show', $area) }}"
                                                    class="btn btn-outline-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan

                                            @can('update', $area)
                                                <a href="{{ route('sisca-v2.areas.edit', $area) }}"
                                                    class="btn btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan

                                            @can('delete', $area)
                                                <button type="button" class="btn btn-outline-danger"
                                                    onclick="confirmDelete({{ $area->id }})" title="Delete">
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
                                            <p class="mb-0">No area found</p>
                                            @can('create', App\Models\SiscaV2\Area::class)
                                                <a href="{{ route('sisca-v2.areas.create') }}"
                                                    class="btn btn-primary btn-sm mt-2">
                                                    <i class="fas fa-plus me-1"></i>Add First Area
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
                @if ($areas->hasPages())
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 gap-3">
                        <div class="text-muted small">
                            Showing {{ $areas->firstItem() }} to {{ $areas->lastItem() }}
                            of {{ $areas->total() }} results
                        </div>
                        <div class="pagination-wrapper">
                            {{ $areas->links() }}
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
                    <p>Are you sure you want to delete this area?</p>
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

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Mapping Picture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Mapping Picture" class="img-fluid rounded">
                </div>
                <div class="modal-footer">
                    <a id="openFullSize" href="" target="_blank" class="btn btn-primary">
                        <i class="fas fa-external-link-alt me-2"></i>Open Full Size
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            function confirmDelete(id) {
                const form = document.getElementById('deleteForm');
                form.action = `{{ route('sisca-v2.areas.index') }}/${id}`;
                const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                modal.show();
            }

            function showImageModal(imageSrc, title) {
                document.getElementById('modalImage').src = imageSrc;
                document.getElementById('imageModalLabel').textContent = title;
                document.getElementById('openFullSize').href = imageSrc;
                const modal = new bootstrap.Modal(document.getElementById('imageModal'));
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
