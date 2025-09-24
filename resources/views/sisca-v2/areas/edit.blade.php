@extends('sisca-v2.layouts.app')

@section('title', 'Edit Area')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Edit Area</h3>
                <p class="text-muted mb-0">Update area information</p>
            </div>
            <a href="{{ route('sisca-v2.areas.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>

        <!-- Edit Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Area Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('sisca-v2.areas.update', $area) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="area_name" class="form-label required">
                                    Area Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('area_name') is-invalid @enderror"
                                    id="area_name" name="area_name" value="{{ old('area_name', $area->area_name) }}"
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
                                <label for="company_id" class="form-label required">
                                    Company
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('company_id') is-invalid @enderror" id="company_id"
                                    name="company_id" required>
                                    <option value="">Select Company</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}"
                                            {{ old('company_id', $area->company_id) == $company->id ? 'selected' : '' }}>
                                            {{ $company->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Select the company this area belongs to</div>
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
                                <div class="form-text">Upload new mapping picture (JPG, PNG, GIF, max 5MB)</div>

                                @if ($area->mapping_picture)
                                    <div class="mt-2">
                                        <small class="text-muted">Current mapping picture:</small>
                                        <div class="mt-1">
                                            <img src="{{ url('storage/' . $area->mapping_picture) }}" alt="Current mapping"
                                                class="img-thumbnail"
                                                style="max-width: 100px; max-height: 100px; cursor: pointer;"
                                                data-bs-toggle="modal" data-bs-target="#currentImageModal">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input @error('is_active') is-invalid @enderror" type="checkbox"
                                        id="is_active" name="is_active" value="1"
                                        {{ old('is_active', $area->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <strong>Active Status</strong>
                                    </label>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Toggle to activate or deactivate this area
                                        <small class="text-muted d-block mt-1">
                                            Current: {{ $area->is_active ? 'Active' : 'Inactive' }}
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
                                <a href="{{ route('sisca-v2.areas.index') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Area
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Current Image Modal -->
    @if ($area->mapping_picture)
        <div class="modal fade" id="currentImageModal" tabindex="-1" aria-labelledby="currentImageModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="currentImageModalLabel">Current Mapping Picture</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ url('storage/' . $area->mapping_picture) }}" alt="Current mapping picture"
                            class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
