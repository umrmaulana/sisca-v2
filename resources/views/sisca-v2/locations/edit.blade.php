@extends('sisca-v2.layouts.app')

@section('title', 'Edit Location')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Edit Location</h3>
                <p class="text-muted mb-0">Update location information</p>
            </div>
            <a href="{{ route('sisca-v2.locations.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>

        <div class="row">
            <!-- Form Section -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Location Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('sisca-v2.locations.update', $location) }}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="location_code" class="form-label required">
                                            Location Code
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                            class="form-control @error('location_code') is-invalid @enderror"
                                            id="location_code" name="location_code"
                                            value="{{ old('location_code', $location->location_code) }}"
                                            placeholder="Enter location code..." required>
                                        @error('location_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Unique identifier for the location (max 20 characters)</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="pos" class="form-label">Position</label>
                                        <input type="text" class="form-control @error('pos') is-invalid @enderror"
                                            id="pos" name="pos" value="{{ old('pos', $location->pos) }}"
                                            placeholder="Enter position...">
                                        @error('pos')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Position description (optional)</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="company_id" class="form-label required">
                                            Company
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('company_id') is-invalid @enderror"
                                            id="company_id" name="company_id" required>
                                            <option value="">Select Company</option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}"
                                                    {{ old('company_id', $location->company_id) == $company->id ? 'selected' : '' }}>
                                                    {{ $company->company_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('company_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Select the company this location belongs to</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="area_id" class="form-label required">
                                            Area
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('area_id') is-invalid @enderror" id="area_id"
                                            name="area_id" required>
                                            <option value="">Select Area</option>
                                            @foreach ($areas as $area)
                                                <option value="{{ $area->id }}"
                                                    {{ old('area_id', $location->area_id) == $area->id ? 'selected' : '' }}>
                                                    {{ $area->area_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('area_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Select the area within the company</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="coordinate_x" class="form-label">X Coordinate (Area)</label>
                                        <input type="text"
                                            class="form-control @error('coordinate_x') is-invalid @enderror"
                                            id="coordinate_x" name="coordinate_x"
                                            value="{{ old('coordinate_x', $location->coordinate_x) }}" readonly>
                                        @error('coordinate_x')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">X coordinate (click on area mapping picture)</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="coordinate_y" class="form-label">Y Coordinate (Area)</label>
                                        <input type="text"
                                            class="form-control @error('coordinate_y') is-invalid @enderror"
                                            id="coordinate_y" name="coordinate_y"
                                            value="{{ old('coordinate_y', $location->coordinate_y) }}" readonly>
                                        @error('coordinate_y')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Y coordinate (click on area mapping picture)</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="company_coordinate_x" class="form-label">X Coordinate (Company)</label>
                                        <input type="number" step="0.01"
                                            class="form-control @error('company_coordinate_x') is-invalid @enderror"
                                            id="company_coordinate_x" name="company_coordinate_x"
                                            value="{{ old('company_coordinate_x', $location->company_coordinate_x) }}"
                                            placeholder="X coordinate for company mapping...">
                                        @error('company_coordinate_x')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">X position relative to company mapping (0-100%)</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="company_coordinate_y" class="form-label">Y Coordinate
                                            (Company)</label>
                                        <input type="number" step="0.01"
                                            class="form-control @error('company_coordinate_y') is-invalid @enderror"
                                            id="company_coordinate_y" name="company_coordinate_y"
                                            value="{{ old('company_coordinate_y', $location->company_coordinate_y) }}"
                                            placeholder="Y coordinate for company mapping...">
                                        @error('company_coordinate_y')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Y position relative to company mapping (0-100%)</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input @error('is_active') is-invalid @enderror"
                                                type="checkbox" id="is_active" name="is_active" value="1"
                                                {{ old('is_active', $location->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                <strong>Active Status</strong>
                                            </label>
                                            @error('is_active')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">
                                                Toggle to activate or deactivate this location
                                                <small class="text-muted d-block mt-1">
                                                    Current: {{ $location->is_active ? 'Active' : 'Inactive' }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('sisca-v2.locations.index') }}" class="btn btn-outline-danger">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Update Location
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mapping Section -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-map me-2"></i>Dual Mapping Interface
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Company Mapping Section -->
                        <div id="companyMappingSection" style="display: none;">
                            <div class="mb-3">
                                <h6 class="text-success mb-2">
                                    <i class="fas fa-industry me-2"></i>Company Mapping
                                </h6>
                                <div class="alert alert-success">
                                    <small><i class="fas fa-info-circle me-2"></i>Click on company map to set company
                                        coordinates (0-100%)</small>
                                </div>
                                <div class="position-relative border rounded" style="background: #f8f9fa;">
                                    <img id="companyMappingImage" src="" alt="Company Mapping" class="img-fluid"
                                        style="width: 100%; cursor: crosshair; border-radius: 8px;">
                                    <div id="companyCoordinateMarker"
                                        style="position: absolute; display: none; z-index: 10;">
                                        <div
                                            style="width: 12px; height: 12px; background-color: #28a745; border: 2px solid white; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                        </div>
                                    </div>
                                </div>
                                @if ($location->company_coordinate_x && $location->company_coordinate_y)
                                    <small class="text-success mt-2 d-block">
                                        Current company coordinates:
                                        X={{ number_format($location->company_coordinate_x, 2) }}%,
                                        Y={{ number_format($location->company_coordinate_y, 2) }}%
                                    </small>
                                @endif
                            </div>
                        </div>

                        <!-- Area Mapping Section -->
                        <div id="areaMappingSection" style="display: none;">
                            <div class="mb-3">
                                <h6 class="text-primary mb-2">
                                    <i class="fas fa-map-marker-alt me-2"></i>Area Mapping
                                </h6>
                                <div class="alert alert-info">
                                    <small><i class="fas fa-info-circle me-2"></i>Click on area map to set area coordinates
                                        (0.0-1.0 decimal)</small>
                                </div>
                                <div class="position-relative border rounded" style="background: #f8f9fa;">
                                    <img id="areaMappingImage" src="" alt="Area Mapping" class="img-fluid"
                                        style="width: 100%; cursor: crosshair; border-radius: 8px;">
                                    <div id="areaCoordinateMarker"
                                        style="position: absolute; display: none; z-index: 10;">
                                        <div
                                            style="width: 12px; height: 12px; background-color: #dc3545; border: 2px solid white; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                        </div>
                                    </div>
                                </div>
                                @if ($location->coordinate_x && $location->coordinate_y)
                                    <small class="text-primary mt-2 d-block">
                                        Current area coordinates: X={{ number_format($location->coordinate_x, 6) }},
                                        Y={{ number_format($location->coordinate_y, 6) }}
                                    </small>
                                @endif
                            </div>
                        </div>

                        <!-- No Mapping Message -->
                        <div id="noMappingMessage" class="text-center py-5">
                            <i class="fas fa-map-marked-alt fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Select Company and Area</h6>
                            <p class="text-muted">Choose a company and area to view mapping interfaces</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                let currentCompanyData = null;
                let currentAreaData = null;

                // Initialize existing coordinates markers
                @if ($location->company && $location->company->company_mapping_picture)
                    loadCompanyMapping({{ $location->company_id }});
                @endif

                @if ($location->area && $location->area->mapping_picture)
                    loadAreaMapping({{ $location->area_id }});
                @endif

                // When company changes
                $('#company_id').on('change', function() {
                    const companyId = $(this).val();
                    const currentCompanyId = {{ $location->company_id ?? 'null' }};

                    // Clear area dropdown
                    $('#area_id').html('<option value="">Select Area</option>');

                    // Only clear company coordinates if company actually changes (not on initial load)
                    if (companyId != currentCompanyId && $('#company_id').data('initialized')) {
                        $('#company_coordinate_x, #company_coordinate_y').val('');
                        $('#companyCoordinateMarker').hide();
                    }

                    // Hide area mapping
                    $('#areaMappingSection').hide();

                    if (companyId) {
                        loadCompanyMapping(companyId);
                        loadAreasForCompany(companyId);
                    } else {
                        $('#companyMappingSection').hide();
                        $('#noMappingMessage').show();
                    }
                });

                // When area changes
                $('#area_id').on('change', function() {
                    const areaId = $(this).val();
                    const currentAreaId = {{ $location->area_id ?? 'null' }};

                    // Only clear area coordinates if area actually changes (not on initial load)
                    if (areaId != currentAreaId && $('#area_id').data('initialized')) {
                        $('#coordinate_x, #coordinate_y').val('');
                        $('#areaCoordinateMarker').hide();
                    }

                    if (areaId) {
                        loadAreaMapping(areaId);
                    } else {
                        $('#areaMappingSection').hide();
                    }
                });

                // Company mapping image click handler
                $(document).on('click', '#companyMappingImage', function(e) {
                    const rect = this.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;

                    // Calculate relative coordinates (0-100 percentage range for company)
                    const relativeX = (x / rect.width) * 100;
                    const relativeY = (y / rect.height) * 100;

                    // Update coordinate inputs
                    $('#company_coordinate_x').val(relativeX.toFixed(2));
                    $('#company_coordinate_y').val(relativeY.toFixed(2));

                    // Show marker
                    $('#companyCoordinateMarker').css({
                        left: x + 'px',
                        top: y + 'px',
                        display: 'block'
                    });
                });

                // Area mapping image click handler
                $(document).on('click', '#areaMappingImage', function(e) {
                    const rect = this.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;

                    // Calculate relative coordinates (0-1 decimal range for area)
                    const relativeX = x / rect.width;
                    const relativeY = y / rect.height;

                    // Update coordinate inputs
                    $('#coordinate_x').val(relativeX.toFixed(6));
                    $('#coordinate_y').val(relativeY.toFixed(6));

                    // Show marker
                    $('#areaCoordinateMarker').css({
                        left: x + 'px',
                        top: y + 'px',
                        display: 'block'
                    });
                });

                function loadCompanyMapping(companyId) {
                    fetch(`${window.location.origin}/sisca-v2/locations/company/${companyId}`)
                        .then(response => {
                            console.log('Company API response status:', response.status);
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(company => {
                            console.log('Company data received:', company);
                            currentCompanyData = company;
                            if (company.company_mapping_picture) {
                                const imagePath =
                                    `${window.location.origin}/storage/${company.company_mapping_picture}`;
                                console.log('Company image path:', imagePath);
                                $('#companyMappingImage').attr('src', imagePath);
                                $('#companyMappingSection').show();
                                $('#noMappingMessage').hide();

                                // Restore existing company coordinates
                                @if ($location->company_coordinate_x && $location->company_coordinate_y)
                                    if (companyId == {{ $location->company_id }}) {
                                        setTimeout(() => {
                                            updateCompanyMarkerPosition(
                                                {{ $location->company_coordinate_x }},
                                                {{ $location->company_coordinate_y }});
                                        }, 100);
                                    }
                                @endif
                            } else {
                                $('#companyMappingSection').hide();
                                if (!currentAreaData || !currentAreaData.mapping_picture) {
                                    $('#noMappingMessage').show();
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error loading company mapping:', error);
                            $('#companyMappingSection').hide();
                            if (!$('#areaMappingSection').is(':visible')) {
                                $('#noMappingMessage').show();
                            }
                        });
                }

                function loadAreaMapping(areaId) {
                    const companyId = $('#company_id').val();
                    if (!companyId) return;

                    fetch(`${window.location.origin}/sisca-v2/locations/areas/${companyId}`)
                        .then(response => response.json())
                        .then(areas => {
                            const area = areas.find(a => a.id == areaId);
                            if (area && area.mapping_picture) {
                                currentAreaData = area;
                                const imagePath = `${window.location.origin}/storage/${area.mapping_picture}`;
                                $('#areaMappingImage').attr('src', imagePath);
                                $('#areaMappingSection').show();
                                $('#noMappingMessage').hide();

                                // Restore existing area coordinates
                                @if ($location->coordinate_x && $location->coordinate_y)
                                    if (areaId == {{ $location->area_id }}) {
                                        setTimeout(() => {
                                            updateAreaMarkerPosition({{ $location->coordinate_x }},
                                                {{ $location->coordinate_y }});
                                        }, 100);
                                    }
                                @endif
                            } else {
                                $('#areaMappingSection').hide();
                            }
                        })
                        .catch(error => {
                            console.error('Error loading area mapping:', error);
                            $('#areaMappingSection').hide();
                        });
                }

                function loadAreasForCompany(companyId) {
                    fetch(`${window.location.origin}/sisca-v2/locations/areas/${companyId}`)
                        .then(response => response.json())
                        .then(areas => {
                            areas.forEach(area => {
                                $('#area_id').append(
                                    `<option value="${area.id}">${area.area_name}</option>`);
                            });

                            // Re-select current area if editing
                            @if ($location->area_id)
                                if (companyId == {{ $location->company_id }}) {
                                    $('#area_id').val({{ $location->area_id }}).trigger('change');
                                }
                            @endif
                        })
                        .catch(error => console.error('Error loading areas:', error));
                }

                function updateCompanyMarkerPosition(relativeX, relativeY) {
                    const image = $('#companyMappingImage')[0];
                    if (image && image.complete) {
                        const rect = image.getBoundingClientRect();
                        const x = (relativeX / 100) * rect.width;
                        const y = (relativeY / 100) * rect.height;

                        $('#companyCoordinateMarker').css({
                            left: x + 'px',
                            top: y + 'px',
                            display: 'block'
                        });
                    }
                }

                function updateAreaMarkerPosition(relativeX, relativeY) {
                    const image = $('#areaMappingImage')[0];
                    if (image && image.complete) {
                        const rect = image.getBoundingClientRect();
                        const x = relativeX * rect.width;
                        const y = relativeY * rect.height;

                        $('#areaCoordinateMarker').css({
                            left: x + 'px',
                            top: y + 'px',
                            display: 'block'
                        });
                    }
                }

                // Initialize page
                const existingCompanyId = $('#company_id').val();
                if (existingCompanyId) {
                    $('#company_id').trigger('change');
                }

                // Mark dropdowns as initialized after initial load
                setTimeout(() => {
                    $('#company_id').data('initialized', true);
                    $('#area_id').data('initialized', true);
                }, 1000);
            });
        </script>
    @endpush
@endsection
