@extends('sisca-v2.layouts.app')

@section('title', 'Equipment Management')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Equipment Management</h3>
                <p class="text-muted mb-0">Manage all equipment in the system with QR code generation</p>
            </div>
            @can('create', App\Models\SiscaV2\Equipment::class)
                <a href="{{ route('sisca-v2.equipments.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Equipment
                </a>
            @endcan
        </div>

        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('sisca-v2.equipments.index') }}" class="row g-3">
                    <div class="col-md-2">
                        <label for="search" class="form-label">Search Equipment</label>
                        <input type="text" class="form-control" id="search" name="search"
                            value="{{ request('search') }}" placeholder="Enter equipment code or type...">
                    </div>
                    @if (count($plants) > 0)
                        <div class="col-md-2">
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
                    @endif
                    <div class="col-md-2">
                        <label for="area_id" class="form-label">Area</label>
                        <select class="form-select" id="area_id" name="area_id"
                            @if (count($plants) > 0) disabled @endif>
                            <option value="">
                                @if (count($plants) > 0)
                                    Select plant first
                                @else
                                    All Areas
                                @endif
                            </option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                    {{ $area->area_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="equipment_type_id" class="form-label">Equipment Type</label>
                        <select class="form-select" id="equipment_type_id" name="equipment_type_id">
                            <option value="">All Types</option>
                            @foreach ($equipmentTypes as $type)
                                <option value="{{ $type->id }}"
                                    {{ request('equipment_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->equipment_name }}-{{ $type->equipment_type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="period_check_id" class="form-label">Period Check</label>
                        <select class="form-select" id="period_check_id" name="period_check_id">
                            <option value="">All Periods</option>
                            @foreach ($periodChecks as $period)
                                <option value="{{ $period->id }}"
                                    {{ request('period_check_id') == $period->id ? 'selected' : '' }}>
                                    {{ $period->period_check }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary me-2">
                            <i class="fas fa-search me-1"></i>Search
                        </button>
                        <a href="{{ route('sisca-v2.equipments.index') }}" class="btn btn-outline-danger">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Equipment Table -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Equipment List</h5>
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
                                <th>Equipment Code</th>
                                <th>Equipment Type</th>
                                <th>Location</th>
                                <th>Period Check</th>
                                <th>Last Inspector</th>
                                <th>QR Code</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($equipments as $index => $equipment)
                                <tr>
                                    <td>{{ $equipments->firstItem() + $index }}</td>
                                    <td>
                                        <div class="fw-bold text-primary">{{ $equipment->equipment_code }}</div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-info">{{ $equipment->equipmentType->equipment_name ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $equipment->location->location_code ?? '-' }}</div>
                                        @if ($equipment->location && $equipment->location->area)
                                            <small
                                                class="text-muted d-block">{{ $equipment->location->area->area_name }}</small>
                                        @endif
                                        @if ($equipment->location && $equipment->location->plant)
                                            <small
                                                class="text-muted d-block">{{ $equipment->location->plant->plant_name }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($equipment->periodCheck)
                                            <span class="badge bg-secondary">
                                                {{ $equipment->periodCheck->period_check }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($equipment->lastInspector)
                                            <div class="fw-bold">{{ $equipment->lastInspector->name }}</div>
                                            <small class="text-muted">NPK: {{ $equipment->lastInspector->npk }}</small>
                                            @if ($equipment->latestInspection)
                                                <br><small
                                                    class="text-muted">{{ $equipment->latestInspection->created_at->format('d M Y') }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">Not inspected</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($equipment->qrcode && \Storage::disk('public')->exists($equipment->qrcode))
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('storage/' . $equipment->qrcode) }}" alt="QR Code"
                                                    class="me-2 border rounded"
                                                    style="width: 50px; height: 50px; object-fit: contain;">
                                                <a href="{{ asset('storage/' . $equipment->qrcode) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary" title="View QR Code">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            </div>
                                        @else
                                            <span class="badge bg-warning">No QR</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($equipment->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $equipment->created_at->format('d M Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('sisca-v2.equipments.show', $equipment) }}"
                                                class="btn btn-outline-info btn-sm" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @can('update', $equipment)
                                                <a href="{{ route('sisca-v2.equipments.edit', $equipment) }}"
                                                    class="btn btn-outline-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('delete', $equipment)
                                                <button type="button" class="btn btn-outline-danger btn-sm"
                                                    onclick="confirmDelete({{ $equipment->id }}, '{{ $equipment->equipment_code }}')"
                                                    title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-cogs fa-3x mb-3"></i>
                                            <p class="mb-0">No equipment found</p>
                                            @can('create', App\Models\SiscaV2\Equipment::class)
                                                <a href="{{ route('sisca-v2.equipments.create') }}"
                                                    class="btn btn-primary btn-sm mt-2">
                                                    <i class="fas fa-plus me-1"></i>Add First Equipment
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
                @if ($equipments->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $equipments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete equipment <strong id="equipmentCode"></strong>?</p>
                    <p class="text-muted">This action will also delete the QR code and cannot be undone.</p>
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
@endsection

@push('scripts')
    <script>
        function confirmDelete(equipmentId, equipmentCode) {
            document.getElementById('equipmentCode').textContent = equipmentCode;
            document.getElementById('deleteForm').action = '/sisca-v2/equipments/' + equipmentId;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Handle plant change for area filtering
        document.addEventListener('DOMContentLoaded', function() {
            const plantSelect = document.getElementById('plant_id');
            const areaSelect = document.getElementById('area_id');

            if (plantSelect && areaSelect) {
                plantSelect.addEventListener('change', function() {
                    const plantId = this.value;

                    // Clear current options
                    areaSelect.innerHTML = '<option value="">Loading...</option>';
                    areaSelect.disabled = true;

                    if (plantId) {
                        // Make AJAX request to get areas
                        fetch(
                                `${window.location.origin}/sisca-v2/equipments/areas-by-plant?plant_id=${plantId}`)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                console.log('Areas response:', data); // Debug log
                                areaSelect.innerHTML = '<option value="">All Areas</option>';

                                // Handle error response
                                if (data.error) {
                                    console.error('Server error:', data.error);
                                    areaSelect.innerHTML = '<option value="">Error: ' + data.error +
                                        '</option>';
                                    areaSelect.disabled = false;
                                    return;
                                }

                                // Handle different response formats
                                let areas = [];
                                if (Array.isArray(data)) {
                                    areas = data;
                                } else if (data && Array.isArray(data.areas)) {
                                    areas = data.areas;
                                }

                                if (areas.length === 0) {
                                    const option = document.createElement('option');
                                    option.value = '';
                                    option.textContent = 'No areas available for this plant';
                                    option.disabled = true;
                                    areaSelect.appendChild(option);
                                } else {
                                    areas.forEach(area => {
                                        if (area && area.id && area.area_name) {
                                            const option = document.createElement('option');
                                            option.value = area.id;
                                            option.textContent = area.area_name;
                                            if (area.id == '{{ request('area_id') }}') {
                                                option.selected = true;
                                            }
                                            areaSelect.appendChild(option);
                                        }
                                    });
                                }
                                areaSelect.disabled = false;
                            })
                            .catch(error => {
                                console.error('Error loading areas:', error);
                                areaSelect.innerHTML =
                                    '<option value="">Error loading areas</option>';
                                areaSelect.disabled = false;
                            });
                    } else {
                        areaSelect.innerHTML = '<option value="">Select plant first</option>';
                        areaSelect.disabled = false;
                    }
                });

                // Auto filter on page load if plant is selected
                if (plantSelect.value) {
                    plantSelect.dispatchEvent(new Event('change'));
                }
            }
        });
    </script>
@endpush
