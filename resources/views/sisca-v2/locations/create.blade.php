@extends('sisca-v2.layouts.app')

@section('title', 'Add Location')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Add Location</h3>
                <p class="text-muted mb-0">Create a new location entry</p>
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
                        <form method="POST" action="{{ route('sisca-v2.locations.store') }}">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="location_code" class="form-label required">
                                            Location Code
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                            class="form-control @error('location_code') is-invalid @enderror"
                                            id="location_code" name="location_code" value="{{ old('location_code') }}"
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
                                            id="pos" name="pos" value="{{ old('pos') }}"
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
                                        <label for="plant_id" class="form-label required">
                                            Plant
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('plant_id') is-invalid @enderror" id="plant_id"
                                            name="plant_id" required>
                                            <option value="">Select Plant</option>
                                            @foreach ($plants as $plant)
                                                <option value="{{ $plant->id }}"
                                                    {{ old('plant_id', request('plant_id')) == $plant->id ? 'selected' : '' }}>
                                                    {{ $plant->plant_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('plant_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Select the plant this location belongs to</div>
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
                                                    {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                                    {{ $area->area_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('area_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Select the area within the plant</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="coordinate_x" class="form-label">X Coordinate</label>
                                        <input type="number" step="0.000001"
                                            class="form-control @error('coordinate_x') is-invalid @enderror"
                                            id="coordinate_x" name="coordinate_x" value="{{ old('coordinate_x') }}"
                                            placeholder="X coordinate..." readonly>
                                        @error('coordinate_x')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">X coordinate (click on mapping picture)</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="coordinate_y" class="form-label">Y Coordinate</label>
                                        <input type="number" step="0.000001"
                                            class="form-control @error('coordinate_y') is-invalid @enderror"
                                            id="coordinate_y" name="coordinate_y" value="{{ old('coordinate_y') }}"
                                            placeholder="Y coordinate..." readonly>
                                        @error('coordinate_y')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Y coordinate (click on mapping picture)</div>
                                    </div>
                                </div>
                            </div>

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
                                        <a href="{{ route('sisca-v2.locations.index') }}" class="btn btn-outline-danger">
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
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-map me-2"></i>Area Mapping
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="mappingContainer">
                            <div id="noMappingMessage" class="text-center py-5">
                                <i class="fas fa-map-marked-alt fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">No Mapping Available</h6>
                                <p class="text-muted">Select a plant and area to view the mapping picture</p>
                            </div>

                            <div id="mappingImageContainer" style="display: none;">
                                <div class="position-relative">
                                    <img id="mappingImage" src="" alt="Area Mapping"
                                        class="img-fluid border rounded" style="width: 100%; cursor: crosshair;">
                                    <div id="coordinateMarker" class="position-absolute bg-danger rounded-circle"
                                        style="width: 10px; height: 10px; transform: translate(-50%, -50%); display: none; z-index: 10;">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Click on the mapping picture to set coordinates
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // When plant changes
                $('#plant_id').on('change', function() {
                    const plantId = $(this).val();

                    // Clear area dropdown
                    $('#area_id').html('<option value="">Select Area</option>');

                    // Clear coordinates
                    $('#coordinate_x, #coordinate_y').val('');

                    // Hide mapping
                    hideMappingImage();

                    if (plantId) {
                        // Fetch areas for the selected plant
                        const url = `{{ route('sisca-v2.locations.areas-by-plant', '') }}/${plantId}`;

                        $.get(url)
                            .done(function(areas) {
                                areas.forEach(function(area) {
                                    $('#area_id').append(
                                        `<option value="${area.id}">${area.area_name}</option>`);
                                });
                            })
                            .fail(function(xhr, status, error) {});
                    }
                });

                // When area changes
                $('#area_id').on('change', function() {
                    const areaId = $(this).val();

                    // Clear coordinates
                    $('#coordinate_x, #coordinate_y').val('');

                    if (areaId) {
                        // Find the selected area data
                        const plantId = $('#plant_id').val();
                        if (plantId) {
                            const url = `{{ route('sisca-v2.locations.areas-by-plant', '') }}/${plantId}`;

                            $.get(url)
                                .done(function(areas) {
                                    const selectedArea = areas.find(area => area.id == areaId);
                                    if (selectedArea && selectedArea.mapping_picture) {
                                        showMappingImage(selectedArea.mapping_picture);
                                    } else {
                                        hideMappingImage();
                                    }
                                })
                                .fail(function(xhr, status, error) {});
                        }
                    } else {
                        hideMappingImage();
                    }
                });

                // Mapping image click handler
                $(document).on('click', '#mappingImage', function(e) {
                    const rect = this.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;

                    // Calculate relative coordinates (0-1 range)
                    const relativeX = x / rect.width;
                    const relativeY = y / rect.height;

                    // Update coordinate inputs
                    $('#coordinate_x').val(relativeX.toFixed(6));
                    $('#coordinate_y').val(relativeY.toFixed(6));

                    // Show marker
                    const marker = $('#coordinateMarker');
                    marker.css({
                        left: x + 'px',
                        top: y + 'px',
                        display: 'block'
                    });
                });
            });

            function showMappingImage(mappingPicture) {
                const imagePath = `{{ asset('storage') }}/${mappingPicture}`;
                $('#mappingImage').attr('src', imagePath);
                $('#noMappingMessage').hide();
                $('#mappingImageContainer').show();
                $('#coordinateMarker').hide();
            }

            function hideMappingImage() {
                $('#mappingImageContainer').hide();
                $('#noMappingMessage').show();
                $('#coordinateMarker').hide();
            }
        </script>
    @endpush
@endsection
