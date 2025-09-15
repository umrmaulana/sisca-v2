@extends('sisca-v2.layouts.app')

@section('title', 'Edit Checksheet Template')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Edit Checksheet Template</h3>
                <p class="text-muted mb-0">Update template information for {{ $checksheetTemplate->item_name }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('sisca-v2.checksheet-templates.show', $checksheetTemplate) }}" class="btn btn-info">
                    <i class="fas fa-eye me-2"></i>View
                </a>
                <a href="{{ route('sisca-v2.checksheet-templates.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Templates
                </a>
            </div>
        </div>

        <!-- Main Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Template Information</h5>
            </div>
            <div class="card-body">
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('sisca-v2.checksheet-templates.update', $checksheetTemplate) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Equipment Type -->
                        <div class="col-md-6 mb-3">
                            <label for="equipment_type_id" class="form-label">Equipment Type <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('equipment_type_id') is-invalid @enderror"
                                id="equipment_type_id" name="equipment_type_id" required>
                                <option value="">Select Equipment Type</option>
                                @foreach ($equipmentTypes as $equipmentType)
                                    <option value="{{ $equipmentType->id }}"
                                        {{ old('equipment_type_id', $checksheetTemplate->equipment_type_id) == $equipmentType->id ? 'selected' : '' }}>
                                        {{ $equipmentType->equipment_name }} - {{ $equipmentType->equipment_type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('equipment_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Order Number -->
                        <div class="col-md-6 mb-3">
                            <label for="order_number" class="form-label">Order Number <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('order_number') is-invalid @enderror"
                                id="order_number" name="order_number"
                                value="{{ old('order_number', $checksheetTemplate->order_number) }}" min="1"
                                required placeholder="Order number for this item">
                            @error('order_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Order number will be updated when you change equipment type. You can also
                                set it manually.</div>
                        </div>
                    </div>

                    <!-- Item Name -->
                    <div class="mb-3">
                        <label for="item_name" class="form-label">Item Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('item_name') is-invalid @enderror" id="item_name"
                            name="item_name" value="{{ old('item_name', $checksheetTemplate->item_name) }}"
                            placeholder="e.g., Pressure Gauge, Safety Pin, etc." required>
                        @error('item_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Standard Condition -->
                    <div class="mb-3">
                        <label for="standar_condition" class="form-label">Standard Condition</label>
                        <textarea class="form-control @error('standar_condition') is-invalid @enderror" id="standar_condition"
                            name="standar_condition" rows="3" placeholder="Describe the standard condition for this inspection item">{{ old('standar_condition', $checksheetTemplate->standar_condition) }}</textarea>
                        @error('standar_condition')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Current Standard Picture -->
                    @if ($checksheetTemplate->standar_picture)
                        <div class="mb-3">
                            <label class="form-label">Current Standard Picture</label>
                            <div class="d-flex align-items-start">
                                <img src="{{ asset('storage/' . $checksheetTemplate->standar_picture) }}"
                                    alt="Current Standard Picture" class="img-thumbnail me-3"
                                    style="width: 100px; height: 100px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2">Current image will be replaced if you upload a new one</p>
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                        data-bs-target="#currentImageModal">
                                        <i class="fas fa-eye me-1"></i>View Full Size
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Standard Picture -->
                    <div class="mb-3">
                        <label for="standar_picture" class="form-label">
                            {{ $checksheetTemplate->standar_picture ? 'Update Standard Picture' : 'Standard Picture' }}
                        </label>
                        <input type="file" class="form-control @error('standar_picture') is-invalid @enderror"
                            id="standar_picture" name="standar_picture" accept="image/*">
                        @error('standar_picture')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            {{ $checksheetTemplate->standar_picture ? 'Leave empty to keep current image. ' : '' }}Upload
                            an
                            image file (JPEG, PNG, JPG, GIF). Maximum size: 5MB
                        </div>

                        <!-- Image Preview -->
                        <div id="image-preview" class="mt-2" style="display: none;">
                            <img id="preview-img" src="#" alt="Preview" class="img-thumbnail"
                                style="max-width: 200px; max-height: 200px;">
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                value="1" {{ old('is_active', $checksheetTemplate->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active Template
                            </label>
                        </div>
                        <div class="form-text">Inactive templates will not be available for use in checksheets</div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('sisca-v2.checksheet-templates.show', $checksheetTemplate) }}"
                            class="btn btn-outline-danger">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Template
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Template Info Card -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Template Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="text-muted mb-2">Created</h6>
                        <div>{{ $checksheetTemplate->created_at->format('d M Y, H:i') }}</div>
                        @if ($checksheetTemplate->creator)
                            <small class="text-muted">by {{ $checksheetTemplate->creator->name }}</small>
                        @endif
                    </div>

                    @if ($checksheetTemplate->updated_at != $checksheetTemplate->created_at)
                        <div class="col-md-4">
                            <h6 class="text-muted mb-2">Last Updated</h6>
                            <div>{{ $checksheetTemplate->updated_at->format('d M Y, H:i') }}</div>
                            @if ($checksheetTemplate->updater)
                                <small class="text-muted">by {{ $checksheetTemplate->updater->name }}</small>
                            @endif
                        </div>
                    @endif

                    <div class="col-md-4">
                        <h6 class="text-muted mb-2">Template ID</h6>
                        <code>{{ $checksheetTemplate->id }}</code>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Image Modal -->
    @if ($checksheetTemplate->standar_picture)
        <div class="modal fade" id="currentImageModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Current Standard Picture</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ asset('storage/' . $checksheetTemplate->standar_picture) }}"
                            alt="Current Standard Picture" class="img-fluid rounded">
                    </div>
                    <div class="modal-footer">
                        <a href="{{ asset('storage/' . $checksheetTemplate->standar_picture) }}" target="_blank"
                            class="btn btn-primary">
                            <i class="fas fa-external-link-alt me-2"></i>Open Full Size
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Auto-fill order number when equipment type changes
                const equipmentTypeSelect = document.getElementById('equipment_type_id');
                const orderNumberInput = document.getElementById('order_number');

                if (equipmentTypeSelect && orderNumberInput) {
                    equipmentTypeSelect.addEventListener('change', function() {
                        const equipmentTypeId = this.value;

                        if (equipmentTypeId) {
                            // Fetch next order number to show suggestion
                            fetch(
                                    `{{ route('sisca-v2.checksheet-templates.get-next-order', '') }}/${equipmentTypeId}`
                                )
                                .then(response => response.json())
                                .then(data => {
                                    if (data.next_order) {
                                        // Update placeholder to show next available number
                                        orderNumberInput.placeholder = `Next available: ${data.next_order}`;
                                    }
                                })
                                .catch(error => {
                                    console.error('Error fetching next order number:', error);
                                });
                        } else {
                            orderNumberInput.placeholder = 'Order number for this item';
                        }
                    });
                }

                // Image preview
                const fileInput = document.getElementById('standar_picture');
                const imagePreview = document.getElementById('image-preview');
                const previewImg = document.getElementById('preview-img');

                if (fileInput && imagePreview && previewImg) {
                    fileInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];

                        if (file) {
                            // Check file size (5MB = 5 * 1024 * 1024 bytes)
                            if (file.size > 5 * 1024 * 1024) {
                                alert('File size must be less than 5MB');
                                this.value = '';
                                imagePreview.style.display = 'none';
                                return;
                            }

                            // Check file type
                            if (!file.type.match('image.*')) {
                                alert('Please select an image file');
                                this.value = '';
                                imagePreview.style.display = 'none';
                                return;
                            }

                            const reader = new FileReader();
                            reader.onload = function(e) {
                                previewImg.src = e.target.result;
                                imagePreview.style.display = 'block';
                            };
                            reader.readAsDataURL(file);
                        } else {
                            imagePreview.style.display = 'none';
                        }
                    });
                }

                // Auto dismiss alerts
                setTimeout(function() {
                    const alerts = document.querySelectorAll('.alert');
                    alerts.forEach(function(alert) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    });
                }, 5000);
            });
        </script>
    @endpush

    @push('styles')
        <style>
            /* Form styling consistency with equipment types */
            .form-control:focus,
            .form-select:focus {
                border-color: var(--primary-color);
                box-shadow: 0 0 0 0.2rem rgba(30, 60, 114, 0.25);
            }

            .form-text {
                font-size: 0.875rem;
                color: #6c757d;
            }

            .img-thumbnail {
                border: 1px solid #dee2e6;
                border-radius: 0.375rem;
                padding: 0.25rem;
            }

            /* Responsive form */
            @media (max-width: 768px) {
                .container-fluid {
                    padding-left: 1rem;
                    padding-right: 1rem;
                }

                .d-flex.justify-content-end {
                    flex-direction: column;
                    gap: 0.5rem;
                }

                .d-flex.justify-content-end .btn {
                    width: 100%;
                }
            }
        </style>
    @endpush
@endsection
