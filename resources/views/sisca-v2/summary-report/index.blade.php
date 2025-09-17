@extends('sisca-v2.layouts.app')

@section('title', 'Summary Report')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-bar text-primary me-2"></i>
                Annual Summary Report
            </h1>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-success btn-sm" onclick="exportPDF()">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
                <button type="button" class="btn btn-info btn-sm" onclick="refreshData()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="card-title m-0 font-weight-bold text-primary">
                    <i class="fas fa-filter me-2"></i>Filter Options
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('sisca-v2.summary-report.index') }}" id="filterForm">
                    <div class="row g-3 mb-3">
                        @if (in_array($userRole, ['Admin', 'Management']))
                            <!-- Plant Filter -->
                            <div class="col-lg-3">
                                <label for="plant_id" class="form-label fw-bold">Company</label>
                                <select class="form-select" id="plant_id" name="plant_id" onchange="loadAreas()">
                                    <option value="">All Companies</option>
                                    @foreach ($plants as $plant)
                                        <option value="{{ $plant->id }}"
                                            {{ $selectedPlantId == $plant->id ? 'selected' : '' }}>
                                            {{ $plant->plant_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Equipment Type Filter -->
                        <div class="col-lg-3">
                            <label for="equipment_type_id" class="form-label fw-bold">Equipment Type</label>
                            <select class="form-select" id="equipment_type_id" name="equipment_type_id"
                                onchange="loadAreasWithEquipmentType()">
                                <option value="">All Equipment Types</option>
                                @foreach ($equipmentTypes as $type)
                                    <option value="{{ $type->id }}"
                                        {{ $selectedEquipmentTypeId == $type->id ? 'selected' : '' }}>
                                        {{ $type->equipment_name }} - {{ $type->equipment_type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Area Filter -->
                        <div class="col-lg-2">
                            <label for="area_id" class="form-label fw-bold">Area</label>
                            <select class="form-select" id="area_id" name="area_id" onchange="submitFilter()">
                                <option value="">All Areas</option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}"
                                        {{ $selectedAreaId == $area->id ? 'selected' : '' }}>
                                        {{ $area->area_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Year Filter -->
                        <div class="col-lg-2">
                            <label for="year" class="form-label fw-bold">Year</label>
                            <select class="form-select" id="year" name="year" onchange="submitFilter()">
                                @for ($i = date('Y') - 5; $i <= date('Y') + 1; $i++)
                                    <option value="{{ $i }}" {{ $selectedYear == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div class="col-lg-2">
                            <label for="status" class="form-label fw-bold">Status</label>
                            <select class="form-select" id="status" name="status" onchange="submitFilter()">
                                <option value="all" {{ $selectedStatus == 'all' ? 'selected' : '' }}>All Status</option>
                                <option value="pending" {{ $selectedStatus == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="approved" {{ $selectedStatus == 'approved' ? 'selected' : '' }}>Approved
                                </option>
                                <option value="rejected" {{ $selectedStatus == 'rejected' ? 'selected' : '' }}>Rejected
                                </option>
                            </select>
                        </div>

                        <!-- Equipment Search -->
                        <div class="col-lg-4">
                            <label for="search_equipment" class="form-label fw-bold">Search Equipment</label>
                            <input type="text" class="form-control" id="search_equipment" name="search_equipment"
                                placeholder="Search by equipment code..." value="{{ $searchEquipment ?? '' }}">
                        </div>

                        <!-- Search Button -->
                        <div class="col-lg-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="{{ route('sisca-v2.summary-report.index') }}" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Equipment Summary Section -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-dark text-white">
                <h6 class="card-title m-0 font-weight-bold">
                    <i class="fas fa-table me-2"></i>Annual Inspection Report - {{ $selectedYear }}
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="equipmentSummaryTable">
                        <thead>
                            <tr>
                                <th rowspan="2" class="text-center align-middle">
                                    Code
                                </th>
                                <th rowspan="2" class="text-center align-middle">
                                    Type
                                </th>
                                <th rowspan="2" class="text-center align-middle">
                                    Area
                                </th>
                                <th rowspan="2" class="text-center align-middle">
                                    Expired
                                </th>
                                <th colspan="12" class="text-center text-white">
                                    Monthly Status
                                </th>
                            </tr>
                            <tr>
                                @for ($month = 1; $month <= 12; $month++)
                                    <th class="text-center text-white" style="min-width: 50px;">
                                        {{ DateTime::createFromFormat('!m', $month)->format('M') }}
                                    </th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($equipmentSummary as $index => $equipment)
                                <tr class="table-row-hover">
                                    <td class="fw-bold">
                                        <div class="fw-bold text-primary">
                                            {{ $equipment['equipment_code'] }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $equipment['equipment_type'] ?? '-' }}</span>
                                    </td>
                                    <td>
                                        {{ $equipment['area'] ?? '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if ($equipment['expired_date'])
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                {{ \Carbon\Carbon::parse($equipment['expired_date'])->format('Y-m-d') }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    @for ($month = 1; $month <= 12; $month++)
                                        <td class="text-center p-2">
                                            @if (isset($equipment['monthly_data'][$month]))
                                                @php
                                                    $monthData = $equipment['monthly_data'][$month];
                                                    $status = $monthData['status'];
                                                    $ngItems = $monthData['ng_items'] ?? [];
                                                    $ngCount = count($ngItems);
                                                @endphp

                                                @if ($status === 'approved')
                                                    @if ($ngCount > 0)
                                                        <span class="badge bg-danger position-relative cursor-pointer"
                                                            data-bs-toggle="tooltip" data-bs-html="true"
                                                            title="<strong>NG Items ({{ $ngCount }}):</strong><br>{{ implode('<br>', $ngItems) }}">
                                                            <i class="fas fa-exclamation-circle me-1"></i>NG
                                                            <span
                                                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-white text-danger">
                                                                {{ $ngCount }}
                                                            </span>
                                                        </span>
                                                    @else
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check-circle me-1"></i>OK
                                                        </span>
                                                    @endif
                                                @elseif ($status === 'pending')
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-clock me-1"></i>PENDING
                                                    </span>
                                                @elseif ($status === 'rejected')
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-times-circle me-1"></i>REJECTED
                                                    </span>
                                                @else
                                                    <span class="badge bg-light text-muted">-</span>
                                                @endif
                                            @else
                                                <span class="badge bg-light text-muted">-</span>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="16" class="text-center py-5">
                                        <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-3 mb-0 h6">No equipment data found for {{ $selectedYear }}
                                        </p>
                                        <small class="text-muted">Try adjusting your filter criteria</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Enhanced Legend -->
                <div class="mt-4 p-3 rounded">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-info-circle text-primary me-2"></i>Status Legend
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success me-2">
                                    <i class="fas fa-check-circle me-1"></i>OK
                                </span>
                                <small>Approved & All Items OK</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-danger me-2">
                                    <i class="fas fa-exclamation-circle me-1"></i>NG
                                </span>
                                <small>Approved but has NG Items</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-warning text-dark me-2">
                                    <i class="fas fa-clock me-1"></i>PENDING
                                </span>
                                <small>Pending Approval</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary me-2">
                                    <i class="fas fa-times-circle me-1"></i>REJECTED
                                </span>
                                <small>Rejected</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-light text-muted me-2">-</span>
                                <small>No Inspection</small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-lightbulb text-warning me-1"></i>
                            <strong>Tip:</strong> Hover over NG badges to see detailed NG item information
                        </small>
                    </div>
                </div>

                <!-- Equipment Summary Pagination -->
                @if ($equipmentSummary->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $equipmentSummary->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Inspections List Section -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title m-0 font-weight-bold text-primary">
                    <i class="fas fa-list-alt me-2"></i>Inspection Details
                </h6>

                <!-- Bulk Actions for Supervisors -->
                @if (in_array($userRole, ['Supervisor', 'Management', 'Admin']))
                    <div class="bulk-actions" style="display: none;">
                        <button type="button" class="btn btn-success btn-sm me-2" onclick="showBulkApprovalModal()">
                            <i class="fas fa-check"></i> Bulk Approve
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="showBulkRejectionModal()">
                            <i class="fas fa-times"></i> Bulk Reject
                        </button>
                    </div>
                @endif
            </div>
            <div class="card-body">
                <!-- Bulk Selection Controls -->
                @if (in_array($userRole, ['Supervisor', 'Management', 'Admin']))
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                            <label class="form-check-label fw-bold" for="selectAll">
                                <i class="fas fa-check-square me-1"></i>Select All Pending Inspections
                            </label>
                        </div>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                @if (in_array($userRole, ['Supervisor', 'Management', 'Admin']))
                                    <th width="50" class="text-center">
                                        <i class="fas fa-check-square"></i>
                                    </th>
                                @endif
                                <th><i class="fas fa-calendar me-1"></i>Date</th>
                                <th><i class="fas fa-tools me-1"></i>Equipment</th>
                                <th><i class="fas fa-user me-1"></i>Inspector</th>
                                <th><i class="fas fa-flag me-1"></i>Status</th>
                                <th><i class="fas fa-exclamation-triangle me-1"></i>NG Items</th>
                                <th><i class="fas fa-tasks me-1"></i>Progress</th>
                                <th><i class="fas fa-user-check me-1"></i>Approved By</th>
                                <th><i class="fas fa-cogs me-1"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($allInspections as $inspection)
                                <tr>
                                    @if (in_array($userRole, ['Supervisor', 'Management', 'Admin']))
                                        <td class="text-center">
                                            @if ($inspection->status === 'pending')
                                                <div class="form-check">
                                                    <input class="form-check-input inspection-checkbox" type="checkbox"
                                                        value="{{ $inspection->id }}" onchange="toggleBulkActions()">
                                                </div>
                                            @endif
                                        </td>
                                    @endif
                                    <td>{{ $inspection->inspection_date->format('d/m/Y') }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ $inspection->equipment->equipment_code ?? '-' }}</strong>
                                            @if ($inspection->equipment->desc ?? false)
                                                <br><small class="text-muted">{{ $inspection->equipment->desc }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <i class="fas fa-user-circle text-primary me-1"></i>
                                        {{ $inspection->user->name ?? '-' }}
                                    </td>
                                    <td>
                                        @if ($inspection->status === 'approved')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Approved
                                            </span>
                                        @elseif ($inspection->status === 'pending')
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </span>
                                        @elseif ($inspection->status === 'rejected')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i>Rejected
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $ngCount = $inspection->details()->where('status', 'NG')->count();
                                        @endphp
                                        @if ($ngCount > 0)
                                            <span class="badge bg-danger">
                                                <i class="fas fa-exclamation-triangle me-1"></i>{{ $ngCount }} NG
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $totalItems = $inspection->details()->count();
                                            $completedItems = $inspection
                                                ->details()
                                                ->whereIn('status', ['OK', 'NG'])
                                                ->count();
                                            $percentage =
                                                $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar 
                                                @if ($percentage == 100) bg-success 
                                                @elseif($percentage >= 50) bg-warning 
                                                @else bg-danger @endif"
                                                role="progressbar" style="width: {{ $percentage }}%">
                                                {{ $percentage }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($inspection->approvedBy)
                                            <div>
                                                <i class="fas fa-user-check text-success me-1"></i>
                                                {{ $inspection->approvedBy->name }}
                                                <br><small
                                                    class="text-muted">{{ $inspection->approved_at->format('d/m/Y H:i') }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('sisca-v2.checksheets.show', $inspection->id) }}"
                                            class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip"
                                            title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if ($inspection->status === 'pending')
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-success"
                                                    onclick="approveInspection({{ $inspection->id }})"
                                                    data-bs-toggle="tooltip" title="Approve Inspection">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger"
                                                    onclick="rejectInspection({{ $inspection->id }})"
                                                    data-bs-toggle="tooltip" title="Reject Inspection">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @else
                                            @if ($inspection->approval_notes)
                                                <button type="button" class="btn btn-sm btn-outline-info"
                                                    onclick="showApprovalNotes('{{ $inspection->approval_notes }}')"
                                                    data-bs-toggle="tooltip" title="View Notes">
                                                    <i class="fas fa-sticky-note"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ in_array($userRole, ['Supervisor', 'Management', 'Admin']) ? '9' : '8' }}"
                                        class="text-center py-5">
                                        <i class="fas fa-clipboard-list text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-3 mb-0 h6">No inspections found</p>
                                        <small class="text-muted">No inspection data available for the selected
                                            criteria</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 text-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Showing {{ $allInspections->count() }} inspection(s) for {{ $selectedYear }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Modal -->
    <div class="modal fade" id="approvalModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approvalModalTitle">Approve Inspection</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="approvalForm">
                        <input type="hidden" id="inspectionId">
                        <input type="hidden" id="approvalAction">

                        <div class="mb-3">
                            <label for="approval_notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="approval_notes" rows="3"
                                placeholder="Enter approval/rejection notes (optional for approval, required for rejection)"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitApproval()">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes Modal -->
    <div class="modal fade" id="notesModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approval Notes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="notesContent"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Approval Modal -->
    <div class="modal fade" id="bulkApprovalModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Approve Inspections</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="bulkApprovalForm">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> You are about to approve <span id="selectedCount">0</span>
                            inspection(s).
                            @if ($userRole === 'Supervisor')
                                <br><strong>Note:</strong> Supervisors can only approve inspections once per month.
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="bulk_approval_notes" class="form-label">Approval Notes (Optional)</label>
                            <textarea class="form-control" id="bulk_approval_notes" rows="3"
                                placeholder="Enter bulk approval notes (optional)"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="submitBulkApproval()">
                        <i class="bi bi-check-lg"></i> Approve Selected
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Rejection Modal -->
    <div class="modal fade" id="bulkRejectionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Reject Inspections</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="bulkRejectionForm">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> You are about to reject <span
                                id="selectedRejectCount">0</span> inspection(s).
                        </div>

                        <div class="mb-3">
                            <label for="bulk_rejection_notes" class="form-label">Rejection Reason (Required)</label>
                            <textarea class="form-control" id="bulk_rejection_notes" rows="3" required
                                placeholder="Enter reason for bulk rejection (required)"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="submitBulkRejection()">
                        <i class="bi bi-x-lg"></i> Reject Selected
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Load areas when plant changes (for Admin/Management)
            function loadAreas() {
                const plantId = document.getElementById('plant_id')?.value;
                const equipmentTypeId = document.getElementById('equipment_type_id')?.value;

                if (plantId) {
                    loadAreasByPlantAndType(plantId, equipmentTypeId);
                }
            }

            // Load areas when equipment type changes
            function loadAreasWithEquipmentType() {
                @if (in_array($userRole, ['Admin', 'Management']))
                    const plantId = document.getElementById('plant_id')?.value;
                @else
                    const plantId = '{{ $user->plant_id ?? '' }}';
                @endif
                const equipmentTypeId = document.getElementById('equipment_type_id')?.value;

                // Reset area selection when equipment type changes
                const areaSelect = document.getElementById('area_id');
                if (areaSelect) {
                    areaSelect.value = '';
                }

                if (plantId) {
                    loadAreasByPlantAndType(plantId, equipmentTypeId);
                }
            }

            // Generic function to load areas based on plant and equipment type
            function loadAreasByPlantAndType(plantId, equipmentTypeId = '') {
                const areaSelect = document.getElementById('area_id');

                if (!areaSelect) {
                    console.warn('Area select element not found');
                    return;
                }

                // Clear current options and show loading state
                areaSelect.innerHTML = '<option value="">Loading areas...</option>';
                areaSelect.disabled = true;

                if (plantId) {
                    let url = `${window.location.origin}/sisca-v2/summary-report/areas-by-plant?plant_id=${plantId}`;
                    if (equipmentTypeId) {
                        url += `&equipment_type_id=${equipmentTypeId}`;
                    }

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Areas response:', data); // Debug log

                            // Reset select
                            areaSelect.innerHTML = '<option value="">All Areas</option>';
                            areaSelect.disabled = false;

                            // Handle different response formats
                            let areas = [];
                            if (Array.isArray(data)) {
                                areas = data;
                            } else if (data && Array.isArray(data.areas)) {
                                areas = data.areas;
                            } else if (data && typeof data === 'object') {
                                // Convert object to array if needed
                                areas = Object.values(data).filter(item => item && item.id && item.area_name);
                            }

                            if (areas.length === 0 && equipmentTypeId) {
                                const option = document.createElement('option');
                                option.value = '';
                                option.textContent = 'No areas available for selected equipment type';
                                option.disabled = true;
                                areaSelect.appendChild(option);
                            } else if (areas.length > 0) {
                                areas.forEach(area => {
                                    if (area && area.id && area.area_name) {
                                        const option = document.createElement('option');
                                        option.value = area.id;
                                        option.textContent = area.area_name;
                                        areaSelect.appendChild(option);
                                    }
                                });
                            } else {
                                console.warn('No valid areas found in response:', data);
                                const option = document.createElement('option');
                                option.value = '';
                                option.textContent = areas.length === 0 ? 'No areas available' :
                                    'Error loading areas - invalid data format';
                                option.disabled = true;
                                areaSelect.appendChild(option);
                            }
                        })
                        .catch(error => {
                            console.error('Error loading areas:', error);
                            areaSelect.innerHTML = '<option value="">All Areas</option>';
                            areaSelect.disabled = false;

                            const errorOption = document.createElement('option');
                            errorOption.value = '';
                            errorOption.textContent = 'Failed to load areas - please try again';
                            errorOption.disabled = true;
                            areaSelect.appendChild(errorOption);
                        });
                } else {
                    // No plant selected, reset to default state
                    areaSelect.innerHTML = '<option value="">All Areas</option>';
                    areaSelect.disabled = false;
                }
            }

            // Submit filter form
            function submitFilter() {
                document.getElementById('filterForm').submit();
            }

            // Refresh data
            function refreshData() {
                location.reload();
            }

            // Export PDF
            function exportPDF() {
                const form = document.getElementById('filterForm');
                const params = new URLSearchParams(new FormData(form));
                const url = `${window.location.origin}/sisca-v2/summary-report/export-pdf?${params.toString()}`;
                window.open(url, '_blank');
            }

            // Approve inspection
            function approveInspection(inspectionId) {
                document.getElementById('inspectionId').value = inspectionId;
                document.getElementById('approvalAction').value = 'approve';
                document.getElementById('approvalModalTitle').innerHTML =
                    '<i class="fas fa-check-circle me-2"></i>Approve Inspection';
                document.getElementById('approval_notes').placeholder = 'Enter approval notes (optional)';
                document.getElementById('approval_notes').required = false;

                const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
                modal.show();
            }

            // Reject inspection
            function rejectInspection(inspectionId) {
                document.getElementById('inspectionId').value = inspectionId;
                document.getElementById('approvalAction').value = 'reject';
                document.getElementById('approvalModalTitle').innerHTML =
                    '<i class="fas fa-times-circle me-2"></i>Reject Inspection';
                document.getElementById('approval_notes').placeholder = 'Enter rejection reason (required)';
                document.getElementById('approval_notes').required = true;

                const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
                modal.show();
            }

            // Submit approval/rejection
            function submitApproval() {
                const inspectionId = document.getElementById('inspectionId').value;
                const action = document.getElementById('approvalAction').value;
                const notes = document.getElementById('approval_notes').value;

                if (action === 'reject' && !notes.trim()) {
                    alert('Rejection reason is required');
                    return;
                }

                const url = action === 'approve' ?
                    `{{ url('sisca-v2/summary-report') }}/${inspectionId}/approve` :
                    `{{ url('sisca-v2/summary-report') }}/${inspectionId}/reject`;

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            approval_notes: notes
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(data.error || 'An error occurred');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred');
                    });

                const modal = bootstrap.Modal.getInstance(document.getElementById('approvalModal'));
                modal.hide();
            }

            // Show approval notes
            function showApprovalNotes(notes) {
                document.getElementById('notesContent').textContent = notes;
                const modal = new bootstrap.Modal(document.getElementById('notesModal'));
                modal.show();
            }

            // Bulk operations functions
            function toggleBulkActions() {
                const checkboxes = document.querySelectorAll('.inspection-checkbox:checked');
                const bulkActions = document.querySelector('.bulk-actions');

                if (checkboxes.length > 0) {
                    bulkActions.style.display = 'block';
                } else {
                    bulkActions.style.display = 'none';
                }
            }

            function showBulkApprovalModal() {
                const selectedCheckboxes = document.querySelectorAll('.inspection-checkbox:checked');
                const count = selectedCheckboxes.length;

                if (count === 0) {
                    alert('Please select at least one inspection to approve.');
                    return;
                }

                document.getElementById('selectedCount').textContent = count;
                const modal = new bootstrap.Modal(document.getElementById('bulkApprovalModal'));
                modal.show();
            }

            function showBulkRejectionModal() {
                const selectedCheckboxes = document.querySelectorAll('.inspection-checkbox:checked');
                const count = selectedCheckboxes.length;

                if (count === 0) {
                    alert('Please select at least one inspection to reject.');
                    return;
                }

                document.getElementById('selectedRejectCount').textContent = count;
                document.getElementById('bulk_rejection_notes').value = '';
                const modal = new bootstrap.Modal(document.getElementById('bulkRejectionModal'));
                modal.show();
            }

            function submitBulkApproval() {
                const selectedCheckboxes = document.querySelectorAll('.inspection-checkbox:checked');
                const inspectionIds = Array.from(selectedCheckboxes).map(cb => cb.value);
                const notes = document.getElementById('bulk_approval_notes').value;

                if (inspectionIds.length === 0) {
                    alert('No inspections selected');
                    return;
                }

                fetch(`${window.location.origin}/sisca-v2/summary-report/bulk-approve`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            inspection_ids: inspectionIds,
                            approval_notes: notes
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(data.error || 'An error occurred');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred');
                    });

                const modal = bootstrap.Modal.getInstance(document.getElementById('bulkApprovalModal'));
                modal.hide();
            }

            function submitBulkRejection() {
                const selectedCheckboxes = document.querySelectorAll('.inspection-checkbox:checked');
                const inspectionIds = Array.from(selectedCheckboxes).map(cb => cb.value);
                const notes = document.getElementById('bulk_rejection_notes').value;

                if (inspectionIds.length === 0) {
                    alert('No inspections selected');
                    return;
                }

                if (!notes.trim()) {
                    alert('Rejection reason is required');
                    return;
                }

                fetch(`${window.location.origin}/sisca-v2/summary-report/bulk-reject`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            inspection_ids: inspectionIds,
                            approval_notes: notes
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(data.error || 'An error occurred');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred');
                    });

                const modal = bootstrap.Modal.getInstance(document.getElementById('bulkRejectionModal'));
                modal.hide();
            }

            // Select All functionality
            document.addEventListener('DOMContentLoaded', function() {
                const selectAllCheckbox = document.getElementById('selectAll');
                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function() {
                        const inspectionCheckboxes = document.querySelectorAll('.inspection-checkbox');
                        inspectionCheckboxes.forEach(checkbox => {
                            checkbox.checked = selectAllCheckbox.checked;
                        });
                        toggleBulkActions();
                    });
                }
            });

            // Initialize area loading on page load
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl, {
                        html: true
                    });
                });

                // Load areas on page load if plant is already selected
                @if (in_array($userRole, ['Admin', 'Management']))
                    const initialPlantId = document.getElementById('plant_id')?.value;
                @else
                    const initialPlantId = '{{ $user->plant_id ?? '' }}';
                @endif
                const initialEquipmentTypeId = document.getElementById('equipment_type_id')?.value;

                if (initialPlantId) {
                    // Add a small delay to ensure DOM is fully loaded
                    setTimeout(() => {
                        loadAreasByPlantAndType(initialPlantId, initialEquipmentTypeId);
                    }, 100);
                }
            });
        </script>
    @endpush
@endsection
