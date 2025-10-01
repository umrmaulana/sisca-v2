@extends('layouts.app')

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
                <button type="button" class="btn btn-primary btn-sm" onclick="exportExcel()">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
                <button type="button" class="btn btn-info btn-sm" onclick="refreshData()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="card-title m-0 font-weight-bold">
                    <i class="fas fa-filter me-2"></i>Filter Options
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('summary-report.index') }}" id="filterForm">
                    <div class="row g-3 mb-3">
                        @if (in_array($userRole, ['Admin', 'Management']))
                            <!-- Company Filter -->
                            <div class="col-lg-3">
                                <label for="company_id" class="form-label fw-bold">Company</label>
                                <select class="form-select" id="company_id" name="company_id" onchange="loadAreas()">
                                    <option value="">All Companies</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}"
                                            {{ $selectedCompanyId == $company->id ? 'selected' : '' }}>
                                            {{ $company->company_name }}
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

                        <!-- Inspection Result Filter -->
                        <div class="col-lg-2">
                            <label for="inspection_result" class="form-label fw-bold">Inspection Result</label>
                            <select class="form-select" id="inspection_result" name="inspection_result"
                                onchange="submitFilter()">
                                <option value="all" {{ $selectedInspectionResult == 'all' ? 'selected' : '' }}>All
                                    Results</option>
                                <option value="ok" {{ $selectedInspectionResult == 'ok' ? 'selected' : '' }}>OK Only
                                </option>
                                <option value="ng" {{ $selectedInspectionResult == 'ng' ? 'selected' : '' }}>NG Only
                                </option>
                                <option value="not_inspected"
                                    {{ $selectedInspectionResult == 'not_inspected' ? 'selected' : '' }}>Not Inspected
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
                            <a href="{{ route('summary-report.index') }}" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Equipment Summary Section -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-dark">
                <h6 class="card-title m-0 font-weight-bold">
                    <i class="fas fa-table me-2"></i>Annual Inspection Report - {{ $selectedYear }}
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="equipmentSummaryTable">
                        <thead class="table-dark">
                            <tr>
                                <th>Code</th>
                                <th>Equipment</th>
                                <th>Area</th>
                                <th class="text-center">Jan</th>
                                <th class="text-center">Feb</th>
                                <th class="text-center">Mar</th>
                                <th class="text-center">Apr</th>
                                <th class="text-center">May</th>
                                <th class="text-center">Jun</th>
                                <th class="text-center">Jul</th>
                                <th class="text-center">Aug</th>
                                <th class="text-center">Sep</th>
                                <th class="text-center">Oct</th>
                                <th class="text-center">Nov</th>
                                <th class="text-center">Dec</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($equipmentSummary as $equipment)
                                <tr>
                                    <td>{{ $equipment['equipment_code'] }}</td>
                                    <td>{{ $equipment['equipment_name'] }} - {{ $equipment['equipment_type'] }}</td>
                                    <td>{{ $equipment['area'] }}</td>
                                    @for ($month = 1; $month <= 12; $month++)
                                        @php
                                            $monthData = $equipment['monthly_data'][$month] ?? null;
                                        @endphp
                                        <td class="text-center">
                                            @if ($monthData)
                                                @if ($monthData['is_ok'])
                                                    <span class="badge bg-success">OK</span>
                                                @elseif ($monthData['has_ng_items'])
                                                    <span class="badge bg-danger clickable-ng" style="cursor: pointer;"
                                                        data-ng-items="{{ json_encode($monthData['ng_items'] ?? []) }}"
                                                        data-equipment="{{ $equipment['equipment_code'] }}"
                                                        data-month="{{ date('F', mktime(0, 0, 0, $month, 1)) }}"
                                                        onclick="showNgDetails(this)">NG</span>
                                                @else
                                                    @switch($monthData['status'])
                                                        @case('pending')
                                                            <span class="badge bg-warning text-dark">P</span>
                                                        @break

                                                        @case('rejected')
                                                            <span class="badge bg-secondary">R</span>
                                                        @break

                                                        @case('not_inspected')
                                                            <span class="badge bg-light text-muted">-</span>
                                                        @break

                                                        @default
                                                            <span
                                                                class="badge bg-info text-white">{{ ucfirst($monthData['status']) }}</span>
                                                    @endswitch
                                                @endif
                                            @else
                                                <span class="badge bg-light text-muted">-</span>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="15" class="text-center">No equipment data found for the selected
                                            criteria.</td>
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
                                    <small>Inspection Approved & All Items OK</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-danger me-2">
                                        <i class="fas fa-exclamation-circle me-1"></i>NG
                                    </span>
                                    <small>Inspection has NG Items</small>
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
                                <strong>Info:</strong> Total OK column shows count of approved inspections with all items OK per
                                equipment
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inspections List Section -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="card-title m-0 font-weight-bold">
                        <i class="fas fa-list-alt me-2"></i>Inspection Details
                    </h6>

                    <!-- Bulk Actions for Supervisors -->
                    @if (in_array($userRole, ['Supervisor', 'Management', 'Admin']))
                        <div class="bulk-actions" style="display: none;">
                            <button type="button" class="btn btn-success btn-sm me-2" onclick="showBulkApprovalModal()">
                                <i class="fas fa-check"></i> Approve All
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="showBulkRejectionModal()">
                                <i class="fas fa-times"></i> Reject All
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
                                    <i class="fas fa-check-square me-1"></i>Select All on This Page
                                </label>
                                <small class="text-muted d-block">
                                    <i class="fas fa-info-circle me-1"></i>Only selects pending inspections visible on current
                                    page
                                </small>
                            </div>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="inspectionsTable">
                            <thead class="table-light">
                                <tr>
                                    @if (in_array($userRole, ['Supervisor', 'Management', 'Admin']))
                                        <th width="30">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                    @endif
                                    <th><i class="fas fa-calendar me-1"></i>Date</th>
                                    <th><i class="fas fa-barcode me-1"></i>Code</th>
                                    <th><i class="fas fa-tools me-1"></i>Equipment</th>
                                    <th><i class="fas fa-map-marker-alt me-1"></i>Area</th>
                                    <th><i class="fas fa-user me-1"></i>Inspector</th>
                                    <th><i class="fas fa-flag me-1"></i>Status</th>
                                    <th><i class="fas fa-user-check me-1"></i>Approved By</th>
                                    <th><i class="fas fa-clock me-1"></i>Approved At</th>
                                    <th><i class="fas fa-cogs me-1"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inspections as $inspection)
                                    <tr>
                                        @if (in_array($userRole, ['Supervisor', 'Management', 'Admin']))
                                            <td>
                                                @if ($inspection->status === 'pending')
                                                    <input type="checkbox" class="form-check-input inspection-checkbox"
                                                        value="{{ $inspection->id }}" onchange="toggleBulkActions()">
                                                @endif
                                            </td>
                                        @endif
                                        <td>{{ $inspection->inspection_date->format('d/m/Y') }}</td>
                                        <td>{{ $inspection->equipment->equipment_code ?? '-' }}</td>
                                        <td>{{ $inspection->equipment->equipmentType->equipment_name }} -
                                            {{ $inspection->equipment->equipmentType->equipment_type }}</td>
                                        <td>{{ $inspection->equipment->location->area->area_name ?? '-' }}</td>
                                        <td>{{ $inspection->user->name ?? '-' }}</td>
                                        <td>
                                            @switch($inspection->status)
                                                @case('approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @break

                                                @case('pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @break

                                                @case('rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @break

                                                @default
                                                    <span class="badge bg-secondary">{{ ucfirst($inspection->status) }}</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $inspection->approvedBy->name ?? '-' }}</td>
                                        <td>{{ $inspection->approved_at ? $inspection->approved_at->format('d/m/Y H:i') : '-' }}
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <!-- View Inspection Button - Always visible -->
                                                <a href="{{ route('inspections.show', $inspection->id) }}"
                                                    class="btn btn-outline-primary btn-sm" title="View Inspection">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if (in_array($userRole, ['Supervisor', 'Management', 'Admin']))
                                                    @if ($inspection->status === 'pending')
                                                        <button class="btn btn-success btn-sm"
                                                            onclick="approveInspection({{ $inspection->id }})"
                                                            title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button class="btn btn-danger btn-sm"
                                                            onclick="rejectInspection({{ $inspection->id }})" title="Reject">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @elseif ($inspection->status === 'approved' && in_array($userRole, ['Management', 'Admin']))
                                                        <button class="btn btn-warning btn-sm"
                                                            onclick="editApprovedInspection({{ $inspection->id }})"
                                                            title="Edit Approved Inspection">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        @if (!empty($inspection->approval_notes))
                                                            <button class="btn btn-info btn-sm"
                                                                onclick="showApprovalNotes('{{ addslashes($inspection->approval_notes) }}')"
                                                                title="View Notes">
                                                                <i class="fas fa-sticky-note"></i>
                                                            </button>
                                                        @endif
                                                    @elseif (!empty($inspection->approval_notes))
                                                        <button class="btn btn-info btn-sm"
                                                            onclick="showApprovalNotes('{{ addslashes($inspection->approval_notes) }}')"
                                                            title="View Notes">
                                                            <i class="fas fa-sticky-note"></i>
                                                        </button>
                                                    @endif

                                                    <!-- Back Date Button - Available for all statuses -->
                                                    @if (in_array($userRole, ['Management', 'Admin']))
                                                        <button class="btn btn-secondary btn-sm"
                                                            onclick="showBackDateModal({{ $inspection->id }})"
                                                            title="Change Inspection Date (Back Date)">
                                                            <i class="fas fa-calendar-alt"></i>
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ in_array($userRole, ['Supervisor', 'Management', 'Admin']) ? '10' : '9' }}"
                                                class="text-center">
                                                No inspections found for the selected criteria.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary Info -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Total: {{ $totalInspections }} inspection(s) for {{ $selectedYear }}
                            </small>
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
                                <h5 class="modal-title">Approve All Inspections</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="bulkApprovalForm">
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle"></i> You are about to approve <span
                                            id="selectedCount">0</span>
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
                                        <textarea class="form-control" id="bulk_rejection_notes" rows="3"
                                            placeholder="Enter reason for bulk rejection"></textarea>
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

                <!-- NG Details Modal -->
                <div class="modal fade" id="ngDetailsModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>NG Items Details
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Equipment:</strong> <span id="ngEquipmentCode" class="text-primary"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Month:</strong> <span id="ngMonth" class="text-info"></span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <strong>NG Check Items:</strong>
                                    <div id="ngItemsList" class="mt-2">
                                        <!-- Dynamic content will be loaded here -->
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Approved Inspection Modal -->
                <div class="modal fade" id="editApprovedModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-edit text-warning me-2"></i>Edit Approved Inspection
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editApprovedForm">
                                    <input type="hidden" id="editInspectionId" name="inspection_id">

                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Warning:</strong> You are editing an approved inspection. This action will be
                                        logged.
                                    </div>

                                    <div class="mb-3">
                                        <label for="editInspectionDate" class="form-label">
                                            <i class="fas fa-calendar me-1"></i>Inspection Date <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" class="form-control" id="editInspectionDate"
                                            name="inspection_date" required>
                                        <div class="form-text">Change the inspection date (back dating allowed)</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="editStatus" class="form-label">
                                            <i class="fas fa-check-circle me-1"></i>Status
                                        </label>
                                        <select class="form-select" id="editStatus" name="status">
                                            <option value="approved">Approved</option>
                                            <option value="pending">Pending</option>
                                            <option value="rejected">Rejected</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="editNotes" class="form-label">
                                            <i class="fas fa-sticky-note me-1"></i>Edit Notes <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" id="editNotes" name="edit_notes" rows="3"
                                            placeholder="Enter reason for editing this approved inspection..." required></textarea>
                                        <div class="form-text">This note will be logged for audit trail</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="editApprovalNotes" class="form-label">
                                            <i class="fas fa-comment me-1"></i>Approval Notes
                                        </label>
                                        <textarea class="form-control" id="editApprovalNotes" name="approval_notes" rows="2"
                                            placeholder="Update approval notes..."></textarea>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-warning" onclick="submitEditApproved()">
                                    <i class="fas fa-save me-1"></i>Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Back Date Inspection Modal -->
                <div class="modal fade" id="backDateModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-calendar-alt text-info me-2"></i>Change Inspection Date (Back Date)
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="backDateForm">
                                    <input type="hidden" id="backDateInspectionId" name="inspection_id">

                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Information:</strong> You are changing the inspection date. This action will be
                                        logged
                                        for audit purposes.
                                    </div>

                                    <!-- Current Date History -->
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-history me-1"></i>Current Inspection Date
                                        </label>
                                        <div class="card bg-light">
                                            <div class="card-body py-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span id="currentInspectionDate" class="fw-bold text-primary">-</span>
                                                    <small class="text-muted">Current Date</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Date Change History -->
                                    <div class="mb-3" id="dateHistorySection" style="display: none;">
                                        <label class="form-label">
                                            <i class="fas fa-clock me-1"></i>Date Change History
                                        </label>
                                        <div id="dateHistoryList" class="card bg-light">
                                            <div class="card-body py-2">
                                                <small class="text-muted">No previous changes</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="newInspectionDate" class="form-label">
                                            <i class="fas fa-calendar me-1"></i>New Inspection Date <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="date" class="form-control" id="newInspectionDate"
                                            name="new_inspection_date" required>
                                        <div class="form-text">Select the new inspection date (back dating is allowed)</div>
                                    </div>

                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-info" onclick="submitBackDate()">
                                    <i class="fas fa-save me-1"></i>Update Date
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endsection

        @push('scripts')
            <script>
                /**
                 * Summary Report Management Class
                 * Clean, modular JavaScript for managing summary report functionality
                 */
                class SummaryReportManager {
                    constructor() {
                        this.init();
                        this.bindEvents();
                        this.setupCSRF();
                    }

                    // Initialize the application
                    init() {
                        this.initializeTooltips();
                        this.initializeDataTables();
                        this.loadInitialAreas();
                    }

                    // Setup CSRF token for all Ajax requests
                    setupCSRF() {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                    }

                    // Bind all event listeners
                    bindEvents() {
                        // Select all checkbox functionality
                        const selectAllCheckbox = document.getElementById('selectAll');
                        if (selectAllCheckbox) {
                            selectAllCheckbox.addEventListener('change', () => {
                                this.handleSelectAll(selectAllCheckbox.checked);
                            });
                        }

                        // Individual checkbox change
                        document.addEventListener('change', (e) => {
                            if (e.target.classList.contains('inspection-checkbox')) {
                                this.toggleBulkActions();
                            }
                        });
                    }

                    // Utility methods
                    getElement(id) {
                        const element = document.getElementById(id);
                        if (!element) {
                            console.warn(`Element with id '${id}' not found`);
                        }
                        return element;
                    }

                    showError(message) {
                        alert('Error: ' + message);
                    }

                    showSuccess(message) {
                        alert(message);
                    }

                    // Area Management
                    loadAreas() {
                        @if (in_array($userRole, ['Admin', 'Management']))
                            const companyId = this.getElement('company_id')?.value;
                        @else
                            const companyId = '{{ $user->company_id ?? '' }}';
                        @endif
                        const equipmentTypeId = this.getElement('equipment_type_id')?.value;

                        // Load equipment types for selected company
                        this.loadEquipmentTypesByCompany(companyId);

                        if (companyId) {
                            this.loadAreasByCompanyAndType(companyId, equipmentTypeId);
                        }
                    }

                    async loadEquipmentTypesByCompany(companyId) {
                        const equipmentTypeSelect = this.getElement('equipment_type_id');
                        if (!equipmentTypeSelect) return;

                        // Store current selection
                        const currentSelection = equipmentTypeSelect.value;

                        // Clear current options and show loading state
                        equipmentTypeSelect.innerHTML = '<option value="">Loading equipment types...</option>';
                        equipmentTypeSelect.disabled = true;

                        try {
                            const response = await fetch(
                                `{{ route('summary-report.equipment-types-by-company') }}?company_id=${companyId}`);
                            const equipmentTypes = await response.json();

                            // Clear and rebuild options
                            equipmentTypeSelect.innerHTML = '<option value="">All Equipment Types</option>';

                            equipmentTypes.forEach(equipmentType => {
                                const option = document.createElement('option');
                                option.value = equipmentType.id;
                                option.textContent = equipmentType.name;
                                if (equipmentType.id == currentSelection) {
                                    option.selected = true;
                                }
                                equipmentTypeSelect.appendChild(option);
                            });

                            equipmentTypeSelect.disabled = false;

                        } catch (error) {
                            console.error('Error loading equipment types:', error);
                            equipmentTypeSelect.innerHTML = '<option value="">Error loading equipment types</option>';
                            equipmentTypeSelect.disabled = false;
                        }
                    }

                    loadAreasWithEquipmentType() {
                        @if (in_array($userRole, ['Admin', 'Management']))
                            const companyId = this.getElement('company_id')?.value;
                        @else
                            const companyId = '{{ $user->company_id ?? '' }}';
                        @endif
                        const equipmentTypeId = this.getElement('equipment_type_id')?.value;

                        // Reset area selection when equipment type changes
                        const areaSelect = this.getElement('area_id');
                        if (areaSelect) {
                            areaSelect.value = '';
                        }

                        if (companyId) {
                            this.loadAreasByCompanyAndType(companyId, equipmentTypeId);
                        }
                    }

                    async loadAreasByCompanyAndType(companyId, equipmentTypeId = '') {
                        const areaSelect = this.getElement('area_id');
                        if (!areaSelect) return;

                        // Clear current options and show loading state
                        areaSelect.innerHTML = '<option value="">Loading areas...</option>';
                        areaSelect.disabled = true;

                        if (!companyId) {
                            areaSelect.innerHTML = '<option value="">All Areas</option>';
                            areaSelect.disabled = false;
                            return;
                        }

                        try {
                            let url = `{{ url('summary-report/areas-by-company') }}?company_id=${companyId}`;
                            if (equipmentTypeId) {
                                url += `&equipment_type_id=${equipmentTypeId}`;
                            }

                            const response = await fetch(url);
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }

                            const data = await response.json();
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
                                const option = document.createElement('option');
                                option.value = '';
                                option.textContent = 'No areas found';
                                option.disabled = true;
                                areaSelect.appendChild(option);
                            }

                        } catch (error) {
                            console.error('Error loading areas:', error);
                            areaSelect.innerHTML = '<option value="">All Areas</option>';
                            areaSelect.disabled = false;

                            const errorOption = document.createElement('option');
                            errorOption.value = '';
                            errorOption.textContent = 'Failed to load areas - please try again';
                            errorOption.disabled = true;
                            areaSelect.appendChild(errorOption);
                        }
                    }

                    // Export and Utility Functions
                    submitFilter() {
                        const form = this.getElement('filterForm');
                        if (form) {
                            // Update DataTables with current filters before submitting
                            this.refreshDataTables();
                            form.submit();
                        }
                    }

                    refreshDataTables() {
                        // Get current filter values
                        const formData = new FormData(this.getElement('filterForm'));
                        const params = new URLSearchParams(formData);

                        // Reload equipment summary table with filters
                        if (window.equipmentSummaryTable) {
                            try {
                                window.equipmentSummaryTable.ajax.url(
                                    `{{ route('summary-report.equipment-data') }}?${params.toString()}`
                                ).load();
                            } catch (error) {
                                console.log('Equipment table reload not available, using form submit');
                            }
                        }

                        // Reload inspections table with filters  
                        if (window.inspectionsTable) {
                            try {
                                window.inspectionsTable.ajax.url(
                                    `{{ route('summary-report.inspections-data') }}?${params.toString()}`
                                ).load();
                            } catch (error) {
                                console.log('Inspections table reload not available, using form submit');
                            }
                        }
                    }
                    refreshData() {
                        location.reload();
                    }

                    exportPDF() {
                        const form = this.getElement('filterForm');
                        if (!form) return;

                        const params = new URLSearchParams(new FormData(form));
                        const url = `{{ url('summary-report/export-pdf') }}?${params.toString()}`;
                        window.open(url, '_blank');
                    }

                    exportExcel() {
                        const form = this.getElement('filterForm');
                        if (!form) return;

                        const params = new URLSearchParams(new FormData(form));
                        const url = `{{ url('summary-report/export-excel') }}?${params.toString()}`;
                        window.location.href = url;
                    } // Inspection Management
                    approveInspection(inspectionId) {
                        const inspectionIdInput = this.getElement('inspectionId');
                        const approvalAction = this.getElement('approvalAction');
                        const approvalModalTitle = this.getElement('approvalModalTitle');
                        const approvalNotes = this.getElement('approval_notes');

                        if (!inspectionIdInput || !approvalAction || !approvalModalTitle || !approvalNotes) return;

                        inspectionIdInput.value = inspectionId;
                        approvalAction.value = 'approve';
                        approvalModalTitle.innerHTML = '<i class="fas fa-check-circle me-2"></i>Approve Inspection';
                        approvalNotes.placeholder = 'Enter approval notes (optional)';
                        approvalNotes.required = false;

                        const modal = new bootstrap.Modal(this.getElement('approvalModal'));
                        modal.show();
                    }

                    rejectInspection(inspectionId) {
                        const inspectionIdInput = this.getElement('inspectionId');
                        const approvalAction = this.getElement('approvalAction');
                        const approvalModalTitle = this.getElement('approvalModalTitle');
                        const approvalNotes = this.getElement('approval_notes');

                        if (!inspectionIdInput || !approvalAction || !approvalModalTitle || !approvalNotes) return;

                        inspectionIdInput.value = inspectionId;
                        approvalAction.value = 'reject';
                        approvalModalTitle.innerHTML = '<i class="fas fa-times-circle me-2"></i>Reject Inspection';
                        approvalNotes.placeholder = 'Enter rejection reason (required)';
                        approvalNotes.required = true;

                        const modal = new bootstrap.Modal(this.getElement('approvalModal'));
                        modal.show();
                    }

                    async submitApproval() {
                        const inspectionId = this.getElement('inspectionId')?.value;
                        const action = this.getElement('approvalAction')?.value;
                        const notes = this.getElement('approval_notes')?.value?.trim();

                        if (!inspectionId || !action) {
                            this.showError('Missing inspection data');
                            return;
                        }

                        if (action === 'reject' && !notes) {
                            this.showError('Rejection reason is required');
                            this.getElement('approval_notes')?.focus();
                            return;
                        }

                        try {
                            const url = `{{ url('summary-report') }}/${inspectionId}/${action}`;
                            const response = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    approval_notes: notes
                                })
                            });

                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }

                            const data = await response.json();

                            if (data.success) {
                                this.showSuccess(data.message);
                                location.reload();
                            } else {
                                throw new Error(data.error || 'Unknown error occurred');
                            }

                        } catch (error) {
                            console.error('Approval error:', error);
                            this.showError(error.message);
                        }

                        const modal = bootstrap.Modal.getInstance(this.getElement('approvalModal'));
                        modal?.hide();
                    }

                    showApprovalNotes(notes) {
                        const notesContent = this.getElement('notesContent');
                        if (notesContent) {
                            notesContent.textContent = notes;
                            const modal = new bootstrap.Modal(this.getElement('notesModal'));
                            modal?.show();
                        }
                    }

                    // Bulk Operations
                    toggleBulkActions() {
                        const checkboxes = document.querySelectorAll('.inspection-checkbox:checked');
                        const bulkActions = document.querySelector('.bulk-actions');

                        if (bulkActions) {
                            bulkActions.style.display = checkboxes.length > 0 ? 'block' : 'none';
                        }
                    }

                    handleSelectAll(checked) {
                        const inspectionCheckboxes = document.querySelectorAll('.inspection-checkbox');
                        inspectionCheckboxes.forEach(checkbox => {
                            checkbox.checked = checked;
                        });
                        this.toggleBulkActions();
                    }

                    showBulkApprovalModal() {
                        const selectedCheckboxes = document.querySelectorAll('.inspection-checkbox:checked');
                        const count = selectedCheckboxes.length;

                        if (count === 0) {
                            this.showError('Please select at least one inspection to approve.');
                            return;
                        }

                        const selectedCountElement = this.getElement('selectedCount');
                        if (selectedCountElement) {
                            selectedCountElement.textContent = count;
                        }

                        const modal = new bootstrap.Modal(this.getElement('bulkApprovalModal'));
                        modal?.show();
                    }

                    showBulkRejectionModal() {
                        const selectedCheckboxes = document.querySelectorAll('.inspection-checkbox:checked');
                        const count = selectedCheckboxes.length;

                        if (count === 0) {
                            this.showError('Please select at least one inspection to reject.');
                            return;
                        }

                        const selectedRejectCountElement = this.getElement('selectedRejectCount');
                        const bulkRejectionNotes = this.getElement('bulk_rejection_notes');

                        if (selectedRejectCountElement) {
                            selectedRejectCountElement.textContent = count;
                        }
                        if (bulkRejectionNotes) {
                            bulkRejectionNotes.value = '';
                        }

                        const modal = new bootstrap.Modal(this.getElement('bulkRejectionModal'));
                        modal?.show();
                    }

                    async submitBulkApproval() {
                        const selectedCheckboxes = document.querySelectorAll('.inspection-checkbox:checked');
                        const inspectionIds = Array.from(selectedCheckboxes).map(cb => parseInt(cb.value));
                        const notes = this.getElement('bulk_approval_notes')?.value?.trim();

                        if (inspectionIds.length === 0) {
                            this.showError('No inspections selected');
                            return;
                        }

                        const submitBtn = event.target;
                        const originalText = submitBtn.innerHTML;

                        try {
                            // Show loading state
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                            submitBtn.disabled = true;

                            const response = await fetch(`{{ route('summary-report.bulk-approve') }}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    inspection_ids: inspectionIds,
                                    approval_notes: notes
                                })
                            });

                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }

                            const data = await response.json();

                            if (data.success) {
                                this.showSuccess(data.message);
                                location.reload();
                            } else {
                                throw new Error(data.error || 'Unknown error occurred');
                            }

                        } catch (error) {
                            console.error('Bulk approval error:', error);
                            this.showError(error.message);
                        } finally {
                            // Restore button state
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }

                        const modal = bootstrap.Modal.getInstance(this.getElement('bulkApprovalModal'));
                        modal?.hide();
                    }

                    async submitBulkRejection() {
                        const selectedCheckboxes = document.querySelectorAll('.inspection-checkbox:checked');
                        const inspectionIds = Array.from(selectedCheckboxes).map(cb => parseInt(cb.value));
                        const notes = this.getElement('bulk_rejection_notes')?.value?.trim();

                        if (inspectionIds.length === 0) {
                            this.showError('No inspections selected');
                            return;
                        }

                        if (!notes) {
                            this.showError('Rejection reason is required');
                            this.getElement('bulk_rejection_notes')?.focus();
                            return;
                        }

                        const submitBtn = event.target;
                        const originalText = submitBtn.innerHTML;

                        try {
                            // Show loading state
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                            submitBtn.disabled = true;

                            const response = await fetch(`{{ route('summary-report.bulk-reject') }}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    inspection_ids: inspectionIds,
                                    approval_notes: notes
                                })
                            });

                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }

                            const data = await response.json();

                            if (data.success) {
                                this.showSuccess(data.message);
                                location.reload();
                            } else {
                                throw new Error(data.error || 'Unknown error occurred');
                            }

                        } catch (error) {
                            console.error('Bulk rejection error:', error);
                            this.showError(error.message);
                        } finally {
                            // Restore button state
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }

                        const modal = bootstrap.Modal.getInstance(this.getElement('bulkRejectionModal'));
                        modal?.hide();
                    }

                    // Initialize tooltips
                    initializeTooltips() {
                        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        tooltipTriggerList.map(function(tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl, {
                                html: true
                            });
                        });
                    }

                    // Load initial areas and equipment types on page load
                    loadInitialAreas() {
                        @if (in_array($userRole, ['Admin', 'Management']))
                            const initialCompanyId = this.getElement('company_id')?.value;
                        @else
                            const initialCompanyId = '{{ $user->company_id ?? '' }}';
                        @endif
                        const initialEquipmentTypeId = this.getElement('equipment_type_id')?.value;

                        if (initialCompanyId) {
                            // Add a small delay to ensure DOM is fully loaded
                            setTimeout(() => {
                                // Load equipment types first, then areas
                                this.loadEquipmentTypesByCompany(initialCompanyId);
                                this.loadAreasByCompanyAndType(initialCompanyId, initialEquipmentTypeId);
                            }, 100);
                        }
                    }

                    // Initialize DataTables
                    initializeDataTables() {
                        // Test if jQuery and DataTables are loaded
                        if (typeof $ === 'undefined') {
                            console.error('jQuery is not loaded');
                            return;
                        }
                        if (typeof $.fn.DataTable === 'undefined') {
                            console.error('DataTables is not loaded');
                            return;
                        }

                        // Initialize DataTables with optimized settings

                        // Initialize Equipment Summary DataTable (client-side processing)
                        try {
                            equipmentSummaryTable = $('#equipmentSummaryTable').DataTable({
                                pageLength: 10,
                                responsive: false, // Disable responsive for better control
                                scrollX: true,
                                scrollCollapse: true,
                                autoWidth: false,
                                order: [
                                    [0, 'asc']
                                ], // Sort by equipment code
                                language: {
                                    emptyTable: "No equipment data found for the selected criteria",
                                    search: "Search equipment:",
                                    lengthMenu: "Show _MENU_ equipment per page",
                                    info: "Showing _START_ to _END_ of _TOTAL_ equipment",
                                    paginate: {
                                        first: "First",
                                        last: "Last",
                                        next: "Next",
                                        previous: "Previous"
                                    }
                                },
                                columnDefs: [{
                                        orderable: false,
                                        targets: [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14], // Month columns
                                        width: "60px",
                                        className: "text-center"
                                    },
                                    {
                                        targets: [0], // Equipment Code
                                        width: "100px"
                                    },
                                    {
                                        targets: [1], // Equipment Name
                                        width: "150px"
                                    },
                                    {
                                        targets: [2], // Area
                                        width: "120px"
                                    }
                                ],
                                drawCallback: function() {
                                    // Ensure proper alignment after each redraw
                                    $(this.api().table().container()).find('.dataTables_scrollHead table').css(
                                        'margin-bottom', '0');
                                }
                            });

                        } catch (error) {
                            console.error('Error initializing Equipment DataTable:', error);
                        }

                        // Initialize Inspections DataTable (client-side processing)
                        try {
                            inspectionsTable = $('#inspectionsTable').DataTable({
                                pageLength: 10,
                                responsive: true,
                                autoWidth: false,
                                width: "100%",
                                order: [
                                    [{{ in_array($userRole, ['Supervisor', 'Management', 'Admin']) ? '1' : '0' }},
                                        'desc'
                                    ]
                                ], // Sort by date desc
                                language: {
                                    emptyTable: "No inspections found for the selected criteria",
                                    search: "Search inspections:",
                                    lengthMenu: "Show _MENU_ inspections per page",
                                    info: "Showing _START_ to _END_ of _TOTAL_ inspections",
                                    paginate: {
                                        first: "First",
                                        last: "Last",
                                        next: "Next",
                                        previous: "Previous"
                                    }
                                },
                                columnDefs: [{
                                        orderable: false,
                                        targets: [
                                            {{ in_array($userRole, ['Supervisor', 'Management', 'Admin']) ? '-1' : '-1' }}
                                        ]
                                    }, // Disable sorting for actions column
                                    {
                                        // Custom sorting for inspection date column
                                        targets: [
                                            {{ in_array($userRole, ['Supervisor', 'Management', 'Admin']) ? '1' : '0' }}
                                        ], // Date column index
                                        type: 'date',
                                        render: function(data, type, row) {
                                            if (type === 'display' || type === 'type') {
                                                return data;
                                            } else if (type === 'sort') {
                                                // Convert DD/MM/YYYY to YYYY-MM-DD for proper sorting
                                                if (data && data.match(/^\d{2}\/\d{2}\/\d{4}$/)) {
                                                    const parts = data.split('/');
                                                    return parts[2] + '-' + parts[1] + '-' + parts[
                                                        0]; // YYYY-MM-DD
                                                }
                                                return data;
                                            }
                                            return data;
                                        }
                                    }
                                ],
                                drawCallback: function() {
                                    // Maintain table layout after redraw
                                    $(this.api().table().container()).css('width', '100%');
                                }
                            });

                        } catch (error) {
                            console.error('Error initializing Inspections DataTable:', error);
                        }
                    }

                    // Additional functions that need to be added to the class
                    showNgDetails(element) {
                        const ngItemsData = element.getAttribute('data-ng-items');
                        const equipment = element.getAttribute('data-equipment');
                        const month = element.getAttribute('data-month');

                        const ngEquipmentCode = this.getElement('ngEquipmentCode');
                        const ngMonth = this.getElement('ngMonth');
                        const ngItemsList = this.getElement('ngItemsList');

                        if (ngEquipmentCode) ngEquipmentCode.textContent = equipment || 'Unknown';
                        if (ngMonth) ngMonth.textContent = month || 'Unknown';

                        if (ngItemsList) {
                            try {
                                if (ngItemsData && ngItemsData.trim() !== '' && ngItemsData !== 'null') {
                                    const ngItems = JSON.parse(ngItemsData);
                                    if (Array.isArray(ngItems) && ngItems.length > 0) {
                                        let itemsHtml = '<div class="list-group">';
                                        ngItems.forEach((item, index) => {
                                            itemsHtml +=
                                                `<div class="list-group-item">${item.item_name || 'Item ' + (index + 1)}: ${item.status || 'NG'}</div>`;
                                        });
                                        itemsHtml += '</div>';
                                        ngItemsList.innerHTML = itemsHtml;
                                    } else {
                                        ngItemsList.innerHTML =
                                            '<div class="alert alert-info">No specific NG items recorded</div>';
                                    }
                                } else {
                                    ngItemsList.innerHTML =
                                        '<div class="alert alert-info">NG status detected but no specific items recorded</div>';
                                }
                            } catch (error) {
                                console.error('Error parsing NG items:', error);
                                ngItemsList.innerHTML = '<div class="alert alert-danger">Error loading NG items data</div>';
                            }
                        }

                        const modal = new bootstrap.Modal(this.getElement('ngDetailsModal'));
                        modal?.show();
                    }



                    // Method: showNgDetails
                    showNgDetails(element) {
                        const ngItemsData = element.getAttribute('data-ng-items');
                        const equipment = element.getAttribute('data-equipment');
                        const month = element.getAttribute('data-month');

                        const ngEquipmentCode = this.getElement('ngEquipmentCode');
                        const ngMonth = this.getElement('ngMonth');
                        const ngItemsList = this.getElement('ngItemsList');

                        if (ngEquipmentCode) ngEquipmentCode.textContent = equipment || 'Unknown';
                        if (ngMonth) ngMonth.textContent = month || 'Unknown';

                        if (ngItemsList) {
                            try {
                                if (ngItemsData && ngItemsData.trim() !== '' && ngItemsData !== 'null') {
                                    const ngItems = JSON.parse(ngItemsData);
                                    if (Array.isArray(ngItems) && ngItems.length > 0) {
                                        let itemsHtml = '<div class="list-group">';
                                        ngItems.forEach((item, index) => {
                                            itemsHtml +=
                                                `<div class="list-group-item">${item.item_name || 'Item ' + (index + 1)}: ${item.status || 'NG'}</div>`;
                                        });
                                        itemsHtml += '</div>';
                                        ngItemsList.innerHTML = itemsHtml;
                                    } else {
                                        ngItemsList.innerHTML =
                                            '<div class="alert alert-info">No specific NG items recorded</div>';
                                    }
                                } else {
                                    ngItemsList.innerHTML =
                                        '<div class="alert alert-info">NG status detected but no specific items recorded</div>';
                                }
                            } catch (error) {
                                console.error('Error parsing NG items:', error);
                                ngItemsList.innerHTML = '<div class="alert alert-danger">Error loading NG items data</div>';
                            }
                        }

                        const modal = new bootstrap.Modal(this.getElement('ngDetailsModal'));
                        modal?.show();
                    }

                    // Method: editApprovedInspection
                    async editApprovedInspection(inspectionId) {
                        try {
                            const response = await fetch(
                                `{{ url('summary-report/get-inspection') }}/${inspectionId}`);
                            const data = await response.json();

                            if (data.success) {
                                const inspection = data.inspection;

                                const editInspectionId = this.getElement('editInspectionId');
                                const editInspectionDate = this.getElement('editInspectionDate');
                                const editStatus = this.getElement('editStatus');
                                const editApprovalNotes = this.getElement('editApprovalNotes');
                                const editNotes = this.getElement('editNotes');

                                if (editInspectionId) editInspectionId.value = inspectionId;
                                if (editInspectionDate) editInspectionDate.value = inspection.inspection_date;
                                if (editStatus) editStatus.value = inspection.status;
                                if (editApprovalNotes) editApprovalNotes.value = inspection.approval_notes || '';
                                if (editNotes) editNotes.value = '';

                                const modal = new bootstrap.Modal(this.getElement('editApprovedModal'));
                                modal?.show();
                            } else {
                                this.showError('Failed to load inspection data: ' + data.message);
                            }
                        } catch (error) {
                            console.error('Error loading inspection:', error);
                            this.showError('Error loading inspection data');
                        }
                    }

                    async submitEditApproved() {
                        const inspectionId = this.getElement('editInspectionId')?.value;
                        const inspectionDate = this.getElement('editInspectionDate')?.value;
                        const status = this.getElement('editStatus')?.value;
                        const editNotes = this.getElement('editNotes')?.value?.trim();
                        const approvalNotes = this.getElement('editApprovalNotes')?.value?.trim();

                        if (!editNotes) {
                            this.showError('Edit notes are required');
                            this.getElement('editNotes')?.focus();
                            return;
                        }

                        if (!inspectionDate) {
                            this.showError('Inspection date is required');
                            this.getElement('editInspectionDate')?.focus();
                            return;
                        }

                        const submitBtn = event.target;
                        const originalText = submitBtn.innerHTML;

                        try {
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                            submitBtn.disabled = true;

                            const response = await fetch(
                                `{{ url('summary-report/edit-approved') }}/${inspectionId}`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                            'content')
                                    },
                                    body: JSON.stringify({
                                        inspection_date: inspectionDate,
                                        status: status,
                                        edit_notes: editNotes,
                                        approval_notes: approvalNotes
                                    })
                                });

                            const data = await response.json();
                            if (data.success) {
                                this.showSuccess('Inspection updated successfully');
                                location.reload();
                            } else {
                                this.showError('Failed to update inspection: ' + data.message);
                            }
                        } catch (error) {
                            console.error('Error updating inspection:', error);
                            this.showError('Error updating inspection: ' + error.message);
                        } finally {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }

                        const modal = bootstrap.Modal.getInstance(this.getElement('editApprovedModal'));
                        modal?.hide();
                    }

                    async showBackDateModal(inspectionId) {
                        try {
                            const response = await fetch(
                                `{{ url('summary-report/get-inspection-history') }}/${inspectionId}`);
                            const data = await response.json();

                            if (data.success) {
                                const inspection = data.inspection;

                                const backDateInspectionId = this.getElement('backDateInspectionId');
                                const currentInspectionDate = this.getElement('currentInspectionDate');
                                const newInspectionDate = this.getElement('newInspectionDate');
                                const backDateReason = this.getElement('backDateReason');

                                if (backDateInspectionId) backDateInspectionId.value = inspectionId;
                                if (currentInspectionDate) currentInspectionDate.textContent = new Date(inspection
                                    .inspection_date).toLocaleDateString();
                                if (newInspectionDate) newInspectionDate.value = inspection.inspection_date;
                                if (backDateReason) backDateReason.value = '';

                                const modal = new bootstrap.Modal(this.getElement('backDateModal'));
                                modal?.show();
                            } else {
                                this.showError('Failed to load inspection data: ' + data.message);
                            }
                        } catch (error) {
                            console.error('Error loading inspection history:', error);
                            this.showError('Error loading inspection data');
                        }
                    }

                    async submitBackDate() {
                        const inspectionId = this.getElement('backDateInspectionId')?.value;
                        const newDate = this.getElement('newInspectionDate')?.value;
                        const reason = this.getElement('backDateReason')?.value?.trim();

                        if (!newDate) {
                            this.showError('New inspection date is required');
                            this.getElement('newInspectionDate')?.focus();
                            return;
                        }

                        const submitBtn = event.target;
                        const originalText = submitBtn.innerHTML;

                        try {
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                            submitBtn.disabled = true;

                            const response = await fetch(`{{ url('summary-report/back-date') }}/${inspectionId}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    new_inspection_date: newDate,
                                    change_reason: reason
                                })
                            });

                            const data = await response.json();
                            if (data.success) {
                                this.showSuccess('Inspection date updated successfully');
                                location.reload();
                            } else {
                                this.showError('Failed to update inspection date: ' + data.message);
                            }
                        } catch (error) {
                            console.error('Error updating inspection date:', error);
                            this.showError('Error updating inspection date: ' + error.message);
                        } finally {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }

                        const modal = bootstrap.Modal.getInstance(this.getElement('backDateModal'));
                        modal?.hide();
                    }
                } // Close the SummaryReportManager class

                // Initialize the application
                let summaryReportManager;

                document.addEventListener('DOMContentLoaded', function() {
                    summaryReportManager = new SummaryReportManager();
                });

                // Global wrapper functions for backward compatibility with HTML onclick events
                function loadAreas() {
                    summaryReportManager?.loadAreas();
                }

                function loadAreasWithEquipmentType() {
                    summaryReportManager?.loadAreasWithEquipmentType();
                }

                function submitFilter() {
                    summaryReportManager?.submitFilter();
                }

                function refreshData() {
                    summaryReportManager?.refreshData();
                }

                function exportPDF() {
                    summaryReportManager?.exportPDF();
                }

                function exportExcel() {
                    summaryReportManager?.exportExcel();
                }

                function approveInspection(inspectionId) {
                    summaryReportManager?.approveInspection(inspectionId);
                }

                function rejectInspection(inspectionId) {
                    summaryReportManager?.rejectInspection(inspectionId);
                }

                function submitApproval() {
                    summaryReportManager?.submitApproval();
                }

                function showApprovalNotes(notes) {
                    summaryReportManager?.showApprovalNotes(notes);
                }

                function toggleBulkActions() {
                    summaryReportManager?.toggleBulkActions();
                }

                function showBulkApprovalModal() {
                    summaryReportManager?.showBulkApprovalModal();
                }

                function showBulkRejectionModal() {
                    summaryReportManager?.showBulkRejectionModal();
                }

                function submitBulkApproval() {
                    summaryReportManager?.submitBulkApproval();
                }

                function submitBulkRejection() {
                    summaryReportManager?.submitBulkRejection();
                }

                function showNgDetails(element) {
                    summaryReportManager?.showNgDetails(element);
                }

                function editApprovedInspection(inspectionId) {
                    summaryReportManager?.editApprovedInspection(inspectionId);
                }

                function submitEditApproved() {
                    summaryReportManager?.submitEditApproved();
                }

                function showBackDateModal(inspectionId) {
                    summaryReportManager?.showBackDateModal(inspectionId);
                }

                function submitBackDate() {
                    summaryReportManager?.submitBackDate();
                }

                // Legacy DataTables variables for compatibility
                let equipmentSummaryTable;
                let inspectionsTable;

                function initializeDataTables() {
                    summaryReportManager?.initializeDataTables();
                }
            </script>
        @endpush

        @push('styles')
            <style>
                /* Fix DataTable header width */
                #equipmentSummaryTable_wrapper .dataTables_scroll .dataTables_scrollHead>div {
                    width: 100% !important;
                }

                /* Ensure proper table layout */
                #equipmentSummaryTable_wrapper .dataTables_scroll .dataTables_scrollHead table {
                    width: 100% !important;
                }

                /* Fix sorting for inspection date to handle month and day properly */
                .inspection-date-sort {
                    white-space: nowrap;
                }
            </style>
        @endpush
