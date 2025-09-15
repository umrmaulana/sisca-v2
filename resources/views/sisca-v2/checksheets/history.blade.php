@extends('sisca-v2.layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Inspection History</h3>
                <p class="text-muted mb-0">View all completed equipment inspections</p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('sisca-v2.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sisca-v2.checksheets.index') }}">Checksheet</a>
                    </li>
                    <li class="breadcrumb-item active">History</li>
                </ol>
            </nav>
        </div>

        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('sisca-v2.checksheets.history') }}" class="row g-3">
                    <div class="col-md-2">
                        <label for="equipment_id" class="form-label">Equipment</label>
                        <select class="form-select" id="equipment_id" name="equipment_id">
                            <option value="">All Equipment</option>
                            @foreach ($equipments as $equipment)
                                <option value="{{ $equipment->id }}"
                                    {{ request('equipment_id') == $equipment->id ? 'selected' : '' }}>
                                    {{ $equipment->equipment_code }} - {{ $equipment->equipmentType->equipment_name ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="area_id" class="form-label">Area</label>
                        <select class="form-select" id="area_id" name="area_id">
                            <option value="">All Areas</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                    {{ $area->area_name }} - {{ $area->plant->plant_name ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="from_date" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="from_date" name="from_date"
                            value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="to_date" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="to_date" name="to_date"
                            value="{{ request('to_date') }}">
                    </div>
                    @if (count($plants) > 0)
                        <div class="col-md-2">
                            <label for="inspector_id" class="form-label">Inspector</label>
                            <select class="form-select" id="inspector_id" name="inspector_id">
                                <option value="">All Inspectors</option>
                                @foreach ($inspectors as $inspector)
                                    <option value="{{ $inspector->id }}"
                                        {{ request('inspector_id') == $inspector->id ? 'selected' : '' }}>
                                        {{ $inspector->name }} ({{ $inspector->npk }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
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
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary me-2">
                            <i class="fas fa-search me-1"></i>Search
                        </button>
                        <a href="{{ route('sisca-v2.checksheets.history') }}" class="btn btn-outline-danger">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Inspection List -->
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h6 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>Inspection Records
                </h6>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Equipment Code</th>
                                <th>Equipment Type</th>
                                <th>Location</th>
                                <th>Inspector</th>
                                <th>Date</th>
                                <th>Status Summary</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inspections as $index => $inspection)
                                @php
                                    $okCount = $inspection->details->where('status', 'OK')->count();
                                    $ngCount = $inspection->details->where('status', 'NG')->count();
                                    $naCount = $inspection->details->where('status', 'NA')->count();
                                    $totalCount = $inspection->details->count();
                                    $overallStatus = $ngCount > 0 ? 'NG' : ($okCount > 0 ? 'OK' : 'Incomplete');
                                @endphp
                                <tr>
                                    <td>{{ $inspections->firstItem() + $index }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $inspection->equipment->equipment_code }}</div>
                                        @if ($inspection->equipment->desc)
                                            <small
                                                class="text-muted">{{ Str::limit($inspection->equipment->desc, 30) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $inspection->equipment->equipmentType->equipment_name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $inspection->equipment->location->location_code ?? 'N/A' }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $inspection->equipment->location->area->area_name ?? '' }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $inspection->user->name ?? 'Unknown' }}</div>
                                        <small class="text-muted">{{ $inspection->user->npk ?? '' }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $inspection->inspection_date->format('d M Y') }}</div>
                                        <small class="text-muted">{{ $inspection->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            @if ($overallStatus === 'NG')
                                                <span class="badge bg-danger mb-1">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Has Issues
                                                </span>
                                            @elseif($overallStatus === 'OK')
                                                <span class="badge bg-success mb-1">
                                                    <i class="fas fa-check-circle me-1"></i>All Good
                                                </span>
                                            @endif
                                            <div class="d-flex gap-1">
                                                @if ($okCount > 0)
                                                    <span class="badge bg-success">{{ $okCount }} OK</span>
                                                @endif
                                                @if ($ngCount > 0)
                                                    <span class="badge bg-danger">{{ $ngCount }} NG</span>
                                                @endif
                                                @if ($naCount > 0)
                                                    <span class="badge bg-secondary">{{ $naCount }} N/A</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('sisca-v2.checksheets.show', $inspection->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-info"
                                                onclick="showInspectionModal({{ $inspection->id }})" title="Quick View">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                            @if ($ngCount > 0)
                                                <a href="{{ route('sisca-v2.checksheets.create') }}?code={{ $inspection->equipment->equipment_code }}"
                                                    class="btn btn-sm btn-outline-warning" title="Re-inspect">
                                                    <i class="fas fa-redo"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No inspections found</h5>
                                            <p class="text-muted">Try adjusting your search criteria or create a new
                                                inspection.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($inspections->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $inspections->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick View Modal -->
    <div class="modal fade" id="inspectionModal" tabindex="-1" aria-labelledby="inspectionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inspectionModalLabel">
                        <i class="fas fa-clipboard-check me-2"></i>Inspection Quick View
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="#" id="viewFullDetails" class="btn btn-primary">View Full Details</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function showInspectionModal(inspectionId) {
            const modal = new bootstrap.Modal(document.getElementById('inspectionModal'));
            const modalContent = document.getElementById('modalContent');
            const viewFullLink = document.getElementById('viewFullDetails');

            // Set full details link
            viewFullLink.href = `/sisca-v2/checksheet/show/${inspectionId}`;

            // Show loading state
            modalContent.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

            modal.show();

            // Fetch inspection details
            fetch(`/sisca-v2/api/inspections/${inspectionId}`)
                .then(response => response.json())
                .then(data => {
                    let statusSummary = '';
                    data.details.forEach(detail => {
                        let statusClass = detail.status === 'OK' ? 'success' : (detail.status === 'NG' ?
                            'danger' : 'secondary');
                        statusSummary += `
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <span>${detail.checksheet_template.item_name}</span>
                        <span class="badge bg-${statusClass}">${detail.status}</span>
                    </div>
                `;
                    });

                    modalContent.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Equipment Details</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Code:</strong></td><td>${data.equipment.equipment_code}</td></tr>
                            <tr><td><strong>Type:</strong></td><td>${data.equipment.equipment_type.equipment_name}</td></tr>
                            <tr><td><strong>Location:</strong></td><td>${data.equipment.location.location_code}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Inspection Info</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Date:</strong></td><td>${new Date(data.inspection_date).toLocaleDateString()}</td></tr>
                            <tr><td><strong>Inspector:</strong></td><td>${data.user.name}</td></tr>
                            <tr><td><strong>Items:</strong></td><td>${data.details.length} items</td></tr>
                        </table>
                    </div>
                </div>
                <div class="mt-3">
                    <h6 class="text-primary">Item Status</h6>
                    <div style="max-height: 200px; overflow-y: auto;">
                        ${statusSummary}
                    </div>
                </div>
            `;
                })
                .catch(error => {
                    modalContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error loading inspection details. Please try again.
                </div>
            `;
                });
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
                    fetch(`{{ route('sisca-v2.checksheets.areas-by-plant') }}?plant_id=${plantId}`)
                        .then(response => response.json())
                        .then(data => {
                            areaSelect.innerHTML = '<option value="">All Areas</option>';
                            data.areas.forEach(area => {
                                const option = document.createElement('option');
                                option.value = area.id;
                                option.textContent = area.area_name;
                                if (area.id == '{{ request('area_id') }}') {
                                    option.selected = true;
                                }
                                areaSelect.appendChild(option);
                            });
                            areaSelect.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error loading areas:', error);
                            areaSelect.innerHTML = '<option value="">Error loading areas</option>';
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

        // Approval modal function
        window.showApprovalModal = function(inspectionId) {
            const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
            const form = document.getElementById('approvalForm');
            form.action = `/sisca-v2/checksheet/approve/${inspectionId}`;
            modal.show();
        }
    </script>
@endpush
