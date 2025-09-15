@extends('sisca-v2.layouts.app')

@section('title', 'Create Equipment Type')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Create New Equipment Type</h3>
                <p class="text-muted mb-0">Add a new equipment type to the system</p>
            </div>
            <a href="{{ route('sisca-v2.equipment-types.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>

        <!-- Create Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Equipment Type Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('sisca-v2.equipment-types.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="equipment_name" class="form-label required">
                                    Equipment Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('equipment_name') is-invalid @enderror"
                                    id="equipment_name" name="equipment_name" value="{{ old('equipment_name') }}"
                                    placeholder="Enter equipment name..." required>
                                @error('equipment_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">The name of the equipment (e.g., APAR, Hydrant)
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="equipment_type" class="form-label required">
                                    Equipment Type <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('equipment_type') is-invalid @enderror"
                                    id="equipment_type" name="equipment_type" value="{{ old('equipment_type') }}"
                                    placeholder="Enter equipment type..." required>
                                @error('equipment_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Category or type of the equipment (e.g., Safety Equipment, PPE)</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="desc" class="form-label">Description <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control @error('desc') is-invalid @enderror" id="desc" name="desc" rows="4"
                                    placeholder="Enter equipment description...">{{ old('desc') }}</textarea>
                                @error('desc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Detailed description of the equipment (optional)</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
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
                                    <div class="form-text">Toggle to activate or deactivate this equipment type</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('sisca-v2.equipment-types.index') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Create Equipment Type
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
