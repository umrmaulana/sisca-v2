@extends('sisca-v2.layouts.app')

@section('title', 'Inspection Details')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Inspection Details</h3>
                <p class="text-muted mb-0">View completed inspection results</p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('sisca-v2.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sisca-v2.checksheets.index') }}">Checksheet</a></li>
                    <li class="breadcrumb-item active">Inspection Details</li>
                </ol>
            </nav>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Inspection Summary -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-clipboard-check me-2"></i>Inspection Summary
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Inspection ID:</strong><br>
                                <span class="text-primary fs-5">#{{ str_pad($inspection->id, 6, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Inspection Date:</strong><br>
                                <span class="text-dark">{{ $inspection->inspection_date->format('d M Y') }}</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Inspector:</strong><br>
                                <span class="text-dark">{{ $inspection->user->name ?? 'Unknown' }}</span><br>
                                <small class="text-muted">{{ $inspection->user->npk ?? '' }}</small>
                            </div>
                            <div class="col-md-3">
                                <strong>Status Summary:</strong><br>
                                @php
                                    $okCount = $inspection->details->where('status', 'OK')->count();
                                    $ngCount = $inspection->details->where('status', 'NG')->count();
                                    $naCount = $inspection->details->where('status', 'NA')->count();
                                    $totalCount = $inspection->details->count();
                                @endphp
                                <span class="badge bg-success">{{ $okCount }} OK</span>
                                <span class="badge bg-danger">{{ $ngCount }} NG</span>
                                <span class="badge bg-secondary">{{ $naCount }} N/A</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Equipment Information -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-cogs me-2"></i>Equipment Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Equipment Code:</strong><br>
                                <span class="text-primary fs-5">{{ $inspection->equipment->equipment_code }}</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Equipment Type:</strong><br>
                                <span
                                    class="badge bg-primary">{{ $inspection->equipment->equipmentType->equipment_name ?? 'N/A' }}</span><br>
                                <small
                                    class="text-muted">{{ $inspection->equipment->equipmentType->equipment_type ?? '' }}</small>
                            </div>
                            <div class="col-md-3">
                                <strong>Location:</strong><br>
                                {{ $inspection->equipment->location->location_code ?? 'N/A' }}<br>
                                <small class="text-muted">
                                    {{ $inspection->equipment->location->area->area_name ?? '' }} -
                                    {{ $inspection->equipment->location->plant->plant_name ?? '' }}
                                </small>
                            </div>
                            <div class="col-md-3">
                                <strong>QR Code:</strong><br>
                                @if ($inspection->equipment->qrcode && \Storage::disk('public')->exists($inspection->equipment->qrcode))
                                    <img src="{{ url('storage/' . $inspection->equipment->qrcode) }}" alt="QR Code"
                                        class="img-thumbnail" style="max-width: 80px;">
                                @else
                                    <span class="text-muted">No QR Code</span>
                                @endif
                            </div>
                        </div>

                        @if ($inspection->equipment->desc)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <strong>Description:</strong><br>
                                    <span class="text-muted">{{ $inspection->equipment->desc }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Inspection Details -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-list-check me-2"></i>Inspection Details
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach ($inspection->details->sortBy('checksheetTemplate.order_number') as $index => $detail)
                            <div
                                class="inspection-detail border rounded p-3 mb-3 
                                @if ($detail->status === 'OK') border-success bg-light-success
                                @elseif($detail->status === 'NG') border-danger bg-light-danger
                                @else border-secondary bg-light-secondary @endif">
                                <div class="row align-items-center">
                                    <div class="col-md-1 col-12">
                                        <div class="text-center mb-2 mb-md-0">
                                            <span class="badge bg-secondary fs-6">
                                                Item {{ $detail->checksheetTemplate->order_number ?? $index + 1 }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12 mb-2 mb-md-0">
                                        <h6 class="text-primary mb-2">{{ $detail->checksheetTemplate->item_name }}</h6>
                                        @if ($detail->checksheetTemplate->standar_condition)
                                            <div class="mb-2">
                                                <span class="badge bg-info-soft text-info px-2 py-1">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    {{ $detail->checksheetTemplate->standar_condition }}
                                                </span>
                                            </div>
                                        @endif
                                        @if (
                                            $detail->checksheetTemplate->standar_picture &&
                                                \Storage::disk('public')->exists($detail->checksheetTemplate->standar_picture))
                                            <div class="mb-2">
                                                <strong class="small text-muted d-block mb-1">Reference Picture:</strong>
                                                <img src="{{ url('storage/' . $detail->checksheetTemplate->standar_picture) }}"
                                                    alt="Standard Picture - {{ $detail->checksheetTemplate->item_name }}"
                                                    class="img-thumbnail" style="max-width: 80px; max-height: 80px;">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-2 col-6 mb-2 mb-md-0">
                                        <strong class="small text-muted d-block mb-1">Status:</strong>
                                        @if ($detail->status === 'OK')
                                            <span class="badge bg-success fs-6">
                                                <i class="fas fa-check me-1"></i>OK
                                            </span>
                                        @elseif($detail->status === 'NG')
                                            <span class="badge bg-danger fs-6">
                                                <i class="fas fa-times me-1"></i>NG
                                            </span>
                                        @else
                                            <span class="badge bg-secondary fs-6">
                                                <i class="fas fa-minus me-1"></i>N/A
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-5 col-12">
                                        @if ($detail->picture && \Storage::disk('public')->exists($detail->picture))
                                            <strong class="small text-muted d-block mb-1">Evidence Photo:</strong>
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ url('storage/' . $detail->picture) }}"
                                                    alt="Evidence Photo - {{ $detail->checksheetTemplate->item_name }}"
                                                    class="img-thumbnail" style="max-width: 120px; max-height: 120px;"
                                                    title="Click to enlarge">
                                                <div class="d-flex flex-column gap-1">
                                                    <a href="{{ url('storage/' . $detail->picture) }}" target="_blank"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-expand me-1"></i>View Full
                                                    </a>
                                                    <a href="{{ url('storage/' . $detail->picture) }}"
                                                        download="Evidence_{{ $detail->checksheetTemplate->item_name }}.jpg"
                                                        class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-download me-1"></i>Download
                                                    </a>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center text-muted py-3">
                                                <i class="fas fa-image fa-2x mb-2 opacity-50"></i>
                                                <p class="mb-0 small">No photo evidence</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Notes -->
        @if ($inspection->notes)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-sticky-note me-2"></i>Additional Notes
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $inspection->notes }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-tools me-2"></i>Actions
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>Print Report
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="shareInspection()">
                                <i class="fas fa-share me-2"></i>Share Report
                            </button>
                            @php
                                $ngCount = $inspection->details->where('status', 'NG')->count();
                            @endphp
                            @if ($ngCount > 0)
                                <a href="{{ route('sisca-v2.checksheets.index') }}?code={{ $inspection->equipment->equipment_code }}"
                                    class="btn btn-warning">
                                    <i class="fas fa-redo me-2"></i>Re-inspect Equipment
                                    <span class="badge bg-danger ms-1">{{ $ngCount }} NG Items</span>
                                </a>
                            @endif
                            <a href="{{ route('sisca-v2.checksheets.history') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-history me-2"></i>Back to History
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Modal -->
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="" alt="Full Size Image" id="modalImage" class="img-fluid rounded">
                    </div>
                    <div class="modal-footer">
                        <a href="" id="downloadLink" class="btn btn-success" download>
                            <i class="fas fa-download me-1"></i>Download
                        </a>
                        <a href="" id="openLink" target="_blank" class="btn btn-primary">
                            <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('styles')
    <style>
        .bg-light-success {
            background-color: #d4edda !important;
        }

        .bg-light-danger {
            background-color: #f8d7da !important;
        }

        .bg-light-secondary {
            background-color: #e2e3e5 !important;
        }

        .img-thumbnail {
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .img-thumbnail:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .inspection-detail {
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .inspection-detail:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .bg-light-success {
            background-color: rgba(40, 167, 69, 0.1) !important;
        }

        .bg-light-danger {
            background-color: rgba(220, 53, 69, 0.1) !important;
        }

        .bg-light-secondary {
            background-color: rgba(108, 117, 125, 0.1) !important;
        }

        .bg-info-soft {
            background-color: rgba(13, 202, 240, 0.1) !important;
            border: 1px solid rgba(13, 202, 240, 0.2);
        }

        .text-info {
            color: #0dcaf0 !important;
        }

        .img-thumbnail {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .img-thumbnail:hover {
            opacity: 0.8;
            transform: scale(1.05);
        }

        .card {
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .inspection-detail .row>div {
                margin-bottom: 1rem;
                text-align: center;
            }

            .inspection-detail .row>div:last-child {
                margin-bottom: 0;
            }

            .inspection-detail .col-md-1 {
                text-align: center;
                margin-bottom: 0.5rem;
            }

            .btn-group {
                flex-direction: column;
                gap: 0.5rem;
            }

            .btn-group .btn {
                width: 100%;
            }

            .badge {
                font-size: 0.8em !important;
            }

            .img-thumbnail {
                max-width: 100px !important;
                max-height: 100px !important;
            }

            .btn-sm {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }

            .inspection-detail {
                padding: 1rem !important;
            }
        }

        @media print {
            .card-header {
                background-color: #6c757d !important;
                -webkit-print-color-adjust: exact;
            }

            .btn,
            .breadcrumb,
            .alert {
                display: none !important;
            }

            .inspection-detail {
                page-break-inside: avoid;
                box-shadow: none !important;
                transform: none !important;
                margin-bottom: 0.5rem !important;
            }

            .btn-sm {
                display: none !important;
            }

            .img-thumbnail {
                max-width: 80px !important;
                max-height: 80px !important;
            }
        }

        /* Modal styles for image viewing */
        .modal-dialog-centered {
            display: flex;
            align-items: center;
            min-height: calc(100% - 3.5rem);
        }

        .modal-body img {
            max-width: 100%;
            height: auto;
            border-radius: 0.375rem;
        }

        .fade-in {
            opacity: 0;
            animation: fadeIn 0.3s ease-in-out forwards;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }
        }

        .card {
            border: 1px solid #dee2e6 !important;
            page-break-inside: avoid;
            margin-bottom: 1rem !important;
        }

        .inspection-detail {
            page-break-inside: avoid;
            margin-bottom: 1rem !important;
        }

        .img-thumbnail {
            max-width: 100px !important;
            max-height: 100px !important;
        }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Ensure sidebar functionality works on this page
        document.addEventListener('DOMContentLoaded', function() {
            // Force initialize sidebar if not already initialized
            if (typeof window.SISCA !== 'undefined' && window.SISCA.initializeSidebar) {
                // Re-initialize sidebar functionality
                const sidebarToggle = document.getElementById('sidebarToggle');
                if (sidebarToggle) {
                    // Remove existing event listeners and re-add
                    const newToggle = sidebarToggle.cloneNode(true);
                    sidebarToggle.parentNode.replaceChild(newToggle, sidebarToggle);

                    newToggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (window.SISCA && window.SISCA.toggleSidebar) {
                            window.SISCA.toggleSidebar();
                        }
                    });
                }
            }

            // Re-initialize menu accordions
            if (typeof window.SISCA !== 'undefined' && window.SISCA.initializeMenuAccordions) {
                window.SISCA.initializeMenuAccordions();
            }

            // Re-initialize user dropdown
            if (typeof window.SISCA !== 'undefined' && window.SISCA.initializeUserDropdown) {
                window.SISCA.initializeUserDropdown();
            }

            // Image modal functionality
            var imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
            var modalImage = document.getElementById('modalImage');
            var modalTitle = document.getElementById('imageModalLabel');
            var downloadLink = document.getElementById('downloadLink');
            var openLink = document.getElementById('openLink');

            // Add click event to all thumbnail images
            document.querySelectorAll('.img-thumbnail').forEach(function(img) {
                img.addEventListener('click', function(e) {
                    e.preventDefault();

                    var imgSrc = this.src;
                    var imgAlt = this.alt || 'Image Preview';

                    // Update modal content
                    modalImage.src = imgSrc;
                    modalImage.alt = imgAlt;
                    modalTitle.textContent = imgAlt;
                    downloadLink.href = imgSrc;
                    openLink.href = imgSrc;

                    // Set download filename
                    var filename = imgAlt.replace(/\s+/g, '_') + '.jpg';
                    downloadLink.setAttribute('download', filename);

                    // Add fade-in animation
                    modalImage.classList.add('fade-in');

                    // Show modal
                    imageModal.show();
                });
            });

            // Remove fade-in class after modal is shown
            document.getElementById('imageModal').addEventListener('shown.bs.modal', function() {
                modalImage.classList.remove('fade-in');
            });

            // Add keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // ESC key to close modal
                if (e.key === 'Escape' && imageModal._isShown) {
                    imageModal.hide();
                }

                // Ctrl+P for print
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    window.print();
                }
            });
        });

        function shareInspection() {
            const url = window.location.href;
            const title = 'Inspection Report #{{ str_pad($inspection->id, 6, '0', STR_PAD_LEFT) }}';
            const text =
                'Equipment: {{ $inspection->equipment->equipment_code }} - Inspection completed on {{ $inspection->inspection_date->format('d M Y') }}';

            if (navigator.share) {
                navigator.share({
                    title: title,
                    text: text,
                    url: url
                }).catch(console.error);
            } else {
                // Fallback: Copy to clipboard
                navigator.clipboard.writeText(url).then(() => {
                    alert('Inspection URL copied to clipboard!');
                }).catch(() => {
                    // Further fallback: Show URL
                    prompt('Copy this URL to share:', url);
                });
            }
        }

        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if (alert && typeof bootstrap !== 'undefined') {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 5000);

        // Add image click functionality for better viewing
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('.img-thumbnail');
            images.forEach(img => {
                img.style.cursor = 'pointer';
                img.addEventListener('click', function() {
                    showImageModal(this.src, this.alt);
                });
            });
        });

        function showImageModal(src, title) {
            // Create modal if not exists
            let modal = document.getElementById('imageViewModal');
            if (!modal) {
                const modalHtml = `
                    <div class="modal fade" id="imageViewModal" tabindex="-1" aria-labelledby="imageViewModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="imageViewModalLabel">Image View</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <img id="modalViewImage" src="" alt="" class="img-fluid" style="max-height: 70vh;">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <a id="downloadImageBtn" href="" download class="btn btn-primary">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                modal = document.getElementById('imageViewModal');
            }

            const modalImage = document.getElementById('modalViewImage');
            const modalTitle = document.getElementById('imageViewModalLabel');
            const downloadBtn = document.getElementById('downloadImageBtn');

            modalImage.src = src;
            modalTitle.textContent = title || 'Image View';
            downloadBtn.href = src;
            downloadBtn.download = title ? title.replace(/[^a-z0-9]/gi, '_').toLowerCase() + '.jpg' : 'image.jpg';

            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
        }
    </script>
@endpush
