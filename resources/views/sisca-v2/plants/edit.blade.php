@extends('sisca-v2.layouts.app')

@section('title', 'Edit Company')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Edit Company</h3>
                <p class="text-muted mb-0">Update company information</p>
            </div>
            <a href="{{ route('sisca-v2.plants.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>

        <!-- Edit Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Company Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('sisca-v2.plants.update', $plant) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="plant_name" class="form-label required">
                                    Plant Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('plant_name') is-invalid @enderror"
                                    id="plant_name" name="plant_name" value="{{ old('plant_name', $plant->plant_name) }}"
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
                                        {{ old('is_active', $plant->is_active) ? 'checked' : '' }}>
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
                                    name="plant_description" rows="3" placeholder="Enter company description...">{{ old('plant_description', $plant->plant_description) }}</textarea>
                                @error('plant_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Optional description about the company</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
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
                                    Upload new plant mapping image. Supported formats: JPEG, PNG, JPG, GIF (Max: 10MB)
                                </div>
                            </div>
                        </div>

                        @if ($plant->plant_mapping_picture)
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-image me-2"></i>Current Mapping Picture
                                    </label>
                                    <div class="border rounded p-2">
                                        <img src="{{ asset('storage/' . $plant->plant_mapping_picture) }}"
                                            alt="Current Plant Mapping" class="img-thumbnail"
                                            style="max-height: 150px; cursor: pointer;"
                                            onclick="showImageModal('{{ asset('storage/' . $plant->plant_mapping_picture) }}', '{{ $plant->plant_name }} - Current Mapping')">
                                        <div class="form-text mt-2">Click image to view full size</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('sisca-v2.plants.index') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Company
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Plant Mapping Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Plant Mapping" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function showImageModal(imageSrc, title) {
                document.getElementById('modalImage').src = imageSrc;
                document.getElementById('imageModalLabel').textContent = title;
                var myModal = new bootstrap.Modal(document.getElementById('imageModal'));
                myModal.show();
            }
        </script>
    @endpush
@endsection
