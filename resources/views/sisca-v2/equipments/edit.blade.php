@extends('sisca-v2.layouts.app')

@section('title', 'Edit Equipment')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Edit Equipment</h3>
                <p class="text-muted mb-0">Update equipment information: {{ $equipment->equipment_code }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('sisca-v2.equipments.show', $equipment) }}" class="btn btn-outline-info">
                    <i class="fas fa-eye me-1"></i>View Details
                </a>
                <a href="{{ route('sisca-v2.equipments.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to List
                </a>
            </div>
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
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Equipment Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('sisca-v2.equipments.update', $equipment) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="equipment_code" class="form-label">
                                        Equipment Code <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('equipment_code') is-invalid @enderror"
                                        id="equipment_code" name="equipment_code"
                                        value="{{ old('equipment_code', $equipment->equipment_code) }}" required
                                        maxlength="20" placeholder="Enter equipment code">
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
                                                {{ old('equipment_type_id', $equipment->equipment_type_id) == $type->id ? 'selected' : '' }}>
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
                                                {{ old('company_id', $equipment->location->company_id ?? '') == $company->id ? 'selected' : '' }}>
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
                                        <option value="">Loading areas...</option>
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
                                        <option value="">Loading locations...</option>
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
                                                {{ old('period_check_id', $equipment->period_check_id) == $period->id ? 'selected' : '' }}>
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
                                        id="expired_date" name="expired_date"
                                        value="{{ old('expired_date', $equipment->expired_date ? $equipment->expired_date->format('Y-m-d') : '') }}"
                                        required maxlength="20" placeholder="Enter expired date">
                                    @error('expired_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-6 mt-4">
                                    <div class="form-check form-switch">
                                        <!-- Hidden input to ensure we always get a value -->
                                        <input type="hidden" name="is_active" value="0">
                                        <input class="form-check-input @error('is_active') is-invalid @enderror"
                                            type="checkbox" id="is_active" name="is_active" value="1"
                                            {{ old('is_active', $equipment->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            <strong>Active Status</strong>
                                        </label>
                                        @error('is_active')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Toggle to activate or deactivate this equipment</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('sisca-v2.equipments.show', $equipment) }}"
                                            class="btn btn-outline-danger">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Update Equipment
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Current QR Code -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-qrcode me-2"></i>Current QR Code
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        @if ($equipment->qrcode && \Storage::disk('public')->exists($equipment->qrcode))
                            <div class="mb-3">
                                <img src="{{ url('storage/' . $equipment->qrcode) }}" alt="QR Code"
                                    class="img-fluid border rounded" style="max-width: 200px;">
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Equipment Code: {{ $equipment->equipment_code }}</small>
                                @if ($equipment->location && $equipment->location->company && $equipment->location->area)
                                    <small class="text-muted d-block">{{ $equipment->location->company->company_name }} -
                                        {{ $equipment->location->area->area_name }}</small>
                                @endif
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ url('storage/' . $equipment->qrcode) }}" target="_blank"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-external-link-alt me-1"></i>View Full Size
                                </a>
                                <a href="{{ url('storage/' . $equipment->qrcode) }}"
                                    download="QR_{{ $equipment->equipment_code }}.png"
                                    class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-download me-1"></i>Download QR Code
                                </a>
                            </div>
                        @else
                            <div class="text-center">
                                <i class="fas fa-qrcode fa-4x text-muted mb-3"></i>
                                <p class="text-muted">No QR Code available</p>
                                <p class="small text-muted">QR Code will be regenerated if equipment code is changed</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Information -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Update Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info" role="alert">
                            <h6><i class="fas fa-lightbulb me-2"></i>Notes:</h6>
                            <ul class="mb-0 small">
                                <li>Changing equipment code will generate a new QR code</li>
                                <li>Old QR code will be automatically deleted</li>
                                <li>QR code contains equipment identification information</li>
                                <li>Location changes affect QR code text overlay</li>
                            </ul>
                        </div>

                        <div class="mt-3">
                            <small class="text-muted">
                                <strong>Created:</strong> {{ $equipment->created_at->format('d M Y H:i') }}<br>
                                <strong>Last Updated:</strong> {{ $equipment->updated_at->format('d M Y H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Store original values for cascade
        const originalCompanyId = {{ old('company_id', $equipment->location->company_id ?? 'null') }};
        const originalAreaId = {{ old('area_id', $equipment->location->area_id ?? 'null') }};
        const originalLocationId = {{ old('location_id', $equipment->location_id ?? 'null') }};

        // Initialize cascade dropdowns
        document.addEventListener('DOMContentLoaded', function() {
            if (originalCompanyId) {
                loadAreas(originalCompanyId, originalAreaId);
            }
            if (originalAreaId) {
                loadLocations(originalAreaId, originalLocationId);
            }
        });

        // Company change handler
        document.getElementById('company_id').addEventListener('change', function() {
            const companyId = this.value;
            loadAreas(companyId);

            // Reset location
            const locationSelect = document.getElementById('location_id');
            locationSelect.innerHTML = '<option value="">Select Area First</option>';
        });

        // Area change handler
        document.getElementById('area_id').addEventListener('change', function() {
            const areaId = this.value;
            loadLocations(areaId);
        });

        function loadAreas(companyId, selectedAreaId = null) {
            const areaSelect = document.getElementById('area_id');
            const locationSelect = document.getElementById('location_id');

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

                        if (areas.length > 0) {
                            areas.forEach(area => {
                                const selected = selectedAreaId && selectedAreaId == area.id ? 'selected' : '';
                                areaSelect.innerHTML +=
                                    `<option value="${area.id}" ${selected}>${area.area_name}</option>`;
                            });

                            // If there's a selected area, load its locations
                            if (selectedAreaId) {
                                loadLocations(selectedAreaId, originalLocationId);
                            }
                        } else {
                            console.error('No areas found in response:', data);
                            areaSelect.innerHTML = '<option value="">No areas available</option>';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading areas:', error);
                        areaSelect.innerHTML = '<option value="">Error loading areas</option>';
                    });
            } else {
                areaSelect.innerHTML = '<option value="">Select Company First</option>';
            }
        }

        function loadLocations(areaId, selectedLocationId = null) {
            const locationSelect = document.getElementById('location_id');

            locationSelect.innerHTML = '<option value="">Loading locations...</option>';

            if (areaId) {
                fetch(`${window.location.origin}/sisca-v2/equipments/locations-by-area?area_id=${areaId}`)
                    .then(response => response.json())
                    .then(locations => {
                        locationSelect.innerHTML = '<option value="">Select Location</option>';
                        locations.forEach(location => {
                            const selected = selectedLocationId && selectedLocationId == location.id ?
                                'selected' : '';
                            locationSelect.innerHTML +=
                                `<option value="${location.id}" ${selected}>${location.location_code}</option>`;
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        locationSelect.innerHTML = '<option value="">Error loading locations</option>';
                    });
            } else {
                locationSelect.innerHTML = '<option value="">Select Area First</option>';
            }
        }
    </script>
@endpush
