@extends('sisca-v2.layouts.app')

@section('title', 'Add New Equipment')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Add New Equipment</h3>
                <p class="text-muted mb-0">Create a new equipment with automatic QR code generation</p>
            </div>
            <a href="{{ route('sisca-v2.equipments.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6><i class="fas fa-exclamation-circle me-2"></i>Please correct the following errors:</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-tools me-2"></i>Equipment Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('sisca-v2.equipments.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="equipment_code" class="form-label">
                                        Equipment Code <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('equipment_code') is-invalid @enderror"
                                        id="equipment_code" name="equipment_code" value="{{ old('equipment_code') }}"
                                        required maxlength="20" placeholder="Enter equipment code">
                                    @error('equipment_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Maximum 20 characters, must be unique</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="equipment_type_id" class="form-label">
                                        Equipment Type <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('equipment_type_id') is-invalid @enderror"
                                        id="equipment_type_id" name="equipment_type_id" required>
                                        <option value="">Select Equipment Type</option>
                                        @foreach ($equipmentTypes as $type)
                                            <option value="{{ $type->id }}"
                                                {{ old('equipment_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->equipment_name }} ({{ $type->equipment_type }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('equipment_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="company_id" class="form-label">
                                        Company <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('company_id') is-invalid @enderror" id="company_id"
                                        name="company_id" required>
                                        <option value="">Select Company</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}"
                                                {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                                {{ $company->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="area_id" class="form-label">
                                        Area <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('area_id') is-invalid @enderror" id="area_id"
                                        name="area_id" required>
                                        <option value="">Select Company First</option>
                                    </select>
                                    @error('area_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="location_id" class="form-label">
                                        Location <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('location_id') is-invalid @enderror" id="location_id"
                                        name="location_id" required>
                                        <option value="">Select Area First</option>
                                    </select>
                                    @error('location_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="period_check_id" class="form-label">Period Check</label>
                                    <select class="form-select @error('period_check_id') is-invalid @enderror"
                                        id="period_check_id" name="period_check_id">
                                        <option value="">Select Period Check</option>
                                        @foreach ($periodChecks as $period)
                                            <option value="{{ $period->id }}"
                                                {{ old('period_check_id') == $period->id ? 'selected' : '' }}>
                                                {{ $period->period_check }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('period_check_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Optional: Set maintenance period for this equipment</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="expired_date" class="form-label">
                                        Expired Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control @error('expired_date') is-invalid @enderror"
                                        id="expired_date" name="expired_date" value="{{ old('expired_date') }}" required
                                        maxlength="20" placeholder="Enter expired date">
                                    @error('expired_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-4">
                                    <div class="form-check form-switch">
                                        <!-- Hidden input to ensure we always get a value -->
                                        <input type="hidden" name="is_active" value="0">
                                        <input class="form-check-input @error('is_active') is-invalid @enderror"
                                            type="checkbox" id="is_active" name="is_active" value="1"
                                            {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            <strong>Active Status</strong>
                                        </label>
                                        @error('is_active')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Toggle to activate or deactivate this equipment
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('sisca-v2.equipments.index') }}"
                                            class="btn btn-outline-danger">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Create Equipment
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-qrcode me-2"></i>QR Code Generation
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="bg-light rounded p-4 mb-3">
                                <i class="fas fa-qrcode fa-4x text-muted mb-2"></i>
                                <p class="text-muted mb-0">QR Code will be generated automatically</p>
                            </div>
                        </div>

                        <div class="alert alert-info" role="alert">
                            <h6><i class="fas fa-info-circle me-2"></i>QR Code Features:</h6>
                            <ul class="mb-0 small">
                                <li>Generated automatically based on equipment code</li>
                                <li>Saved to: <code>storage/app/public/sisca-v2/qrcode/</code></li>
                                <li>Format: PNG (300x300 pixels)</li>
                                <li>Can be downloaded and printed</li>
                            </ul>
                        </div>

                        <div class="alert alert-warning" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Note:</strong> Equipment code must be unique. QR code will contain this code for
                            equipment identification.
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-lightbulb me-2"></i>Instructions
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Equipment code must be unique and maximum 20 characters
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Select appropriate equipment type and location
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Period check is optional but recommended for maintenance scheduling
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Set status to Active for operational equipment
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-gear me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('sisca-v2.equipment-types.index') }}"
                                class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-plus me-1"></i>Manage Equipment Types
                            </a>
                            <a href="{{ route('sisca-v2.locations.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-geo-alt me-1"></i>Manage Locations
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            // Cascade dropdown functionality
            document.getElementById('company_id').addEventListener('change', function() {
                const companyId = this.value;
                const areaSelect = document.getElementById('area_id');
                const locationSelect = document.getElementById('location_id');

                // Reset area and location
                areaSelect.innerHTML = '<option value="">Loading areas...</option>';
                locationSelect.innerHTML = '<option value="">Select Area First</option>';

                if (companyId) {
                    fetch(`${window.location.origin}/sisca-v2/equipments/areas-by-company?company_id=${companyId}`)
                        .then(response => response.json())
                        .then(data => {
                            areaSelect.innerHTML = '<option value="">Select Area</option>';

                            // Handle different response formats - server returns {areas: [...]}
                            let areas = [];
                            if (Array.isArray(data)) {
                                areas = data;
                            } else if (data && Array.isArray(data.areas)) {
                                areas = data.areas;
                            }

                            areas.forEach(area => {
                                areaSelect.innerHTML +=
                                    `<option value="${area.id}">${area.area_name}</option>`;
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            areaSelect.innerHTML = '<option value="">Error loading areas</option>';
                        });
                } else {
                    areaSelect.innerHTML = '<option value="">Select Company First</option>';
                }
            });

            document.getElementById('area_id').addEventListener('change', function() {
                const areaId = this.value;
                const locationSelect = document.getElementById('location_id');

                locationSelect.innerHTML = '<option value="">Loading locations...</option>';

                if (areaId) {
                    fetch(`${window.location.origin}/sisca-v2/equipments/locations-by-area?area_id=${areaId}`)
                        .then(response => response.json())
                        .then(locations => {
                            locationSelect.innerHTML = '<option value="">Select Location</option>';
                            locations.forEach(location => {
                                locationSelect.innerHTML +=
                                    `<option value="${location.id}">${location.location_code}</option>`;
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            locationSelect.innerHTML = '<option value="">Error loading locations</option>';
                        });
                } else {
                    locationSelect.innerHTML = '<option value="">Select Area First</option>';
                }
            });

            // Auto-generate equipment code based on type selection (optional)
            document.getElementById('equipment_type_id').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value && !document.getElementById('equipment_code').value) {
                    // Generate suggestion based on equipment type
                    const typeText = selectedOption.text;
                    const typeCode = typeText.substring(0, 3).toUpperCase();
                    const timestamp = Date.now().toString().slice(-4);
                    document.getElementById('equipment_code').value = typeCode + timestamp;
                }
            });

            // QR Code generator placeholder
            document.getElementById('qrcode').addEventListener('focus', function() {
                if (!this.value && document.getElementById('equipment_code').value) {
                    this.value = 'QR-' + document.getElementById('equipment_code').value;
                }
            });
        </script>
    @endpush
