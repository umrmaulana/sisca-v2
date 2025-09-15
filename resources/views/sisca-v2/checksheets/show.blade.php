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
                                    <img src="{{ asset('storage/' . $inspection->equipment->qrcode) }}" alt="QR Code"
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
                                    <div class="col-md-1">
                                        <div class="text-center">
                                            <span class="badge bg-secondary fs-6">
                                                {{ $detail->checksheetTemplate->order_number }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <h6 class="text-primary mb-2">{{ $detail->checksheetTemplate->item_name }}</h6>
                                        @if ($detail->checksheetTemplate->standar_condition)
                                            <p class="text-muted mb-2">
                                                <strong>Standard:</strong>
                                                {{ $detail->checksheetTemplate->standar_condition }}
                                            </p>
                                        @endif
                                        @if (
                                            $detail->checksheetTemplate->standar_picture &&
                                                \Storage::disk('public')->exists($detail->checksheetTemplate->standar_picture))
                                            <div class="mb-2">
                                                <strong>Reference:</strong><br>
                                                <img src="{{ asset('storage/' . $detail->checksheetTemplate->standar_picture) }}"
                                                    alt="Standard Picture" class="img-thumbnail" style="max-width: 120px;">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-2">
                                        <strong>Status:</strong><br>
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
                                    <div class="col-md-4">
                                        @if ($detail->picture && \Storage::disk('public')->exists($detail->picture))
                                            <strong>Evidence Photo:</strong><br>
                                            <img src="{{ asset('storage/' . $detail->picture) }}" alt="Evidence Photo"
                                                class="img-thumbnail" style="max-width: 150px;">
                                            <div class="mt-1">
                                                <a href="{{ asset('storage/' . $detail->picture) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-expand me-1"></i>View Full Size
                                                </a>
                                            </div>
                                        @else
                                            <span class="text-muted">No photo evidence</span>
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

            .card {
                border: 1px solid #dee2e6 !important;
                page-break-inside: avoid;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
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
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
@endpush
