@extends('sisca-v2.layouts.app')

@section('title', 'Inspections')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Inspections Management</h3>
                        <button type="button" class="btn btn-primary" onclick="createInspection()">
                            <i class="fas fa-plus"></i> New Inspection
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Filter Form -->
                        <form id="filterForm" class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date">
                            </div>
                            <div class="col-md-3">
                                <label for="equipment_id" class="form-label">Equipment</label>
                                <select class="form-control" id="equipment_id" name="equipment_id">
                                    <option value="">All Equipment</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-secondary">Filter</button>
                            </div>
                        </form>

                        <!-- Inspections Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="inspectionsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Equipment</th>
                                        <th>Inspector</th>
                                        <th>Status</th>
                                        <th>NG Count</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav id="pagination" class="mt-3">
                            <!-- Pagination links will be loaded via AJAX -->
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="inspectionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Create Inspection</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="inspectionForm">
                    <div class="modal-body">
                        <input type="hidden" id="inspection_id" name="inspection_id">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="equipment_id_form" class="form-label">Equipment *</label>
                                    <select class="form-control" id="equipment_id_form" name="equipment_id" required>
                                        <option value="">Select Equipment</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="inspection_date" class="form-label">Inspection Date *</label>
                                    <input type="date" class="form-control" id="inspection_date" name="inspection_date"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <h6>Checksheet Items</h6>
                            <div id="checksheetItems">
                                <!-- Checksheet items will be loaded based on equipment selection -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Inspection Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewModalBody">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            loadInspections();
            loadEquipments();

            // Filter form submission
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                loadInspections();
            });

            // Equipment selection change
            $('#equipment_id_form').on('change', function() {
                loadChecksheetItems($(this).val());
            });
        });

        function loadInspections(page = 1) {
            const formData = $('#filterForm').serialize() + '&page=' + page;

            $.get("{{ route('sisca-v2.inspections.index') }}?" + formData)
                .done(function(response) {
                    if (response.success) {
                        renderInspectionsTable(response.data.data);
                        renderPagination(response.data);
                    }
                })
                .fail(function() {
                    alert('Failed to load inspections');
                });
        }

        function loadEquipments() {
            $.get("{{ route('sisca-v2.api.equipments') }}")
                .done(function(equipments) {
                    const options = '<option value="">Select Equipment</option>' +
                        equipments.map(eq => `<option value="${eq.id}">${eq.equipment_code} - ${eq.desc}</option>`)
                        .join('');
                    $('#equipment_id, #equipment_id_form').html(options);
                });
        }

        function loadChecksheetItems(equipmentId) {
            if (!equipmentId) {
                $('#checksheetItems').html('');
                return;
            }

            // This would need an API endpoint to get checksheet templates by equipment
            // For now, we'll use a placeholder
            $('#checksheetItems').html('<p>Loading checksheet items...</p>');
        }

        function renderInspectionsTable(inspections) {
            const tbody = inspections.map(inspection => {
                const ngCount = inspection.details.filter(d => d.statum === 'NG').length;
                const statusBadge = getStatusBadge(inspection.status);

                return `
            <tr>
                <td>${inspection.id}</td>
                <td>${inspection.inspection_date}</td>
                <td>${inspection.equipment?.equipment_code || '-'}</td>
                <td>${inspection.user?.name || '-'}</td>
                <td>${statusBadge}</td>
                <td>${ngCount}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="viewInspection(${inspection.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${inspection.status !== 'approved' ? `
                            <button class="btn btn-sm btn-warning" onclick="editInspection(${inspection.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteInspection(${inspection.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        ` : ''}
                </td>
            </tr>
        `;
            }).join('');

            $('#inspectionsTable tbody').html(tbody);
        }

        function getStatusBadge(status) {
            const badges = {
                'pending': '<span class="badge bg-warning">Pending</span>',
                'approved': '<span class="badge bg-success">Approved</span>',
                'rejected': '<span class="badge bg-danger">Rejected</span>'
            };
            return badges[status] || status;
        }

        function renderPagination(data) {
            // Implementation for pagination rendering
            $('#pagination').html('<!-- Pagination implementation -->');
        }

        function createInspection() {
            $('#modalTitle').text('Create Inspection');
            $('#inspectionForm')[0].reset();
            $('#inspection_id').val('');
            $('#checksheetItems').html('');
            $('#inspectionModal').modal('show');
        }

        function editInspection(id) {
            // Implementation for editing inspection
            console.log('Edit inspection:', id);
        }

        function viewInspection(id) {
            // Implementation for viewing inspection details
            console.log('View inspection:', id);
        }

        function deleteInspection(id) {
            if (confirm('Are you sure you want to delete this inspection?')) {
                $.ajax({
                    url: "{{ route('sisca-v2.inspections.index') }}/" + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Inspection deleted successfully');
                            loadInspections();
                        }
                    },
                    error: function() {
                        alert('Failed to delete inspection');
                    }
                });
            }
        }
    </script>
@endpush
