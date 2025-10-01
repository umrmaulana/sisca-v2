@extends('layouts.app')

@section('title', 'Add New Location')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Add New Location</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('locations.index') }}">Locations</a></li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <!-- Form Section -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-plus-circle me-2"></i>Location Information
                        </h5>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <h6>Please fix the following errors:</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('locations.store') }}" method="POST"
                            onsubmit="console.log('Form submitted'); return true;" id="locationForm">
                            @csrf

                            <!-- Company Selection (Hidden for Supervisor) -->
                            @if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Management')
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="company_id" class="form-label">Company<span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('company_id') is-invalid @enderror"
                                                id="company_id" name="company_id" required>
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
                                            <div class="form-text">Select the company where this location is situated</div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Hidden company field for Supervisor -->
                                <input type="hidden" id="company_id" name="company_id"
                                    value="{{ $companies->first()->id ?? '' }}">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Assigned Company</label>
                                            <div class="form-control-plaintext">
                                                <span
                                                    class="badge bg-info">{{ $companies->first()->company_name ?? 'No Company Assigned' }}</span>
                                            </div>
                                            <div class="form-text">You are assigned to this company</div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Area Selection -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="area_id" class="form-label">Area <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('area_id') is-invalid @enderror" id="area_id"
                                            name="area_id" required>
                                            <option value="">Select Area</option>
                                            @if (auth()->user()->role === 'Supervisor')
                                                @foreach ($areas as $area)
                                                    <option value="{{ $area->id }}"
                                                        {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                                        {{ $area->area_name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('area_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Select the area within the company</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Location Name -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="location_name" class="form-label">Location Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('location_name') is-invalid @enderror"
                                            id="location_name" name="location_name" value="{{ old('location_name') }}"
                                            placeholder="Enter location name..." required>
                                        @error('location_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Descriptive name for this location</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Area Coordinates -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="coordinate_x" class="form-label">Area X Coordinate</label>
                                        <input type="number" step="0.000001"
                                            class="form-control @error('coordinate_x') is-invalid @enderror"
                                            id="coordinate_x" name="coordinate_x" value="{{ old('coordinate_x') }}"
                                            placeholder="X coordinate for area mapping...">
                                        @error('coordinate_x')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">X position relative to area mapping (0-1 decimal)</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="coordinate_y" class="form-label">Area Y Coordinate</label>
                                        <input type="number" step="0.000001"
                                            class="form-control @error('coordinate_y') is-invalid @enderror"
                                            id="coordinate_y" name="coordinate_y" value="{{ old('coordinate_y') }}"
                                            placeholder="Y coordinate for area mapping...">
                                        @error('coordinate_y')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Y position relative to area mapping (0-1 decimal)</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Company Coordinates -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="company_coordinate_x" class="form-label">Company X Coordinate</label>
                                        <input type="number" step="0.01"
                                            class="form-control @error('company_coordinate_x') is-invalid @enderror"
                                            id="company_coordinate_x" name="company_coordinate_x"
                                            value="{{ old('company_coordinate_x') }}"
                                            placeholder="X coordinate for company mapping...">
                                        @error('company_coordinate_x')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">X position relative to company mapping (0-100%)</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="company_coordinate_y" class="form-label">Company Y Coordinate</label>
                                        <input type="number" step="0.01"
                                            class="form-control @error('company_coordinate_y') is-invalid @enderror"
                                            id="company_coordinate_y" name="company_coordinate_y"
                                            value="{{ old('company_coordinate_y') }}"
                                            placeholder="Y coordinate for company mapping...">
                                        @error('company_coordinate_y')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Y position relative to company mapping (0-100%)</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Active Status -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input @error('is_active') is-invalid @enderror"
                                                type="checkbox" id="is_active" name="is_active" value="1"
                                                {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                <strong>Active Status</strong>
                                            </label>
                                            @error('is_active')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Toggle to activate or deactivate this location</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('locations.index') }}" class="btn btn-outline-danger">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Create Location
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
                                class="img-fluid border rounded shadow-sm"
                                style="cursor: crosshair; width: 100%; height: auto;">
                            <div id="companyCoordinateMarker"
                                style="position: absolute; width: 12px; height: 12px; 
                                       background: #28a745; border: 2px solid white; 
                                       border-radius: 50%; transform: translate(-50%, -50%); 
                                       box-shadow: 0 2px 8px rgba(0,0,0,0.3); 
                                       display: none; z-index: 10;">
                            </div>
                        </div>
                        <div class="mt-2 small text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Current company coordinates:
                            <span id="companyCurrentCoords">
                                X: <span id="companyCoordX">-</span>, Y: <span id="companyCoordY">-</span>
                            </span>
                        </div>
                        <div class="mt-2 small text-success">
                            <i class="fas fa-mouse-pointer me-1"></i>Click on the company mapping to set coordinates
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
                                class="img-fluid border rounded shadow-sm"
                                style="cursor: crosshair; width: 100%; height: auto;">
                            <div id="areaCoordinateMarker"
                                style="position: absolute; width: 12px; height: 12px; 
                                       background: #0d6efd; border: 2px solid white; 
                                       border-radius: 50%; transform: translate(-50%, -50%); 
                                       box-shadow: 0 2px 8px rgba(0,0,0,0.3); 
                                       display: none; z-index: 10;">
                            </div>
                        </div>
                        <div class="mt-2 small text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Current area coordinates:
                            <span id="areaCurrentCoords">
                                X: <span id="areaCoordX">-</span>, Y: <span id="areaCoordY">-</span>
                            </span>
                        </div>
                        <div class="mt-2 small text-primary">
                            <i class="fas fa-mouse-pointer me-1"></i>Click on the area mapping to set coordinates
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
        $(document).ready(function() {
            let currentCompanyData = null;
            let currentAreaData = null;
            const userRole = '{{ auth()->user()->role }}';

            // Initialize for supervisor (auto-load company mapping)
            if (userRole === 'Supervisor') {
                const companyId = $('#company_id').val();
                if (companyId) {
                    loadCompanyMapping(companyId);
                }
            }

            // When company changes (only for Admin/Management)
            $('#company_id').on('change', function() {
                const companyId = $(this).val();

                // For Admin/Management - clear area dropdown and load areas
                if (userRole === 'Admin' || userRole === 'Management') {
                    // Clear area dropdown
                    $('#area_id').html('<option value="">Select Area</option>');
                }

                // Clear company coordinates
                $('#company_coordinate_x, #company_coordinate_y').val('');
                $('#companyCoordinateMarker').hide();
                $('#companyCoordX, #companyCoordY').text('-');

                // Hide area mapping
                $('#areaMappingSection').hide();

                if (companyId) {
                    loadCompanyMapping(companyId);

                    // Only load areas dynamically for Admin/Management
                    if (userRole === 'Admin' || userRole === 'Management') {
                        loadAreasForCompany(companyId);
                    }
                } else {
                    $('#companyMappingSection').hide();
                    $('#noMappingMessage').show();
                }
            });

            // When area changes
            $('#area_id').on('change', function() {
                const areaId = $(this).val();

                // Clear area coordinates
                $('#coordinate_x, #coordinate_y').val('');
                $('#areaCoordinateMarker').hide();
                $('#areaCoordX, #areaCoordY').text('-');

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

                // Update display
                $('#companyCoordX').text(relativeX.toFixed(2));
                $('#companyCoordY').text(relativeY.toFixed(2));

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

                // Update display
                $('#areaCoordX').text(relativeX.toFixed(6));
                $('#areaCoordY').text(relativeY.toFixed(6));

                // Show marker
                $('#areaCoordinateMarker').css({
                    left: x + 'px',
                    top: y + 'px',
                    display: 'block'
                });
            });

            function loadCompanyMapping(companyId) {
                fetch(`${window.location.origin}/locations/company/${companyId}`)
                    .then(response => response.json())
                    .then(company => {
                        currentCompanyData = company;
                        if (company.company_mapping_picture) {
                            const imagePath =
                                `${window.location.origin}/storage/${company.company_mapping_picture}`;
                            $('#companyMappingImage').attr('src', imagePath);
                            $('#companyMappingSection').show();
                            $('#noMappingMessage').hide();
                        } else {
                            $('#companyMappingSection').hide();
                            if (!$('#areaMappingSection').is(':visible')) {
                                $('#noMappingMessage').show();
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error loading company mapping:', error);
                        $('#companyMappingSection').hide();
                    });
            }

            function loadAreaMapping(areaId) {
                const companyId = $('#company_id').val();
                if (!companyId) return;

                fetch(`${window.location.origin}/locations/areas/${companyId}`)
                    .then(response => response.json())
                    .then(areas => {
                        const area = areas.find(a => a.id == areaId);
                        if (area && area.mapping_picture) {
                            currentAreaData = area;
                            const imagePath = `${window.location.origin}/storage/${area.mapping_picture}`;
                            $('#areaMappingImage').attr('src', imagePath);
                            $('#areaMappingSection').show();
                            $('#noMappingMessage').hide();
                        } else {
                            $('#areaMappingSection').hide();
                            if (!$('#companyMappingSection').is(':visible')) {
                                $('#noMappingMessage').show();
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error loading area mapping:', error);
                        $('#areaMappingSection').hide();
                    });
            }

            function loadAreasForCompany(companyId) {
                fetch(`${window.location.origin}/locations/areas/${companyId}`)
                    .then(response => response.json())
                    .then(areas => {
                        areas.forEach(area => {
                            $('#area_id').append(
                                `<option value="${area.id}">${area.area_name}</option>`);
                        });
                    })
                    .catch(error => console.error('Error loading areas:', error));
            }

            // Form submission debug
            $('#locationForm').on('submit', function(e) {
                console.log('Form submit event triggered');
                const formData = new FormData(this);
                console.log('Form data:');
                for (let [key, value] of formData.entries()) {
                    console.log(key + ': ' + value);
                }
                return true; // Allow form to submit normally
            });
        });
    </script>
@endpush
