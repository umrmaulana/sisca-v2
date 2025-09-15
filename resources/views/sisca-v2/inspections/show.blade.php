@extends('sisca-v2.layouts.app')

@section('title', 'Inspection Details')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Inspection Details</h3>
                <p class="text-muted mb-0">{{ $inspection->equipment->equipment_code }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Inspection Information -->
            <div class="col-lg-8">
                <!-- General Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>Inspection Information
                            </h5>
                            @if ($inspection->status === 'approved')
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-check-circle me-1"></i>Approved
                                </span>
                            @elseif ($inspection->status === 'pending')
                                <span class="badge bg-warning fs-6">
                                    <i class="fas fa-clock me-1"></i>Pending Approval
                                </span>
                            @elseif ($inspection->status === 'rejected')
                                <span class="badge bg-danger fs-6">
                                    <i class="fas fa-times-circle me-1"></i>Rejected
                                </span>
                            @else
                                <span class="badge bg-secondary fs-6">
                                    {{ ucfirst($inspection->status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted fw-bold">Inspection Date</label>
                                <div>
                                    <h6 class="mb-1">{{ $inspection->inspection_date->format('d F Y') }}</h6>
                                    <small class="text-muted">{{ $inspection->inspection_date->format('l') }}</small>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted fw-bold">Inspector</label>
                                <div>
                                    <h6 class="mb-1">{{ $inspection->user->name ?? 'N/A' }}</h6>
                                    @if ($inspection->user && $inspection->user->npk)
                                        <small class="text-muted">NPK: {{ $inspection->user->npk }}</small>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted fw-bold">Equipment</label>
                                <div>
                                    <h6 class="mb-1">{{ $inspection->equipment->equipment_code }}</h6>
                                    <small class="text-muted">{{ $inspection->equipment->desc ?? 'No description' }}</small>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted fw-bold">Equipment Type</label>
                                <div>
                                    <span
                                        class="badge bg-info">{{ $inspection->equipment->equipmentType->equipment_name ?? 'N/A' }}</span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted fw-bold">Location</label>
                                <div>
                                    <h6 class="mb-1">{{ $inspection->equipment->location->location_code ?? 'N/A' }}</h6>
                                    @if ($inspection->equipment->location && $inspection->equipment->location->area)
                                        <small class="text-muted d-block">Area:
                                            {{ $inspection->equipment->location->area->area_name }}</small>
                                    @endif
                                    @if (
                                        $inspection->equipment->location &&
                                            $inspection->equipment->location->area &&
                                            $inspection->equipment->location->area->plant)
                                        <small class="text-muted d-block">Plant:
                                            {{ $inspection->equipment->location->area->plant->plant_name }}</small>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted fw-bold">Inspection Time</label>
                                <div>
                                    <h6 class="mb-1">{{ $inspection->created_at->format('H:i:s') }}</h6>
                                    <small class="text-muted">{{ $inspection->created_at->diffForHumans() }}</small>
                                </div>
                            </div>

                            @if ($inspection->notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label text-muted fw-bold">Notes</label>
                                    <div class="border p-3 rounded">
                                        <p class="mb-0">{{ $inspection->notes }}</p>
                                    </div>
                                </div>
                            @endif

                            @if ($inspection->approvedBy)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted fw-bold">Approved By</label>
                                    <div>
                                        <h6 class="mb-1">{{ $inspection->approvedBy->name }}</h6>
                                        <small
                                            class="text-muted">{{ $inspection->approved_at->format('d M Y H:i') }}</small>
                                    </div>
                                </div>
                            @endif

                            @if ($inspection->approval_notes)
                                <div class="col-12 mb-3">
                                    <label class="form-label text-muted fw-bold">Approval Notes</label>
                                    <div class="border p-3 rounded">
                                        <p class="mb-0">{{ $inspection->approval_notes }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Inspection Details -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list-check me-2"></i>Inspection Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle animation-in">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Checksheet Item</th>
                                        <th>Status</th>
                                        <th>Picture</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($inspection->details as $index => $detail)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div>
                                                    <strong>{{ $detail->checksheetTemplate->item_name ?? 'N/A' }}</strong>
                                                    @if ($detail->checksheetTemplate->standar_condition)
                                                        <br><small class="text-muted">Standard:
                                                            {{ $detail->checksheetTemplate->standar_condition }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if ($detail->status === 'OK')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>OK
                                                    </span>
                                                @elseif ($detail->status === 'NG')
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times me-1"></i>NG
                                                    </span>
                                                @elseif ($detail->status === 'NA')
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-minus me-1"></i>N/A
                                                    </span>
                                                @else
                                                    <span class="badge bg-dark">{{ $detail->status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($detail->picture && Storage::disk('public')->exists($detail->picture))
                                                    <a href="{{ asset('storage/' . $detail->picture) }}" target="_blank">
                                                        <img src="{{ asset('storage/' . $detail->picture) }}"
                                                            alt="Inspection Picture" class="img-thumbnail"
                                                            style="max-width: 100px; max-height: 80px; object-fit: cover;">
                                                    </a>
                                                @else
                                                    <span class="text-muted">No image</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                                    <p class="mb-0">No inspection details found</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Information -->
            <div class="col-lg-4">
                <!-- Inspection Summary -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Inspection Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $totalItems = $inspection->details->count();
                            $okItems = $inspection->details->where('status', 'OK')->count();
                            $ngItems = $inspection->details->where('status', 'NG')->count();
                            $naItems = $inspection->details->where('status', 'NA')->count();
                            $completionRate = $totalItems > 0 ? round(($okItems / $totalItems) * 100, 1) : 0;
                        @endphp

                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="border-end">
                                    <h4 class="text-primary mb-1">{{ $totalItems }}</h4>
                                    <small class="text-muted">Total Items</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <h4 class="text-success mb-1">{{ $okItems }}</h4>
                                <small class="text-muted">OK Items</small>
                            </div>
                        </div>

                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="border-end">
                                    <h4 class="text-danger mb-1">{{ $ngItems }}</h4>
                                    <small class="text-muted">NG Items</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <h4 class="text-secondary mb-1">{{ $naItems }}</h4>
                                <small class="text-muted">N/A Items</small>
                            </div>
                        </div>

                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: {{ $completionRate }}%" aria-valuenow="{{ $completionRate }}"
                                aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <div class="text-center">
                            <small class="text-muted">Completion Rate: {{ $completionRate }}%</small>
                        </div>
                    </div>
                </div>

                <!-- Equipment Quick Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog me-2"></i>Equipment Info
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Equipment Code:</strong><br>
                            <span class="text-primary">{{ $inspection->equipment->equipment_code }}</span>
                        </div>

                        @if ($inspection->equipment->desc)
                            <div class="mb-3">
                                <strong>Description:</strong><br>
                                <span class="text-muted">{{ $inspection->equipment->desc }}</span>
                            </div>
                        @endif

                        <div class="mb-3">
                            <strong>Equipment Type:</strong><br>
                            <span
                                class="badge bg-info">{{ $inspection->equipment->equipmentType->equipment_name ?? 'N/A' }}</span>
                        </div>

                        <div class="d-grid">
                            <a href="{{ route('sisca-v2.equipments.show', $inspection->equipment->id) }}"
                                class="btn btn-outline-primary">
                                <i class="fas fa-eye me-1"></i>View Equipment Details
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                @if (in_array(auth('sisca-v2')->user()->role, ['Supervisor', 'Management', 'Admin']) &&
                        $inspection->status === 'pending')
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-tasks me-2"></i>Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-success"
                                    onclick="approveInspection({{ $inspection->id }})">
                                    <i class="fas fa-check me-1"></i>Approve Inspection
                                </button>
                                <button type="button" class="btn btn-danger"
                                    onclick="rejectInspection({{ $inspection->id }})">
                                    <i class="fas fa-times me-1"></i>Reject Inspection
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
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
                            <textarea class="form-control" id="approval_notes" rows="3" placeholder="Enter approval/rejection notes"></textarea>
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

    <script>
        // Approve inspection
        function approveInspection(inspectionId) {
            document.getElementById('inspectionId').value = inspectionId;
            document.getElementById('approvalAction').value = 'approve';
            document.getElementById('approvalModalTitle').textContent = 'Approve Inspection';
            document.getElementById('approval_notes').placeholder = 'Enter approval notes (optional)';
            document.getElementById('approval_notes').required = false;

            const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
            modal.show();
        }

        // Reject inspection
        function rejectInspection(inspectionId) {
            document.getElementById('inspectionId').value = inspectionId;
            document.getElementById('approvalAction').value = 'reject';
            document.getElementById('approvalModalTitle').textContent = 'Reject Inspection';
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
    </script>
@endsection
