@extends('sisca-v2.layouts.app')

@section('title', 'Create Area')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Create New Area</h3>
                <p class="text-muted mb-0">Add a new area to the system</p>
            </div>
            <a href="{{ route('sisca-v2.areas.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>

        <!-- Create Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Area Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('sisca-v2.areas.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="area_name" class="form-label required">
                                    Area Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('area_name') is-invalid @enderror"
                                    id="area_name" name="area_name" value="{{ old('area_name') }}"
                                    placeholder="Enter area name..." required>
                                @error('area_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">The name of the area (e.g., Area 1, Production Area)
                                </div>
                            </div>
                        </div>

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
                                            {{ old('plant_id') == $plant->id ? 'selected' : '' }}>
                                            {{ $plant->plant_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('plant_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Select the plant this area belongs to</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mapping_picture" class="form-label">
                                    Mapping Picture
                                </label>
                                <input type="file" class="form-control @error('mapping_picture') is-invalid @enderror"
                                    id="mapping_picture" name="mapping_picture" accept="image/*">
                                @error('mapping_picture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Upload area mapping picture (JPG, PNG, GIF, max 5MB)</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input @error('is_active') is-invalid @enderror" type="checkbox"
                                        id="is_active" name="is_active" value="1"
                                        {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <strong>Active Status</strong>
                                    </label>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Toggle to activate or deactivate this area</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('sisca-v2.areas.index') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Create Area
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
