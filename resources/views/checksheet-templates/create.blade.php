@extends('layouts.app')

@section('title', 'Create Checksheet Template')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Create Checksheet Template</h3>
                <p class="text-muted mb-0">Add a new checksheet template for equipment inspection</p>
            </div>
            <a href="{{ route('checksheet-templates.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Templates
            </a>
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

                <form action="{{ route('checksheet-templates.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

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
                                        {{ old('equipment_type_id') == $equipmentType->id ? 'selected' : '' }}>
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
                            <label for="order_number" class="form-label">Order Number</label>
                            <input type="number" class="form-control @error('order_number') is-invalid @enderror"
                                id="order_number" name="order_number" value="{{ old('order_number') }}" min="1"
                                placeholder="Auto-filled when equipment type selected">
                            @error('order_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Order number will be auto-filled when you select an equipment type. Leave
                                empty for automatic ordering.</div>
                        </div>
                    </div>

                    <!-- Item Name -->
                    <div class="mb-3">
                        <label for="item_name" class="form-label">Item Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('item_name') is-invalid @enderror" id="item_name"
                            name="item_name" value="{{ old('item_name') }}"
                            placeholder="e.g., Pressure Gauge, Safety Pin, etc." required>
                        @error('item_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Standard Condition -->
                    <div class="mb-3">
                        <label for="standar_condition" class="form-label">Standard Condition</label>
                        <textarea class="form-control @error('standar_condition') is-invalid @enderror" id="standar_condition"
                            name="standar_condition" rows="3" placeholder="Describe the standard condition for this inspection item">{{ old('standar_condition') }}</textarea>
                        @error('standar_condition')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Standard Picture -->
                    <div class="mb-3">
                        <label for="standar_picture" class="form-label">Standard Picture</label>
                        <input type="file" class="form-control @error('standar_picture') is-invalid @enderror"
                            id="standar_picture" name="standar_picture" accept="image/*">
                        @error('standar_picture')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Upload an image file (JPEG, PNG, JPG, GIF). Maximum size: 5MB</div>

                        <!-- Image Preview -->
                        <div id="image-preview" class="mt-2" style="display: none;">
                            <img id="preview-img" src="#" alt="Preview" class="img-thumbnail"
                                style="max-width: 200px; max-height: 200px;">
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active Template
                            </label>
                        </div>
                        <div class="form-text">Inactive templates will not be available for use in checksheets</div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('checksheet-templates.index') }}" class="btn btn-outline-danger">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Template
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                            // Always fetch next order number to show suggestion
                            fetch(
                                    `{{ route('checksheet-templates.get-next-order', '') }}/${equipmentTypeId}`
                                )
                                .then(response => response.json())
                                .then(data => {
                                    if (data.next_order) {
                                        // Only auto-fill if field is empty
                                        if (!orderNumberInput.value) {
                                            orderNumberInput.value = data.next_order;
                                        }
                                        // Update placeholder to show next available number
                                        orderNumberInput.placeholder = `Next available: ${data.next_order}`;
                                    }
                                })
                                .catch(error => {
                                    console.error('Error fetching next order number:', error);
                                });
                        } else {
                            orderNumberInput.placeholder = 'Auto-filled when equipment type selected';
                        }
                    });

                    // Add form submit handler to auto-assign order if empty
                    const form = equipmentTypeSelect.closest('form');
                    if (form) {
                        form.addEventListener('submit', function(e) {
                            const equipmentTypeId = equipmentTypeSelect.value;

                            // If order number is empty and equipment type is selected, 
                            // let the backend handle auto-assignment
                            if (!orderNumberInput.value && equipmentTypeId) {
                                // Remove any validation errors for order number
                                const errorDiv = orderNumberInput.parentNode.querySelector('.invalid-feedback');
                                if (errorDiv) {
                                    errorDiv.style.display = 'none';
                                }
                                orderNumberInput.classList.remove('is-invalid');
                            }
                        });
                    }
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
