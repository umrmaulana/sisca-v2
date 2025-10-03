@extends('layouts.app')

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
                <a href="{{ route('equipments.show', $equipment) }}" class="btn btn-outline-info">
                    <i class="fas fa-eye me-1"></i>View Details
                </a>
                <a href="{{ route('equipments.index') }}" class="btn btn-outline-secondary">
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
            <div class="col-lg-6">
                <!-- Equipment Edit Form -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-edit me-2"></i>Equipment Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('equipments.update', $equipment) }}" method="POST">
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
                                        Expired Date
                                    </label>
                                    <input type="date" class="form-control @error('expired_date') is-invalid @enderror"
                                        id="expired_date" name="expired_date"
                                        value="{{ old('expired_date', $equipment->expired_date ? $equipment->expired_date->format('Y-m-d') : '') }}"
                                        maxlength="20" placeholder="Enter expired date">
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
                                        <a href="{{ route('equipments.show', $equipment) }}"
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
                <!-- Current QR Code -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
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
                                <small class="text-muted d-block">Equipment Code:
                                    {{ $equipment->equipment_code }}</small>
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
                                <p class="small text-muted">QR Code will be regenerated if equipment code is changed
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Information -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
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

            <div class="col-lg-6">
                <!-- Area Mapping Section -->
                <div id="areaMappingSection" class="card mb-3" style="display: none;">
                    <div class="card-header" style="background-color: #0d6efd;">
                        <h5 class="card-title mb-0 text-white">
                            <i class="fas fa-map me-2"></i>Area Mapping
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="position-relative">
                            <img id="areaMappingImage" src="" alt="Area Mapping"
                                class="img-fluid border rounded"
                                style="cursor: crosshair; width: 100%; height: auto; max-height: 300px;">
                            <div id="locationMarkers"></div>
                        </div>
                        <div class="mt-2 small text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Area mapping with location points
                        </div>
                        <div class="mt-2 small text-primary">
                            <i class="fas fa-mouse-pointer me-1"></i>Click on location points to select
                        </div>
                    </div>
                </div>

                <!-- Location Detail Section -->
                <div id="locationDetailSection" class="card mb-3" style="display: none;">
                    <div class="card-header" style="background-color: #6f42c1;">
                        <h5 class="card-title mb-0 text-white">
                            <i class="fas fa-map-marker-alt me-2"></i>Selected Location
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Location Code</small>
                                <div class="fw-bold" id="selectedLocationCode">-</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Coordinates</small>
                                <div class="fw-bold">
                                    Area: <span id="selectedAreaCoords">-</span><br>
                                    Company: <span id="selectedCompanyCoords">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Mapping Available -->
                <div class="card" id="noMappingMessage">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-map me-2"></i>Location Mapping
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-5">
                            <i class="fas fa-map-marked-alt fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No Mapping Available</h6>
                            <p class="text-muted">Select a company and area to view the mapping pictures</p>
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

        let currentLocations = [];
        let selectedLocationData = null;

        // Initialize cascade dropdowns
        document.addEventListener('DOMContentLoaded', function() {
            if (originalCompanyId) {
                loadAreas(originalCompanyId, originalAreaId);
            }
            if (originalAreaId) {
                loadLocations(originalAreaId, originalLocationId);
                loadAreaMapping(originalAreaId);
            }
        });

        // Company change handler
        document.getElementById('company_id').addEventListener('change', function() {
            const companyId = this.value;

            // Hide mapping sections
            document.getElementById('areaMappingSection').style.display = 'none';
            document.getElementById('locationDetailSection').style.display = 'none';

            if (companyId) {
                loadCompanyMapping(companyId);
            } else {
                document.getElementById('companyMappingSection').style.display = 'none';
                document.getElementById('noMappingMessage').style.display = 'block';
            }

            loadAreas(companyId);

            // Reset location
            const locationSelect = document.getElementById('location_id');
            locationSelect.innerHTML = '<option value="">Select Area First</option>';
        });

        // Area change handler
        document.getElementById('area_id').addEventListener('change', function() {
            const areaId = this.value;
            document.getElementById('locationDetailSection').style.display = 'none';

            if (areaId) {
                loadAreaMapping(areaId);
            } else {
                document.getElementById('areaMappingSection').style.display = 'none';
            }

            loadLocations(areaId);
        });

        // Location selection change
        document.getElementById('location_id').addEventListener('change', function() {
            const locationId = this.value;
            if (locationId) {
                const location = currentLocations.find(loc => loc.id == locationId);
                if (location) {
                    selectedLocationData = location;
                    showLocationDetail(location);
                    highlightLocationMarker(locationId);
                }
            } else {
                document.getElementById('locationDetailSection').style.display = 'none';
                clearLocationHighlight();
            }
        });

        // Load area mapping
        function loadAreaMapping(areaId) {
            const companyId = document.getElementById('company_id').value;
            if (!companyId) return;

            fetch(`${window.location.origin}/locations/areas/${companyId}`)
                .then(response => response.json())
                .then(areas => {
                    const area = areas.find(a => a.id == areaId);
                    if (area && area.mapping_picture) {
                        const imagePath = `${window.location.origin}/storage/${area.mapping_picture}`;
                        document.getElementById('areaMappingImage').src = imagePath;
                        document.getElementById('areaMappingSection').style.display = 'block';
                        document.getElementById('noMappingMessage').style.display = 'none';
                    } else {
                        document.getElementById('areaMappingSection').style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error loading area mapping:', error);
                    document.getElementById('areaMappingSection').style.display = 'none';
                });
        }

        // Display location markers on area mapping
        function displayLocationMarkers(locations) {
            const markersContainer = document.getElementById('locationMarkers');
            markersContainer.innerHTML = '';

            locations.forEach(location => {
                if (location.coordinate_x && location.coordinate_y) {
                    const marker = document.createElement('div');
                    marker.className = 'location-marker';
                    marker.id = `marker-${location.id}`;
                    marker.style.cssText = `
                        position: absolute;
                        left: ${location.coordinate_x * 100}%;
                        top: ${location.coordinate_y * 100}%;
                        width: 12px;
                        height: 12px;
                        background-color: #dc3545;
                        border: 2px solid white;
                        border-radius: 50%;
                        cursor: pointer;
                        transform: translate(-50%, -50%);
                        z-index: 10;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
                    `;

                    marker.title = location.location_code;
                    marker.onclick = () => selectLocationFromMarker(location);

                    markersContainer.appendChild(marker);
                }
            });
        }

        // Select location from marker click
        function selectLocationFromMarker(location) {
            document.getElementById('location_id').value = location.id;
            selectedLocationData = location;
            showLocationDetail(location);
            highlightLocationMarker(location.id);
        }

        // Show location detail
        function showLocationDetail(location) {
            document.getElementById('selectedLocationCode').textContent = location.location_code;
            document.getElementById('selectedAreaCoords').textContent =
                location.coordinate_x && location.coordinate_y ?
                `${location.coordinate_x}, ${location.coordinate_y}` : 'Not set';
            document.getElementById('selectedCompanyCoords').textContent =
                location.company_coordinate_x && location.company_coordinate_y ?
                `${location.company_coordinate_x}, ${location.company_coordinate_y}` : 'Not set';

            document.getElementById('locationDetailSection').style.display = 'block';
        }

        // Highlight selected location marker
        function highlightLocationMarker(locationId) {
            // Reset all markers
            document.querySelectorAll('.location-marker').forEach(marker => {
                marker.style.backgroundColor = '#dc3545';
                marker.style.transform = 'translate(-50%, -50%) scale(1)';
            });

            // Highlight selected marker
            const selectedMarker = document.getElementById(`marker-${locationId}`);
            if (selectedMarker) {
                selectedMarker.style.backgroundColor = '#28a745';
                selectedMarker.style.transform = 'translate(-50%, -50%) scale(1.5)';
            }
        }

        // Clear location highlight
        function clearLocationHighlight() {
            document.querySelectorAll('.location-marker').forEach(marker => {
                marker.style.backgroundColor = '#dc3545';
                marker.style.transform = 'translate(-50%, -50%) scale(1)';
            });
        }

        function loadAreas(companyId, selectedAreaId = null) {
            const areaSelect = document.getElementById('area_id');
            const locationSelect = document.getElementById('location_id');

            areaSelect.innerHTML = '<option value="">Loading areas...</option>';
            locationSelect.innerHTML = '<option value="">Select Area First</option>';

            if (companyId) {
                fetch(`${window.location.origin}/equipments/areas-by-company?company_id=${companyId}`)
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
                fetch(`${window.location.origin}/equipments/locations-by-area?area_id=${areaId}`)
                    .then(response => response.json())
                    .then(locations => {
                        currentLocations = locations;
                        locationSelect.innerHTML = '<option value="">Select Location</option>';
                        locations.forEach(location => {
                            const selected = selectedLocationId && selectedLocationId == location.id ?
                                'selected' : '';
                            locationSelect.innerHTML +=
                                `<option value="${location.id}" ${selected}>${location.location_code}</option>`;
                        });

                        // Display location markers on area mapping
                        displayLocationMarkers(locations);

                        // If there's a selected location, show its detail
                        if (selectedLocationId) {
                            const selectedLocation = locations.find(loc => loc.id == selectedLocationId);
                            if (selectedLocation) {
                                showLocationDetail(selectedLocation);
                                highlightLocationMarker(selectedLocationId);
                            }
                        }
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
