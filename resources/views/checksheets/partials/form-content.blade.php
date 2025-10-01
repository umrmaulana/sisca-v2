<!-- Equipment Information -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h6 class="card-title mb-0">
                    <i class="fas fa-info-circle mr-2"></i>
                    Equipment Information
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>Code:</strong></td>
                        <td>{{ $equipment->equipment_code }}</td>
                    </tr>
                    <tr>
                        <td><strong>Type:</strong></td>
                        <td>{{ $equipment->equipmentType->equipment_name }} -
                            {{ $equipment->equipmentType->equipment_type }}</td>
                    </tr>
                    <tr>
                        <td><strong>Company:</strong></td>
                        <td>{{ $equipment->location->company->company_name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Area:</strong></td>
                        <td>{{ $equipment->location->area->area_name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Check Period:</strong></td>
                        <td>
                            @if ($equipment->periodCheck)
                                <span class="badge bg-info">{{ $equipment->periodCheck->period_check }}</span>
                            @else
                                <span class="badge bg-secondary">Not Set</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h6 class="card-title mb-0">
                    <i class="fas fa-calendar-check mr-2"></i>
                    Inspection Status
                </h6>
            </div>
            <div class="card-body">
                @if ($periodRestriction)
                    @if ($periodRestriction['can_inspect'])
                        @if ($periodRestriction['existing_inspection'] && $periodRestriction['has_ng_items'])
                            <div class="alert alert-warning mb-2">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Re-inspection Required</strong><br>
                                Previous inspection had NG items. You can re-inspect this equipment.
                            </div>
                        @else
                            <div class="alert alert-success mb-2">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Ready for Inspection</strong><br>
                                This equipment can be inspected now.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-danger mb-2">
                            <i class="fas fa-ban me-2"></i>
                            <strong>Cannot Inspect</strong><br>
                            {{ $periodRestriction['reason'] }}
                        </div>
                    @endif

                    @if ($periodRestriction['existing_inspection'])
                        <small class="text-muted">
                            <strong>Last Inspection:</strong>
                            {{ $periodRestriction['existing_inspection']->inspection_date->format('d/m/Y') }}
                            by {{ $periodRestriction['existing_inspection']->user->name }}
                        </small>
                    @endif
                @else
                    <div class="alert alert-success mb-2">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Ready for Inspection</strong><br>
                        This equipment can be inspected now.
                    </div>
                @endif
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>NPK:</strong></td>
                        <td>{{ Auth::guard()->user()->npk }}</td>
                    </tr>
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>{{ Auth::guard()->user()->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Role:</strong></td>
                        <td>{{ Auth::guard()->user()->role }}</td>
                    </tr>
                    <tr>
                        <td><strong>Date:</strong></td>
                        <td>{{ now()->format('d/m/Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@if (!$periodRestriction || $periodRestriction['can_inspect'])
    <!-- Checksheet Form -->
    <form action="{{ route('checksheets.store') }}" method="POST" enctype="multipart/form-data" id="checksheetForm">
        @csrf
        <input type="hidden" name="equipment_id" value="{{ $equipment->id }}">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-clipboard-list mr-2"></i>
                            Inspection Checklist
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach ($templates as $template)
                            <div class="inspection-item card mb-4 shadow-sm">
                                <div class="card-body">
                                    <div class="row g-4">
                                        <!-- Item Number & Standard Picture -->
                                        <div class="col-lg-3 col-md-6">
                                            <div class="text-center">
                                                <div class="mb-2">
                                                    <span class="badge bg-primary fs-6">Item
                                                        {{ $loop->iteration }}</span>
                                                </div>
                                                <label class="form-label fw-bold text-muted">Standard Picture</label>
                                                <div class="standard-picture-container mt-2">
                                                    @if ($template->equipment_type_id && $template->standar_picture)
                                                        <img src="{{ url('storage/' . $template->standar_picture) }}"
                                                            alt="Standard Picture"
                                                            class="img-thumbnail standard-picture"
                                                            onclick="showImageModal('{{ url('storage/' . $template->standar_picture) }}', '{{ $template->item_name }}')"
                                                            role="button" title="Click to enlarge"
                                                            style="width: 120px; height: 120px; object-fit: cover; cursor: pointer;">
                                                    @else
                                                        <div class="no-image-placeholder d-flex align-items-center justify-content-center"
                                                            style="width: 120px; height: 120px; background-color: #f8f9fa; border: 2px dashed #dee2e6;">
                                                            <i class="fas fa-image text-muted fa-2x"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Item Check Information -->
                                        <div class="col-lg-3 col-md-6">
                                            <label class="form-label fw-bold text-muted">Item Check</label>
                                            <div class="item-info">
                                                @if ($template->equipment_type_id)
                                                    <h6 class="text-primary mb-2 fw-bold">{{ $template->item_name }}
                                                    </h6>
                                                    <div class="condition-badge">
                                                        <span class="badge bg-info-soft text-info px-3 py-2">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            {{ $template->standar_condition }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Status Selection -->
                                        <div class="col-lg-3 col-md-6">
                                            <label class="form-label fw-bold text-muted">
                                                Status <span class="text-danger">*</span>
                                            </label>
                                            <div class="status-buttons mt-2">
                                                <div class="btn-group-vertical w-100" role="group">
                                                    <input type="radio" class="btn-check"
                                                        name="items[{{ $loop->index }}][status]" value="OK"
                                                        id="ok-{{ $loop->index }}" required>
                                                    <label class="btn btn-outline-success mb-2 py-2"
                                                        for="ok-{{ $loop->index }}">
                                                        <i class="fas fa-check-circle me-2"></i>OK
                                                    </label>

                                                    <input type="radio" class="btn-check"
                                                        name="items[{{ $loop->index }}][status]" value="NG"
                                                        id="ng-{{ $loop->index }}" required>
                                                    <label class="btn btn-outline-danger mb-2 py-2"
                                                        for="ng-{{ $loop->index }}">
                                                        <i class="fas fa-times-circle me-2"></i>NG
                                                    </label>

                                                    <input type="radio" class="btn-check"
                                                        name="items[{{ $loop->index }}][status]" value="NA"
                                                        id="na-{{ $loop->index }}" required>
                                                    <label class="btn btn-outline-secondary py-2"
                                                        for="na-{{ $loop->index }}">
                                                        <i class="fas fa-minus-circle me-2"></i>N/A
                                                    </label>
                                                </div>
                                            </div>
                                            <input type="hidden" name="items[{{ $loop->index }}][checksheet_id]"
                                                value="{{ $template->id }}">
                                        </div>

                                        <!-- Photo Upload -->
                                        <div class="col-lg-3 col-md-6">
                                            <label class="form-label fw-bold text-muted">
                                                Upload Photo <span class="text-danger">*</span>
                                            </label>
                                            <div class="upload-container mt-2">
                                                <div class="upload-area border border-2 border-dashed rounded p-3 text-center"
                                                    ondrop="handleDrop(event, {{ $loop->index }})"
                                                    ondragover="handleDragOver(event)"
                                                    onclick="document.getElementById('photo-{{ $loop->index }}').click()">
                                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                                    <p class="mb-2 text-muted small">
                                                        <strong>Click to upload</strong> or drag and drop
                                                    </p>
                                                    <p class="mb-0 text-muted small">
                                                        Max 10MB (JPEG, PNG, JPG)
                                                    </p>
                                                </div>
                                                <input type="file" class="form-control d-none"
                                                    id="photo-{{ $loop->index }}"
                                                    name="items[{{ $loop->index }}][picture]"
                                                    accept="image/jpeg,image/png,image/jpg"
                                                    onchange="previewImage(this, {{ $loop->index }})">
                                                <div id="preview-{{ $loop->index }}" class="mt-2 d-none">
                                                    <img id="preview-img-{{ $loop->index }}" class="img-thumbnail"
                                                        style="max-width: 100px; max-height: 100px;">
                                                    <button type="button" class="btn btn-sm btn-outline-danger mt-1"
                                                        onclick="removeImage({{ $loop->index }})">
                                                        <i class="fas fa-trash"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Notes Section -->
                        <div class="form-group mt-4">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                placeholder="Add any additional observations or notes..."></textarea>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="form-group mt-4 text-center">
                            <button type="button" class="btn btn-outline-danger me-2"
                                onclick="window.location.href='{{ route('checksheets.index') }}'">
                                <i class="fas fa-arrow-left me-2"></i>
                                Back to Scanner
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Submit Inspection
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@else
    <!-- Cannot Inspect - Show Previous Inspection -->
    @if ($periodRestriction['existing_inspection'])
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-history mr-2"></i>
                            Previous Inspection Results
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Inspection Date:</strong>
                                {{ $periodRestriction['existing_inspection']->inspection_date->format('d/m/Y') }}
                            </div>
                            <div class="col-md-6">
                                <strong>Inspector:</strong> {{ $periodRestriction['existing_inspection']->user->name }}
                            </div>
                        </div>

                        @if ($periodRestriction['existing_inspection']->notes)
                            <div class="mb-3">
                                <strong>Notes:</strong>
                                <p class="mt-1">{{ $periodRestriction['existing_inspection']->notes }}</p>
                            </div>
                        @endif

                        <div class="text-center">
                            <a href="{{ route('checksheets.show', $periodRestriction['existing_inspection']->id) }}"
                                class="btn btn-primary">
                                <i class="fas fa-eye me-2"></i>
                                View Full Inspection Report
                            </a>
                            <button type="button" class="btn btn-outline-danger ms-2"
                                onclick="window.location.href='{{ route('checksheets.index') }}'">
                                <i class="fas fa-arrow-left me-2"></i>
                                Back to Scanner
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif

@push('styles')
    <style>
        .inspection-item {
            transition: all 0.3s ease;
            border: 1px solid #e3e6f0;
        }

        .inspection-item:hover {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
            transform: translateY(-2px);
        }

        .standard-picture {
            transition: transform 0.2s ease;
            border-radius: 8px;
        }

        .standard-picture:hover {
            transform: scale(1.05);
        }

        .no-image-placeholder {
            border-radius: 8px;
        }

        .bg-info-soft {
            background-color: rgba(13, 202, 240, 0.1) !important;
        }

        .upload-area {
            transition: all 0.3s ease;
            background-color: #f8f9fa;
            min-height: 120px;
            cursor: pointer;
        }

        .upload-area:hover {
            background-color: #e9ecef;
            border-color: #0d6efd !important;
        }

        .upload-area.drag-over {
            background-color: rgba(13, 110, 253, 0.1);
            border-color: #0d6efd !important;
        }

        .status-buttons .btn {
            font-weight: 600;
            border-width: 2px;
        }

        .btn-check:checked+.btn-outline-success {
            background-color: #198754;
            border-color: #198754;
            color: white;
        }

        .btn-check:checked+.btn-outline-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
        }

        .btn-check:checked+.btn-outline-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
        }

        .inspection-item.has-error {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .form-label {
            font-size: 14px;
            margin-bottom: 8px;
        }

        .item-info h6 {
            line-height: 1.4;
        }

        .condition-badge .badge {
            font-size: 12px;
            padding: 6px 12px;
        }

        @media (max-width: 768px) {
            .inspection-item .row>div {
                margin-bottom: 1rem;
            }

            .status-buttons .btn-group-vertical {
                flex-direction: row;
            }

            .status-buttons .btn {
                flex: 1;
                margin-right: 5px;
                margin-bottom: 0;
            }

            .status-buttons .btn:last-child {
                margin-right: 0;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation
            const form = document.getElementById('checksheetForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    let allFieldsCompleted = true;
                    const inspectionItems = form.querySelectorAll('.inspection-item');

                    inspectionItems.forEach((item, index) => {
                        const radioButtons = item.querySelectorAll('input[type="radio"]');
                        const fileInput = item.querySelector('input[type="file"]');
                        const hasSelectedStatus = Array.from(radioButtons).some(radio => radio
                            .checked);
                        const hasUploadedFile = fileInput && fileInput.files.length > 0;

                        // Remove previous error state
                        item.classList.remove('has-error');

                        if (!hasSelectedStatus || !hasUploadedFile) {
                            allFieldsCompleted = false;
                            item.classList.add('has-error');

                            // Scroll to first error
                            if (allFieldsCompleted === false) {
                                item.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });
                            }
                        }
                    });

                    if (!allFieldsCompleted) {
                        e.preventDefault();
                        showAlert('error',
                            'Please complete all inspection items (status and photo) before submitting.'
                        );
                        return false;
                    }

                    // Confirm submission
                    if (!confirm(
                            'Are you sure you want to submit this inspection? This action cannot be undone.'
                        )) {
                        e.preventDefault();
                        return false;
                    }
                });

                // Remove error highlight when fields are completed
                form.querySelectorAll('input[type="radio"]').forEach(radio => {
                    radio.addEventListener('change', function() {
                        const item = this.closest('.inspection-item');
                        const fileInput = item.querySelector('input[type="file"]');

                        if (fileInput && fileInput.files.length > 0) {
                            item.classList.remove('has-error');
                        }
                    });
                });
            }
        });

        // Image preview function
        function previewImage(input, index) {
            const file = input.files[0];
            const previewContainer = document.getElementById(`preview-${index}`);
            const previewImg = document.getElementById(`preview-img-${index}`);
            const uploadArea = input.closest('.upload-container').querySelector('.upload-area');

            if (file) {
                // Validate file size (10MB)
                if (file.size > 10 * 1024 * 1024) {
                    showAlert('error', 'File size must be less than 10MB.');
                    input.value = '';
                    return;
                }

                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!allowedTypes.includes(file.type)) {
                    showAlert('error', 'Only JPEG, PNG, and JPG files are allowed.');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewContainer.classList.remove('d-none');
                    uploadArea.style.display = 'none';

                    // Remove error state
                    const item = input.closest('.inspection-item');
                    const hasSelectedStatus = item.querySelector('input[type="radio"]:checked');
                    if (hasSelectedStatus) {
                        item.classList.remove('has-error');
                    }
                };
                reader.readAsDataURL(file);
            }
        }

        // Remove image function
        function removeImage(index) {
            const input = document.getElementById(`photo-${index}`);
            const previewContainer = document.getElementById(`preview-${index}`);
            const uploadArea = input.closest('.upload-container').querySelector('.upload-area');

            input.value = '';
            previewContainer.classList.add('d-none');
            uploadArea.style.display = 'block';
        }

        // Drag and drop functions
        function handleDragOver(e) {
            e.preventDefault();
            e.currentTarget.classList.add('drag-over');
        }

        function handleDrop(e, index) {
            e.preventDefault();
            e.currentTarget.classList.remove('drag-over');

            const files = e.dataTransfer.files;
            const input = document.getElementById(`photo-${index}`);

            if (files.length > 0) {
                input.files = files;
                previewImage(input, index);
            }
        }

        // Remove drag-over class when leaving
        document.addEventListener('dragleave', function(e) {
            if (e.target.classList.contains('upload-area')) {
                e.target.classList.remove('drag-over');
            }
        });

        // Alert function
        function showAlert(type, message) {
            const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'check-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            // Remove existing alerts
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());

            // Add new alert to top of form
            const form = document.getElementById('checksheetForm');
            if (form) {
                form.insertAdjacentHTML('beforebegin', alertHtml);

                // Auto dismiss after 5 seconds
                setTimeout(() => {
                    const alert = document.querySelector('.alert');
                    if (alert) {
                        alert.remove();
                    }
                }, 5000);
            }
        }
    </script>
@endpush
