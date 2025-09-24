@extends('sisca-v2.layouts.app')

@section('title', $isRecheck ? 'Equipment Re-inspection' : 'Equipment Inspection')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">
                    @if ($isRecheck)
                        Equipment Re-inspection
                    @else
                        Equipment Inspection
                    @endif
                </h3>
                <p class="text-muted mb-0">
                    @if ($isRecheck)
                        Re-inspect equipment with previous NG items
                    @else
                        Complete equipment inspection checklist
                    @endif
                </p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('sisca-v2.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sisca-v2.checksheets.index') }}">Checksheet</a></li>
                    <li class="breadcrumb-item active">
                        @if ($isRecheck)
                            Re-inspection
                        @else
                            Inspection
                        @endif
                    </li>
                </ol>
            </nav>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Previous Inspection Alert -->
        @if ($isRecheck && $previousInspection)
            <div class="alert alert-warning mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Previous Inspection Found:</strong> This equipment was last inspected on
                        {{ $previousInspection->inspection_date->format('d M Y') }} by
                        {{ $previousInspection->user->name }}.
                        @php
                            $ngCount = $previousInspection->details->where('status', 'NG')->count();
                        @endphp
                        @if ($ngCount > 0)
                            <span class="badge bg-danger ms-2">{{ $ngCount }} NG Items</span>
                        @endif
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('sisca-v2.checksheets.show', $previousInspection->id) }}"
                            class="btn btn-outline-info btn-sm">
                            <i class="fas fa-eye me-1"></i>View Previous Report
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Equipment Information -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-3">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>Equipment Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Code:</strong></td>
                                <td>{{ $equipment->equipment_code }}</td>
                            </tr>
                            <tr>
                                <td><strong>Type:</strong></td>
                                <td>{{ $equipment->equipmentType->equipment_name ?? 'N/A' }} -
                                    {{ $equipment->equipmentType->equipment_type ?? '' }}</td>
                            </tr>
                            @if ($equipment->location)
                                <tr>
                                    <td><strong>Location:</strong></td>
                                    <td>{{ $equipment->location->location_code }}</td>
                                </tr>
                                @if ($equipment->location->area)
                                    <tr>
                                        <td><strong>Area:</strong></td>
                                        <td>{{ $equipment->location->area->area_name }}</td>
                                    </tr>
                                @endif
                                @if ($equipment->location->company)
                                    <tr>
                                        <td><strong>Company:</strong></td>
                                        <td>{{ $equipment->location->company->company_name }}</td>
                                    </tr>
                                @endif
                            @endif
                            @if ($equipment->periodCheck)
                                <tr>
                                    <td><strong>Check Period:</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ $equipment->periodCheck->period_check }}</span>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-3">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-calendar-check me-2"></i>
                            @if ($isRecheck)
                                Re-inspection Information
                            @else
                                Inspection Information
                            @endif
                        </h6>
                    </div>
                    <div class="card-body">
                        @if ($periodRestriction)
                            @if ($periodRestriction['can_inspect'])
                                @if ($periodRestriction['existing_inspection'] && $periodRestriction['has_ng_items'])
                                    <div class="alert alert-warning mb-2">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Re-inspection Required</strong><br>
                                        Previous inspection had NG items. You can re-inspect this equipment.
                                    </div>
                                @else
                                    <div class="alert alert-success mb-2">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <strong>Ready for Inspection</strong><br>
                                        This equipment can be inspected now.
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-danger mb-2">
                                    <i class="fas fa-ban me-2"></i>
                                    <strong>Cannot Inspect</strong><br>
                                    {{ $periodRestriction['reason'] }}
                                </div>
                            @endif

                            @if ($periodRestriction['existing_inspection'])
                                <small class="text-muted">
                                    <strong>Last Inspection:</strong>
                                    {{ $periodRestriction['existing_inspection']->inspection_date->format('d/m/Y') }}
                                    by {{ $periodRestriction['existing_inspection']->user->name }}
                                </small>
                            @endif
                        @else
                            <div class="alert alert-success mb-2">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Ready for Inspection</strong><br>
                                This equipment can be inspected now.
                            </div>
                        @endif

                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Inspector NPK:</strong></td>
                                <td>{{ Auth::guard('sisca-v2')->user()->npk }}</td>
                            </tr>
                            <tr>
                                <td><strong>Inspector Name:</strong></td>
                                <td>{{ Auth::guard('sisca-v2')->user()->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Role:</strong></td>
                                <td>{{ Auth::guard('sisca-v2')->user()->role }}</td>
                            </tr>
                            <tr>
                                <td><strong>Date:</strong></td>
                                <td>{{ now()->format('d/m/Y') }}</td>
                            </tr>
                            @if ($isRecheck)
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td>
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-redo me-1"></i>NG Item Follow-up
                                        </span>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            @if (!$periodRestriction || $periodRestriction['can_inspect'])
                <!-- Checksheet Form -->
                <form action="{{ route('sisca-v2.checksheets.store') }}" method="POST" enctype="multipart/form-data"
                    id="checksheetForm">
                    @csrf
                    <input type="hidden" name="equipment_id" value="{{ $equipment->id }}">
                    @if ($isRecheck && $previousInspection)
                        <input type="hidden" name="is_recheck" value="1">
                        <input type="hidden" name="previous_inspection_id" value="{{ $previousInspection->id }}">
                    @endif

                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-clipboard-list me-2"></i>
                                        @if ($isRecheck)
                                            Re-inspection Checklist
                                            @if ($previousInspection)
                                                <span class="badge bg-warning text-dark ms-2">
                                                    Focus on
                                                    {{ $previousInspection->details->where('status', 'NG')->count() }}
                                                    NG Items
                                                </span>
                                            @endif
                                        @else
                                            Inspection Checklist
                                        @endif
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @php
                                        $templatesToShow =
                                            $isRecheck && isset($ngOnlyTemplates) && $ngOnlyTemplates->isNotEmpty()
                                                ? $ngOnlyTemplates
                                                : $templates;
                                    @endphp

                                    @if ($isRecheck && isset($ngOnlyTemplates) && $ngOnlyTemplates->isNotEmpty())
                                        <div class="alert alert-info mb-4">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Re-inspection Mode:</strong> Only showing
                                            {{ $ngOnlyTemplates->count() }} items
                                            that were previously marked as NG.
                                            Items that were OK will remain unchanged.
                                        </div>
                                    @endif

                                    @foreach ($templatesToShow as $template)
                                        @php
                                            $previousDetail = null;
                                            $isPreviousNG = false;
                                            if ($isRecheck && $previousInspection) {
                                                $previousDetail = $previousInspection->details->firstWhere(
                                                    'checksheet_id',
                                                    $template->id,
                                                );
                                                $isPreviousNG = $previousDetail && $previousDetail->status === 'NG';
                                            }
                                        @endphp

                                        <div
                                            class="inspection-item card mb-4 shadow-sm 
                                        @if ($isPreviousNG || $isRecheck) border-warning ng-item @else border-light @endif">

                                            @if ($isPreviousNG || $isRecheck)
                                                <div class="bg-warning text-dark p-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        <strong>Previously NG - Requires Re-inspection</strong>
                                                        <span class="badge bg-danger ms-auto">Priority</span>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="card-body">
                                                <div class="row g-4">
                                                    <!-- Item Number & Standard Picture -->
                                                    <div class="col-lg-3 col-md-6">
                                                        <div class="text-center">
                                                            <div class="mb-2">
                                                                <span
                                                                    class="badge {{ $isPreviousNG || $isRecheck ? 'bg-warning text-dark' : 'bg-primary' }} fs-6">
                                                                    Item {{ $template->order_number ?? $loop->iteration }}
                                                                </span>
                                                            </div>
                                                            <label class="form-label fw-bold text-muted">Standard
                                                                Picture</label>
                                                            <div class="standard-picture-container mt-2">
                                                                @if ($template->standar_picture && asset('public/' . $template->standar_picture))
                                                                    <img src="{{ url('storage/' . $template->standar_picture) }}"
                                                                        alt="Standard Picture"
                                                                        class="img-thumbnail standard-picture"
                                                                        onclick="showImageModal('{{ url('storage/' . $template->standar_picture) }}', '{{ $template->item_name }}')"
                                                                        role="button" title="Click to enlarge"
                                                                        style="width: 120px; height: 120px; object-fit: cover; cursor: pointer;">
                                                                @else
                                                                    <div class="no-image-placeholder d-flex align-items-center justify-content-center"
                                                                        style="width: 120px; height: 120px; background-color: #f8f9fa; border: 2px dashed #dee2e6;">
                                                                        <i class="fas fa-image text-muted fa-2x"></i>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Item Check Information -->
                                                    <div class="col-lg-3 col-md-6">
                                                        <label class="form-label fw-bold text-muted">Item Check</label>
                                                        <div class="item-info">
                                                            <h6 class="text-primary mb-2 fw-bold">
                                                                {{ $template->item_name }}
                                                            </h6>
                                                            @if ($template->standar_condition)
                                                                <div class="condition-badge mb-3">
                                                                    <span class="badge bg-info-soft text-info px-3 py-2">
                                                                        <i class="fas fa-info-circle me-1"></i>
                                                                        {{ $template->standar_condition }}
                                                                    </span>
                                                                </div>
                                                            @endif

                                                            @if ($previousDetail)
                                                                <div class="previous-status mt-2">
                                                                    <small class="text-muted"><strong>Previous
                                                                            Status:</strong></small><br>
                                                                    <span
                                                                        class="badge bg-{{ $previousDetail->status === 'OK' ? 'success' : ($previousDetail->status === 'NG' ? 'danger' : 'secondary') }}">
                                                                        {{ $previousDetail->status }}
                                                                    </span>
                                                                    @if ($previousDetail->picture && \Storage::disk('public')->exists($previousDetail->picture))
                                                                        <br><small class="text-muted mt-1">
                                                                            <a href="{{ url('storage/' . $previousDetail->picture) }}"
                                                                                target="_blank"
                                                                                class="text-decoration-none">
                                                                                <i class="fas fa-image me-1"></i>View
                                                                                Previous
                                                                                Photo
                                                                            </a>
                                                                        </small>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Status Selection -->
                                                    <div class="col-lg-3 col-md-6">
                                                        <label class="form-label fw-bold text-muted">
                                                            Status <span class="text-danger">*</span>
                                                            @if ($isPreviousNG || $isRecheck)
                                                                <small class="text-warning"><i class="fas fa-star"></i>
                                                                    Priority Item</small>
                                                            @endif
                                                        </label>
                                                        <div class="status-buttons mt-2">
                                                            <div class="btn-group-vertical w-100" role="group">
                                                                <input type="radio" class="btn-check"
                                                                    name="items[{{ $loop->index }}][status]"
                                                                    value="OK" id="ok-{{ $loop->index }}" required>
                                                                <label class="btn btn-outline-success mb-2 py-2"
                                                                    for="ok-{{ $loop->index }}">
                                                                    <i class="fas fa-check-circle me-2"></i>OK
                                                                    @if ($isPreviousNG || $isRecheck)
                                                                        <span
                                                                            class="badge bg-success text-white ms-1">Fixed</span>
                                                                    @endif
                                                                </label>

                                                                <input type="radio" class="btn-check"
                                                                    name="items[{{ $loop->index }}][status]"
                                                                    value="NG" id="ng-{{ $loop->index }}" required>
                                                                <label class="btn btn-outline-danger mb-2 py-2"
                                                                    for="ng-{{ $loop->index }}">
                                                                    <i class="fas fa-times-circle me-2"></i>NG
                                                                    @if ($isPreviousNG || $isRecheck)
                                                                        <span class="badge bg-danger text-white ms-1">Still
                                                                            Issue</span>
                                                                    @endif
                                                                </label>

                                                                <input type="radio" class="btn-check"
                                                                    name="items[{{ $loop->index }}][status]"
                                                                    value="NA" id="na-{{ $loop->index }}" required>
                                                                <label class="btn btn-outline-secondary py-2"
                                                                    for="na-{{ $loop->index }}">
                                                                    <i class="fas fa-minus-circle me-2"></i>N/A
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <input type="hidden"
                                                            name="items[{{ $loop->index }}][checksheet_id]"
                                                            value="{{ $template->id }}">
                                                    </div>

                                                    <!-- Photo Upload -->
                                                    <div class="col-lg-3 col-md-6">
                                                        <label class="form-label fw-bold text-muted">
                                                            Upload Photo <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="upload-container mt-2">
                                                            <!-- Mobile Camera Options -->
                                                            <div class="mobile-camera-options mb-2">
                                                                <div class="btn-group w-100" role="group">
                                                                    <button type="button"
                                                                        class="btn btn-outline-primary btn-sm"
                                                                        onclick="openCamera({{ $loop->index }})">
                                                                        <i class="fas fa-camera me-1"></i>Camera
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-outline-secondary btn-sm"
                                                                        onclick="openGallery({{ $loop->index }})">
                                                                        <i class="fas fa-folder me-1"></i>Gallery
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <div class="upload-area border border-2 border-dashed rounded p-3 text-center"
                                                                ondrop="handleDrop(event, {{ $loop->index }})"
                                                                ondragover="handleDragOver(event)"
                                                                onclick="handleUploadClick({{ $loop->index }})">
                                                                <i
                                                                    class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                                                <p class="mb-2 text-muted small">
                                                                    <strong class="d-none d-md-inline">Click to
                                                                        upload</strong>
                                                                    <strong class="d-md-none">Choose Photo</strong>
                                                                    <span class="d-none d-md-inline"> or drag and
                                                                        drop</span>
                                                                </p>
                                                                <p class="mb-0 text-muted small">
                                                                    Max 10MB (JPEG, PNG, JPG)<br>
                                                                    <small class="text-success">⚡ Auto-compressed to
                                                                        800KB</small>
                                                                </p>
                                                            </div>

                                                            <!-- Hidden file inputs for different capture modes -->
                                                            <input type="file" class="form-control d-none"
                                                                id="photo-{{ $loop->index }}"
                                                                name="items[{{ $loop->index }}][picture]"
                                                                accept="image/jpeg,image/png,image/jpg"
                                                                onchange="previewImage(this, {{ $loop->index }})">

                                                            <!-- Camera input for mobile -->
                                                            <input type="file" class="form-control d-none"
                                                                id="camera-{{ $loop->index }}" accept="image/*"
                                                                capture="environment"
                                                                onchange="handleCameraCapture(this, {{ $loop->index }})">

                                                            <!-- Gallery input for mobile -->
                                                            <input type="file" class="form-control d-none"
                                                                id="gallery-{{ $loop->index }}"
                                                                accept="image/jpeg,image/png,image/jpg"
                                                                onchange="handleGalleryCapture(this, {{ $loop->index }})">

                                                            <div id="preview-{{ $loop->index }}" class="mt-2 d-none">
                                                                <img id="preview-img-{{ $loop->index }}"
                                                                    class="img-thumbnail"
                                                                    style="max-width: 100px; max-height: 100px;">
                                                                <div class="mt-2">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-success me-1"
                                                                        onclick="showImagePreview({{ $loop->index }})">
                                                                        <i class="fas fa-eye"></i> View
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-danger"
                                                                        onclick="removeImage({{ $loop->index }})">
                                                                        <i class="fas fa-trash"></i> Remove
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    <!-- Notes Section -->
                                    <div class="form-group mt-4">
                                        <label for="notes" class="form-label fw-bold">
                                            @if ($isRecheck)
                                                Re-inspection Notes
                                            @else
                                                Additional Notes
                                            @endif
                                        </label>
                                        <textarea class="form-control" id="notes" name="notes" rows="4"
                                            placeholder="@if ($isRecheck) Describe any corrective actions taken, findings, or additional observations...@elseAdd any additional observations or notes... @endif"></textarea>
                                        @if ($isRecheck)
                                            <small class="form-text text-muted">
                                                Please provide details about any fixes or improvements made since the last
                                                inspection.
                                            </small>
                                        @endif
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="form-group mt-4 text-center">
                                        <button type="button" class="btn btn-outline-secondary me-2"
                                            onclick="window.location.href='{{ route('sisca-v2.checksheets.index') }}'">
                                            <i class="fas fa-arrow-left me-2"></i>Cancel
                                        </button>
                                        @if ($isRecheck && $previousInspection)
                                            <button type="button" class="btn btn-outline-info me-2"
                                                onclick="window.open('{{ route('sisca-v2.checksheets.show', $previousInspection->id) }}', '_blank')">
                                                <i class="fas fa-eye me-2"></i>View Previous Report
                                            </button>
                                        @endif
                                        <button type="submit"
                                            class="btn {{ $isRecheck ? 'btn-warning' : 'btn-primary' }}">
                                            <i class="fas fa-{{ $isRecheck ? 'redo' : 'save' }} me-2"></i>
                                            Submit {{ $isRecheck ? 'Re-inspection' : 'Inspection' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            @else
                <!-- Cannot Inspect - Show Previous Inspection -->
                @if ($periodRestriction['existing_inspection'])
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-history me-2"></i>Previous Inspection Results
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>Inspection Date:</strong>
                                            {{ $periodRestriction['existing_inspection']->inspection_date->format('d/m/Y') }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Inspector:</strong>
                                            {{ $periodRestriction['existing_inspection']->user->name }}
                                        </div>
                                    </div>

                                    @if ($periodRestriction['existing_inspection']->notes)
                                        <div class="mb-3">
                                            <strong>Notes:</strong>
                                            <p class="mt-1">{{ $periodRestriction['existing_inspection']->notes }}</p>
                                        </div>
                                    @endif

                                    <div class="text-center">
                                        <a href="{{ route('sisca-v2.checksheets.show', $periodRestriction['existing_inspection']->id) }}"
                                            class="btn btn-primary">
                                            <i class="fas fa-eye me-2"></i>View Full Inspection Report
                                        </a>
                                        <button type="button" class="btn btn-outline-danger ms-2"
                                            onclick="window.location.href='{{ route('sisca-v2.checksheets.index') }}'">
                                            <i class="fas fa-arrow-left me-2"></i>Back to Scanner
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        <!-- Image Modal -->
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel">Standard Picture</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img id="modalImage" src="" alt="" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('styles')
        <style>
            .inspection-item {
                transition: all 0.3s ease;
            }

            .inspection-item:hover {
                transform: translateY(-2px);
            }

            .ng-item {
                animation: pulseWarning 2s infinite;
            }

            @keyframes pulseWarning {
                0% {
                    box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4);
                }

                70% {
                    box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
                }

                100% {
                    box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
                }
            }

            .standard-picture {
                transition: transform 0.2s ease;
                border-radius: 8px;
            }

            .standard-picture:hover {
                transform: scale(1.05);
            }

            .no-image-placeholder {
                border-radius: 8px;
            }

            .bg-info-soft {
                background-color: rgba(13, 202, 240, 0.1) !important;
            }

            .upload-area {
                transition: all 0.3s ease;
                background-color: #f8f9fa;
                min-height: 120px;
                cursor: pointer;
            }

            .upload-area:hover {
                background-color: #e9ecef;
                border-color: #0d6efd !important;
            }

            .upload-area.drag-over {
                background-color: rgba(13, 110, 253, 0.1);
                border-color: #0d6efd !important;
            }

            .mobile-camera-options {
                border-radius: 6px;
                overflow: hidden;
            }

            .mobile-camera-options .btn {
                border-radius: 0;
                font-weight: 600;
                padding: 8px 12px;
            }

            .mobile-camera-options .btn:first-child {
                border-top-left-radius: 6px;
                border-bottom-left-radius: 6px;
            }

            .mobile-camera-options .btn:last-child {
                border-top-right-radius: 6px;
                border-bottom-right-radius: 6px;
            }

            .mobile-camera-options .btn-outline-primary:hover {
                background-color: #0d6efd;
                border-color: #0d6efd;
                color: white;
            }

            .mobile-camera-options .btn-outline-secondary:hover {
                background-color: #6c757d;
                border-color: #6c757d;
                color: white;
            }

            .compression-loading {
                padding: 20px;
                background-color: rgba(13, 110, 253, 0.05);
                border-radius: 8px;
                border: 2px dashed rgba(13, 110, 253, 0.3);
            }

            .compression-loading .spinner-border-sm {
                width: 1.5rem;
                height: 1.5rem;
            }

            .upload-area small {
                font-size: 0.75rem;
                color: #6c757d;
            }

            .status-buttons .btn {
                font-weight: 600;
                border-width: 2px;
            }

            .btn-check:checked+.btn-outline-success {
                background-color: #198754;
                border-color: #198754;
                color: white;
            }

            .btn-check:checked+.btn-outline-danger {
                background-color: #dc3545;
                border-color: #dc3545;
                color: white;
            }

            .btn-check:checked+.btn-outline-secondary {
                background-color: #6c757d;
                border-color: #6c757d;
                color: white;
            }

            .inspection-item.has-error {
                border-color: #dc3545 !important;
                box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
            }

            .previous-status {
                padding: 8px;
                background-color: #f8f9fa;
                border-radius: 6px;
                border-left: 3px solid #dee2e6;
            }

            @media (max-width: 768px) {
                .inspection-item .row>div {
                    margin-bottom: 1rem;
                }

                .status-buttons .btn-group-vertical {
                    flex-direction: row;
                }

                .status-buttons .btn {
                    flex: 1;
                    margin-right: 5px;
                    margin-bottom: 0;
                    font-size: 14px;
                    padding: 8px 4px;
                }

                .status-buttons .btn:last-child {
                    margin-right: 0;
                }

                .upload-area {
                    min-height: 80px;
                    padding: 15px !important;
                }

                .upload-area p {
                    font-size: 12px !important;
                    margin-bottom: 8px !important;
                }

                .upload-area i {
                    font-size: 1.5rem !important;
                    margin-bottom: 8px !important;
                }

                .mobile-camera-options {
                    margin-bottom: 10px !important;
                }

                .card-body {
                    padding: 15px;
                }

                .inspection-item .col-lg-3 {
                    margin-bottom: 15px;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Form validation
                const form = document.getElementById('checksheetForm');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        let allFieldsCompleted = true;
                        const inspectionItems = form.querySelectorAll('.inspection-item');
                        let ngItemsWithoutStatus = [];

                        inspectionItems.forEach((item, index) => {
                            const radioButtons = item.querySelectorAll('input[type="radio"]');
                            const fileInput = item.querySelector(
                                'input[name*="[picture]"]'); // Main file input
                            const hasSelectedStatus = Array.from(radioButtons).some(radio => radio
                                .checked);
                            const hasUploadedFile = fileInput && fileInput.files.length > 0;
                            const isNGItem = item.classList.contains('ng-item');

                            // Remove previous error state
                            item.classList.remove('has-error');

                            if (!hasSelectedStatus || !hasUploadedFile) {
                                allFieldsCompleted = false;
                                item.classList.add('has-error');

                                if (isNGItem) {
                                    ngItemsWithoutStatus.push(index + 1);
                                }

                                // Scroll to first error
                                if (allFieldsCompleted === false) {
                                    item.scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'center'
                                    });
                                }
                            }
                        });

                        if (!allFieldsCompleted) {
                            e.preventDefault();
                            let errorMessage =
                                'Please complete all inspection items (status and photo) before submitting.';
                            if (ngItemsWithoutStatus.length > 0) {
                                errorMessage += ' Priority NG items missing: ' + ngItemsWithoutStatus.join(
                                    ', ');
                            }
                            showAlert('error', errorMessage);
                            return false;
                        }

                        // Confirm submission
                        const confirmMessage =
                            @if ($isRecheck)
                                'Are you sure you want to submit this re-inspection? This action cannot be undone.'
                            @else
                                'Are you sure you want to submit this inspection? This action cannot be undone.'
                            @endif ;

                        if (!confirm(confirmMessage)) {
                            e.preventDefault();
                            return false;
                        }
                    });

                    // Remove error highlight when fields are completed
                    form.querySelectorAll('input[type="radio"]').forEach(radio => {
                        radio.addEventListener('change', function() {
                            const item = this.closest('.inspection-item');
                            const fileInput = item.querySelector('input[name*="[picture]"]');

                            if (fileInput && fileInput.files.length > 0) {
                                item.classList.remove('has-error');
                            }
                        });
                    });
                }

                // Auto-hide alerts
                setTimeout(function() {
                    const alerts = document.querySelectorAll('.alert');
                    alerts.forEach(function(alert) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    });
                }, 10000);

                @if ($isRecheck)
                    // Focus on NG items on page load for recheck
                    const ngItems = document.querySelectorAll('.ng-item');
                    if (ngItems.length > 0) {
                        // Add a visual indicator
                        ngItems.forEach(item => {
                            item.style.borderWidth = '2px';
                        });

                        // Scroll to first NG item
                        setTimeout(() => {
                            ngItems[0].scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }, 500);
                    }
                @endif
            });

            // Image compression function
            function compressImage(file, maxSizeKB = 800, quality = 0.7) {
                return new Promise((resolve) => {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    const img = new Image();

                    img.onload = function() {
                        // Calculate new dimensions while maintaining aspect ratio
                        let {
                            width,
                            height
                        } = calculateDimensions(img.width, img.height);

                        canvas.width = width;
                        canvas.height = height;

                        // Draw and compress
                        ctx.drawImage(img, 0, 0, width, height);

                        // Convert to blob with compression
                        canvas.toBlob(function(blob) {
                            // If still too large, reduce quality further
                            if (blob.size > maxSizeKB * 1024 && quality > 0.1) {
                                // Recursive compression with lower quality
                                compressImage(file, maxSizeKB, quality - 0.1).then(resolve);
                            } else {
                                // Create new file object
                                const compressedFile = new File([blob], file.name, {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                });
                                resolve(compressedFile);
                            }
                        }, 'image/jpeg', quality);
                    };

                    img.src = URL.createObjectURL(file);
                });
            }

            // Calculate optimal dimensions for compression
            function calculateDimensions(width, height, maxWidth = 1920, maxHeight = 1080) {
                if (width <= maxWidth && height <= maxHeight) {
                    return {
                        width,
                        height
                    };
                }

                const ratio = Math.min(maxWidth / width, maxHeight / height);
                return {
                    width: Math.round(width * ratio),
                    height: Math.round(height * ratio)
                };
            }

            // Show compression loading indicator
            function showCompressionLoading(uploadArea, show = true) {
                const loadingHtml = `
                    <div class="compression-loading text-center">
                        <div class="spinner-border spinner-border-sm text-primary mb-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mb-0">Mengompres gambar...</p>
                    </div>
                `;

                if (show) {
                    uploadArea.innerHTML = loadingHtml;
                } else {
                    // Restore original upload area content
                    uploadArea.innerHTML = `
                        <div class="text-center">
                            <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-1">Click to upload or drag and drop</p>
                            <small class="text-muted">JPEG, PNG, JPG (Max: 10MB → Compressed to 800KB)</small>
                        </div>
                    `;
                }
            }

            // Enhanced image preview function with compression
            function previewImage(input, index) {
                const file = input.files[0];
                const previewContainer = document.getElementById(`preview-${index}`);
                const previewImg = document.getElementById(`preview-img-${index}`);
                const uploadArea = input.closest('.upload-container').querySelector('.upload-area');

                if (file) {
                    // Validate file size (10MB limit before compression)
                    if (file.size > 10 * 1024 * 1024) {
                        showAlert('error', 'File size must be less than 10MB.');
                        input.value = '';
                        return;
                    }

                    // Validate file type
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                    if (!allowedTypes.includes(file.type)) {
                        showAlert('error', 'Only JPEG, PNG, and JPG files are allowed.');
                        input.value = '';
                        return;
                    }

                    // Show compression loading
                    showCompressionLoading(uploadArea, true);

                    // Compress image before preview
                    compressImage(file, 800, 0.7).then(compressedFile => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.src = e.target.result;
                            previewContainer.classList.remove('d-none');
                            uploadArea.style.display = 'none';

                            // Update the main file input with the compressed file
                            const mainInput = document.getElementById(`photo-${index}`);
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(compressedFile);
                            mainInput.files = dataTransfer.files;

                            // Show compression info
                            const originalSize = SISCA.formatFileSize ? SISCA.formatFileSize(file.size) : (file
                                .size / 1024).toFixed(1) + 'KB';
                            const compressedSize = SISCA.formatFileSize ? SISCA.formatFileSize(compressedFile
                                .size) : (compressedFile.size / 1024).toFixed(1) + 'KB';
                            showAlert('success',
                                `Gambar berhasil dikompres: ${originalSize} → ${compressedSize}`
                            );

                            // Remove error state
                            const item = input.closest('.inspection-item');
                            const hasSelectedStatus = item.querySelector('input[type="radio"]:checked');
                            if (hasSelectedStatus) {
                                item.classList.remove('has-error');
                            }
                        };
                        reader.readAsDataURL(compressedFile);
                    }).catch(error => {
                        console.error('Compression failed:', error);
                        showAlert('error', 'Gagal mengompres gambar. Silakan coba lagi.');
                        showCompressionLoading(uploadArea, false);
                        input.value = '';
                    });
                }
            }

            // Mobile-specific functions
            function isMobileDevice() {
                return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            }

            function handleUploadClick(index) {
                if (isMobileDevice()) {
                    // On mobile, show option buttons (they are already visible)
                    return;
                } else {
                    // On desktop, directly open file picker
                    document.getElementById(`photo-${index}`).click();
                }
            }

            function openCamera(index) {
                const cameraInput = document.getElementById(`camera-${index}`);
                cameraInput.click();
            }

            function openGallery(index) {
                const galleryInput = document.getElementById(`gallery-${index}`);
                galleryInput.click();
            }

            function handleCameraCapture(input, index) {
                previewImage(input, index);
            }

            function handleGalleryCapture(input, index) {
                previewImage(input, index);
            }

            function showImagePreview(index) {
                const previewImg = document.getElementById(`preview-img-${index}`);
                if (previewImg && previewImg.src) {
                    showImageModal(previewImg.src, `Inspection Photo - Item ${index + 1}`);
                }
            }

            // Remove image function
            function removeImage(index) {
                const input = document.getElementById(`photo-${index}`);
                const previewContainer = document.getElementById(`preview-${index}`);
                const uploadArea = input.closest('.upload-container').querySelector('.upload-area');

                input.value = '';
                previewContainer.classList.add('d-none');
                uploadArea.style.display = 'block';
            }

            // Drag and drop functions
            function handleDragOver(e) {
                e.preventDefault();
                e.currentTarget.classList.add('drag-over');
            }

            function handleDrop(e, index) {
                e.preventDefault();
                e.currentTarget.classList.remove('drag-over');

                const files = e.dataTransfer.files;
                const input = document.getElementById(`photo-${index}`);

                if (files.length > 0) {
                    input.files = files;
                    previewImage(input, index);
                }
            }

            // Remove drag-over class when leaving
            document.addEventListener('dragleave', function(e) {
                if (e.target.classList.contains('upload-area')) {
                    e.target.classList.remove('drag-over');
                }
            });

            // Show image modal
            function showImageModal(src, title) {
                const modal = new bootstrap.Modal(document.getElementById('imageModal'));
                document.getElementById('modalImage').src = src;
                document.getElementById('imageModalLabel').textContent = title || 'Standard Picture';
                modal.show();
            }

            // Alert function
            function showAlert(type, message) {
                const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
                const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'check-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

                // Remove existing alerts
                const existingAlerts = document.querySelectorAll('.alert:not(.mb-4)');
                existingAlerts.forEach(alert => alert.remove());

                // Add new alert to top of form
                const form = document.getElementById('checksheetForm');
                if (form) {
                    form.insertAdjacentHTML('beforebegin', alertHtml);

                    // Auto dismiss after 8 seconds
                    setTimeout(() => {
                        const alert = document.querySelector('.alert:not(.mb-4)');
                        if (alert) {
                            alert.remove();
                        }
                    }, 8000);
                }
            }
        </script>
    @endpush
