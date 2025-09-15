@extends('sisca-v2.layouts.app')

@section('title', 'Equipment Details')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Equipment Details</h3>
                <p class="text-muted mb-0">{{ $equipment->equipment_code }}</p>
            </div>
            <div class="d-flex gap-2">
                @can('update', $equipment)
                    <a href="{{ route('sisca-v2.equipments.edit', $equipment) }}" class="btn btn-outline-warning">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                @endcan
                <a href="{{ route('sisca-v2.equipments.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to List
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Equipment Information -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>Equipment Information
                            </h5>
                            @if ($equipment->is_active)
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>Active
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="fas fa-times-circle me-1"></i>Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label text-muted fw-bold">Equipment Code</label>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded p-2">
                                            <i class="fas fa-cog fs-4"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 text-primary">{{ $equipment->equipment_code }}</h5>
                                        <small class="text-muted">Equipment Identifier</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label text-muted fw-bold">Equipment Type</label>
                                <div>
                                    <h6 class="mb-1">{{ $equipment->equipmentType->equipment_name ?? 'N/A' }}</h6>
                                    <span
                                        class="badge bg-info">{{ $equipment->equipmentType->equipment_type ?? 'N/A' }}</span>
                                    @if ($equipment->equipmentType && $equipment->equipmentType->desc)
                                        <p class="text-muted small mt-1 mb-0">{{ $equipment->equipmentType->desc }}</p>
                                    @endif
                                </div>
                            </div>

                            @if ($equipment->description)
                                <div class="col-12 mb-4">
                                    <label class="form-label text-muted fw-bold">Description</label>
                                    <div class="bg-light p-3 rounded">
                                        <p class="mb-0">{{ $equipment->description }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-6 mb-4">
                                <label class="form-label text-muted fw-bold">Location</label>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-success bg-opacity-10 text-success rounded p-2">
                                            <i class="fas fa-map-marker-alt fs-4"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $equipment->location->location_code ?? 'N/A' }}</h6>
                                        @if ($equipment->location && $equipment->location->area)
                                            <small class="text-muted d-block">Area:
                                                {{ $equipment->location->area->area_name }}</small>
                                        @endif
                                        @if ($equipment->location && $equipment->location->plant)
                                            <small class="text-muted d-block">Plant:
                                                {{ $equipment->location->plant->plant_name }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label text-muted fw-bold">Period Check</label>
                                <div>
                                    @if ($equipment->periodCheck)
                                        <span class="badge bg-warning">{{ $equipment->periodCheck->period_check }}</span>
                                        <p class="text-muted small mt-1 mb-0">Maintenance Schedule</p>
                                    @else
                                        <span class="text-muted">Not Set</span>
                                        <p class="text-muted small mt-1 mb-0">No maintenance schedule defined</p>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted fw-bold">Created Date</label>
                                <div>
                                    <span class="fw-semibold">{{ $equipment->created_at->format('d M Y H:i') }}</span>
                                    <p class="text-muted small mt-1 mb-0">{{ $equipment->created_at->diffForHumans() }}</p>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted fw-bold">Last Updated</label>
                                <div>
                                    <span class="fw-semibold">{{ $equipment->updated_at->format('d M Y H:i') }}</span>
                                    <p class="text-muted small mt-1 mb-0">{{ $equipment->updated_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inspection History (if exists) -->
                @if ($equipment->inspections && $equipment->inspections->count() > 0)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-history me-2"></i>Recent Inspections
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Inspector</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($equipment->inspections->take(5) as $inspection)
                                            <tr>
                                                <td>{{ $inspection->created_at->format('d M Y') }}</td>
                                                <td>
                                                    @if ($inspection->user)
                                                        <div>
                                                            <strong>{{ $inspection->user->name }}</strong>
                                                            <br>
                                                            <small class="text-muted">NPK:
                                                                {{ $inspection->user->npk }}</small>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($inspection->status === 'approved')
                                                        <span
                                                            class="badge bg-success">{{ ucfirst($inspection->status) }}</span>
                                                    @elseif ($inspection->status === 'pending')
                                                        <span
                                                            class="badge bg-warning">{{ ucfirst($inspection->status) }}</span>
                                                    @elseif ($inspection->status === 'rejected')
                                                        <span
                                                            class="badge bg-danger">{{ ucfirst($inspection->status) }}</span>
                                                    @else
                                                        <span
                                                            class="badge bg-secondary">{{ ucfirst($inspection->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('sisca-v2.inspections.show', $inspection->id) }}"
                                                        class="btn btn-sm btn-outline-info" title="View Inspection Details">
                                                        <i class="fas fa-eye me-1"></i>View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- QR Code Section -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-qrcode me-2"></i>QR Code
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        @if ($equipment->qrcode && \Storage::disk('public')->exists($equipment->qrcode))
                            <div class="mb-3">
                                <img src="{{ url('storage/' . $equipment->qrcode) }}" alt="QR Code"
                                    class="img-fluid border rounded shadow-sm" style="max-width: 250px;">
                            </div>
                            <div class="mb-3">
                                <h6 class="text-primary">{{ $equipment->equipment_code }}</h6>
                                @if ($equipment->location && $equipment->location->plant && $equipment->location->area)
                                    <small class="text-muted d-block">{{ $equipment->location->plant->plant_name }} -
                                        {{ $equipment->location->area->area_name }}</small>
                                @endif
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ url('storage/' . $equipment->qrcode) }}" target="_blank"
                                    class="btn btn-outline-primary">
                                    <i class="fas fa-external-link-alt me-1"></i>View Full Size
                                </a>
                                <a href="{{ url('storage/' . $equipment->qrcode) }}"
                                    download="QR_{{ $equipment->equipment_code }}.png" class="btn btn-outline-success">
                                    <i class="fas fa-download me-1"></i>Download QR Code
                                </a>
                                <button type="button" class="btn btn-outline-info" onclick="printQR()">
                                    <i class="fas fa-print me-1"></i>Print QR Code
                                </button>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-qrcode fa-4x text-muted mb-3"></i>
                                <h6 class="text-muted">No QR Code Available</h6>
                                <p class="text-muted small">QR Code not generated or missing</p>
                                @can('update', $equipment)
                                    <a href="{{ route('sisca-v2.equipments.edit', $equipment) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i>Edit to Regenerate
                                    </a>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bolt me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @can('update', $equipment)
                                <a href="{{ route('sisca-v2.equipments.edit', $equipment) }}"
                                    class="btn btn-outline-warning">
                                    <i class="fas fa-edit me-1"></i>Edit Equipment
                                </a>
                            @endcan

                            @if ($equipment->equipmentType)
                                <a href="{{ route('sisca-v2.equipment-types.show', $equipment->equipmentType) }}"
                                    class="btn btn-outline-info">
                                    <i class="fas fa-cogs me-1"></i>View Equipment Type
                                </a>
                            @endif

                            @if ($equipment->location)
                                <a href="{{ route('sisca-v2.locations.show', $equipment->location) }}"
                                    class="btn btn-outline-success">
                                    <i class="fas fa-map-marker-alt me-1"></i>View Location
                                </a>
                            @endif

                            <a href="{{ route('sisca-v2.equipments.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-list me-1"></i>All Equipment
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Equipment Stats -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Equipment Stats
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-primary mb-1">
                                        {{ $equipment->inspections ? $equipment->inspections->count() : 0 }}</h4>
                                    <small class="text-muted">Total Inspections</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success mb-1">{{ $equipment->created_at->diffInDays(now()) }}</h4>
                                <small class="text-muted">Days in Service</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function printQR() {
            const qrImage = document.querySelector('img[alt="QR Code"]');
            if (qrImage) {
                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
                    <html>
                        <head>
                            <title>QR Code - {{ $equipment->equipment_code }}</title>
                            <style>
                                body { 
                                    margin: 0; 
                                    padding: 20px; 
                                    text-align: center; 
                                    font-family: Arial, sans-serif; 
                                }
                                img { 
                                    max-width: 300px; 
                                    height: auto; 
                                }
                                .info {
                                    margin-top: 10px;
                                    font-size: 14px;
                                }
                            </style>
                        </head>
                        <body>
                            <img src="${qrImage.src}" alt="QR Code">
                            <div class="info">
                                <strong>{{ $equipment->equipment_code }}</strong><br>
                                @if ($equipment->location && $equipment->location->plant && $equipment->location->area)
                                    {{ $equipment->location->plant->plant_name }} - {{ $equipment->location->area->area_name }}
                                @endif
                            </div>
                        </body>
                    </html>
                `);
                printWindow.document.close();
                printWindow.print();
            }
        }
    </script>
@endpush
