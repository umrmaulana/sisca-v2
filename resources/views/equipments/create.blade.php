@extends('layouts.app')

@section('title', 'Add New Equipment')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Add New Equipment</h3>
                <p class="text-muted mb-0">Create a new equipment with automatic QR code generation</p>
            </div>
            <a href="{{ route('equipments.index') }}" class="btn btn-outline-secondary">
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
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-tools me-2"></i>Equipment Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('equipments.store') }}" method="POST">
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
                                        Expired Date
                                    </label>
                                    <input type="date" class="form-control @error('expired_date') is-invalid @enderror"
                                        id="expired_date" name="expired_date" value="{{ old('expired_date') }}"
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
                                        <a href="{{ route('equipments.index') }}" class="btn btn-outline-danger">
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

            <div class="col-lg-6">
                <!-- Company Mapping Section -->
                <div id="companyMappingSection" class="card mb-3" style="display: none;">
                    <div class="card-header" style="background-color: #198754;">
                        <h5 class="card-title mb-0 text-white">
                            <i class="fas fa-industry me-2"></i>Company Mapping
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="position-relative">
                            <img id="companyMappingImage" src="" alt="Company Mapping"
                                class="img-fluid border rounded"
                                style="cursor: crosshair; width: 100%; height: auto; max-height: 300px;">
                        </div>
                        <div class="mt-2 small text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Company mapping for location reference
                        </div>
                    </div>
                </div>

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
    @endsection

    @push('scripts')
        <script>
            let currentLocations = [];
            let selectedLocationData = null;

            // Cascade dropdown functionality
            document.getElementById('company_id').addEventListener('change', function() {
                const companyId = this.value;
                const areaSelect = document.getElementById('area_id');
                const locationSelect = document.getElementById('location_id');

                // Reset area and location
                areaSelect.innerHTML = '<option value="">Loading areas...</option>';
                locationSelect.innerHTML = '<option value="">Select Area First</option>';

                // Hide mapping sections
                document.getElementById('areaMappingSection').style.display = 'none';
                document.getElementById('locationDetailSection').style.display = 'none';

                if (companyId) {
                    // Load company mapping
                    loadCompanyMapping(companyId);

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
                    document.getElementById('companyMappingSection').style.display = 'none';
                    document.getElementById('noMappingMessage').style.display = 'block';
                }
            });

            document.getElementById('area_id').addEventListener('change', function() {
                const areaId = this.value;
                const locationSelect = document.getElementById('location_id');

                locationSelect.innerHTML = '<option value="">Loading locations...</option>';
                document.getElementById('locationDetailSection').style.display = 'none';

                if (areaId) {
                    // Load area mapping and locations
                    loadAreaMapping(areaId);

                    fetch(`${window.location.origin}/equipments/locations-by-area?area_id=${areaId}`)
                        .then(response => response.json())
                        .then(locations => {
                            currentLocations = locations;
                            locationSelect.innerHTML = '<option value="">Select Location</option>';
                            locations.forEach(location => {
                                locationSelect.innerHTML +=
                                    `<option value="${location.id}">${location.location_code}</option>`;
                            });

                            // Display location markers on area mapping
                            displayLocationMarkers(locations);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            locationSelect.innerHTML = '<option value="">Error loading locations</option>';
                        });
                } else {
                    locationSelect.innerHTML = '<option value="">Select Area First</option>';
                    document.getElementById('areaMappingSection').style.display = 'none';
                }
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

            // Load company mapping
            function loadCompanyMapping(companyId) {
                fetch(`${window.location.origin}/locations/company/${companyId}`)
                    .then(response => response.json())
                    .then(company => {
                        if (company.company_mapping_picture) {
                            const imagePath = `${window.location.origin}/storage/${company.company_mapping_picture}`;
                            document.getElementById('companyMappingImage').src = imagePath;
                            document.getElementById('companyMappingSection').style.display = 'block';
                            document.getElementById('noMappingMessage').style.display = 'none';
                        } else {
                            document.getElementById('companyMappingSection').style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading company mapping:', error);
                        document.getElementById('companyMappingSection').style.display = 'none';
                    });
            }

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

                const areaImage = document.getElementById('areaMappingImage');
                const imageRect = areaImage.getBoundingClientRect();

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
        </script>
    @endpush
