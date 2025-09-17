@extends('sisca-v2.layouts.app')

@section('title', 'Create Company')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Create New Company</h3>
                <p class="text-muted mb-0">Add a new company to the system</p>
            </div>
            <a href="{{ route('sisca-v2.plants.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>

        <!-- Create Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Company Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('sisca-v2.plants.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="plant_name" class="form-label required">
                                    Company Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('plant_name') is-invalid @enderror"
                                    id="plant_name" name="plant_name" value="{{ old('plant_name') }}"
                                    placeholder="Enter company name..." required>
                                @error('plant_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">The name of the company (e.g., Company 1, Main Company)
                                </div>
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
                                    <div class="form-text">Toggle to activate or deactivate this company</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="plant_description" class="form-label">
                                    <i class="fas fa-file-text me-2"></i>Description
                                </label>
                                <textarea class="form-control @error('plant_description') is-invalid @enderror" id="plant_description"
                                    name="plant_description" rows="3" placeholder="Enter company description...">{{ old('plant_description') }}</textarea>
                                @error('plant_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Optional description about the company</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="plant_mapping_picture" class="form-label">
                                    <i class="fas fa-map me-2"></i>Plant Mapping Picture
                                </label>
                                <input type="file"
                                    class="form-control @error('plant_mapping_picture') is-invalid @enderror"
                                    id="plant_mapping_picture" name="plant_mapping_picture"
                                    accept="image/jpeg,image/png,image/jpg,image/gif">
                                @error('plant_mapping_picture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Upload plant mapping image. Supported formats: JPEG, PNG, JPG, GIF (Max: 10MB)
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('sisca-v2.plants.index') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Create Company
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
