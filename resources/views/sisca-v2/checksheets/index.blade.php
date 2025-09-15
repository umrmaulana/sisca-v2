@extends('sisca-v2.layouts.app')

@section('title', 'Checksheet Scanner')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Equipment Checksheet</h1>
                <p class="mb-0 text-muted">Scan QR code to start equipment inspection</p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('sisca-v2.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Checksheet</li>
                </ol>
            </nav>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- QR Scanner Interface -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-camera mr-2"></i>
                            Scan Equipment QR Code
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- QR Scanner -->
                        <div class="text-center mb-4">
                            <div id="qr-reader" style="width: 100%; max-width: 500px; margin: 0 auto;"></div>
                        </div>

                        <!-- Manual Input -->
                        <div class="row">
                            <div class="col-12">
                                <h6>Or enter equipment code manually:</h6>
                                <form action="{{ route('sisca-v2.checksheets.index') }}" method="GET">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="code"
                                            placeholder="Enter equipment code..." required />
                                        <div class="input-group-append d-flex justify-content-center px-3">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-search"></i>
                                                <span>
                                                    Find Equipment
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('sisca-v2.checksheets.index') }}" class="row g-3">
                    @if (request('code'))
                        <input type="hidden" name="code" value="{{ request('code') }}">
                    @endif

                    <div class="col-md-3">
                        <label for="equipment_type_id" class="form-label">Equipment Type</label>
                        <select name="equipment_type_id" id="equipment_type_id" class="form-select">
                            <option value="">All Types</option>
                            @foreach ($equipmentTypes as $type)
                                <option value="{{ $type->id }}"
                                    {{ request('equipment_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->equipment_name }} - {{ $type->equipment_type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="area_id" class="form-label">Area</label>
                        <select name="area_id" id="area_id" class="form-select"
                            @if (count($plants) > 0) disabled @endif>
                            <option value="">
                                @if (count($plants) > 0)
                                    Select plant first
                                @else
                                    All Areas
                                @endif
                            </option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}"
                                    {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                    {{ $area->area_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="month" class="form-label">Month</label>
                        <select name="month" id="month" class="form-select">
                            <option value="">All Months</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="year" class="form-label">Year</label>
                        <select name="year" id="year" class="form-select">
                            <option value="">All Years</option>
                            @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    @if (count($plants) > 0)
                        <div class="col-md-2">
                            <label for="plant_id" class="form-label">Plant</label>
                            <select name="plant_id" id="plant_id" class="form-select">
                                <option value="">All Plants</option>
                                @foreach ($plants as $plant)
                                    <option value="{{ $plant->id }}"
                                        {{ request('plant_id') == $plant->id ? 'selected' : '' }}>
                                        {{ $plant->plant_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-md-{{ count($plants) > 0 ? '12' : '2' }} d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary me-2">
                            <i class="fas fa-search me-1"></i>Search
                        </button>
                        <a href="{{ route('sisca-v2.checksheets.index') }}" class="btn btn-outline-danger">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Recent Inspections -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="card-title m-0">
                            <i class="fas fa-clipboard-list mr-2"></i>
                            Recent Inspections
                        </h6>
                        <a href="{{ route('sisca-v2.checksheets.history') }}" class="text-white btn btn-lg btn-info">
                            <i class="fas fa-history mr-2"></i>
                            View All History
                        </a>
                    </div>
                    <div class="card-body">
                        @if ($recentInspections->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle animate-in">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Equipment</th>
                                            <th>Type</th>
                                            <th>Location</th>
                                            <th>Inspector</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentInspections as $inspection)
                                            @php
                                                $hasNgItems = $inspection->details->where('status', 'NG')->count() > 0;
                                                $statusClass = $hasNgItems ? 'warning' : 'success';
                                                $statusText = $hasNgItems ? 'Issues Found' : 'Passed';
                                            @endphp
                                            <tr>
                                                <td>{{ $inspection->inspection_date->format('d/m/Y') }}</td>
                                                <td>
                                                    <strong>{{ $inspection->equipment->equipment_code }}</strong><br>
                                                    <small class="text-muted">{{ $inspection->equipment->name }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">
                                                        {{ $inspection->equipment->equipmentType->equipment_name }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div>
                                                        <small
                                                            class="text-muted">{{ $inspection->equipment->location->plant->plant_name }}</small><br>
                                                        {{ $inspection->equipment->location->area->area_name }}
                                                    </div>
                                                </td>
                                                <td>{{ $inspection->user->name }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('sisca-v2.checksheets.show', $inspection->id) }}"
                                                        class="btn btn-sm btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if ($hasNgItems)
                                                        <a href="{{ route('sisca-v2.checksheets.index', ['code' => $inspection->equipment->equipment_code]) }}"
                                                            class="btn btn-sm btn-outline-warning" title="Re-inspect">
                                                            <i class="fas fa-redo"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center">
                                {{ $recentInspections->withQueryString()->links() }}
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                                <h5 class="text-gray-600">No Inspections Found</h5>
                                <p class="text-muted">Start by scanning an equipment QR code above.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize QR Code Scanner
            function initQRScanner() {
                const html5QrCode = new Html5Qrcode("qr-reader");

                const config = {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    },
                    aspectRatio: 1.0
                };

                html5QrCode.start({
                        facingMode: "environment"
                    },
                    config,
                    (decodedText, decodedResult) => {
                        // Success callback
                        html5QrCode.stop().then(() => {
                            // Redirect to scan with the decoded equipment code
                            window.location.href =
                                '{{ route('sisca-v2.checksheets.index') }}?code=' +
                                encodeURIComponent(decodedText);
                        });
                    },
                    (errorMessage) => {
                        // Error callback - don't log every frame error
                    }
                ).catch(err => {
                    console.log("Unable to start scanning:", err);
                    // Show manual input option
                    document.getElementById('qr-reader').innerHTML =
                        '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle mr-2"></i>Camera not available. Please use manual input below.</div>';
                });
            }

            // Start scanner
            initQRScanner();

            // Auto-hide alerts
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Handle plant change for area filtering
            const plantSelect = document.getElementById('plant_id');
            const areaSelect = document.getElementById('area_id');

            if (plantSelect && areaSelect) {
                plantSelect.addEventListener('change', function() {
                    const plantId = this.value;

                    // Clear current options
                    areaSelect.innerHTML = '<option value="">Loading...</option>';
                    areaSelect.disabled = true;

                    if (plantId) {
                        // Make AJAX request to get areas
                        fetch(`{{ route('sisca-v2.checksheets.areas-by-plant') }}?plant_id=${plantId}`)
                            .then(response => response.json())
                            .then(data => {
                                areaSelect.innerHTML = '<option value="">All Areas</option>';
                                data.areas.forEach(area => {
                                    const option = document.createElement('option');
                                    option.value = area.id;
                                    option.textContent = area.area_name;
                                    if (area.id == '{{ request('area_id') }}') {
                                        option.selected = true;
                                    }
                                    areaSelect.appendChild(option);
                                });
                                areaSelect.disabled = false;
                            })
                            .catch(error => {
                                console.error('Error loading areas:', error);
                                areaSelect.innerHTML =
                                    '<option value="">Error loading areas</option>';
                                areaSelect.disabled = false;
                            });
                    } else {
                        areaSelect.innerHTML = '<option value="">Select plant first</option>';
                        areaSelect.disabled = false;
                    }
                });

                // Auto filter on page load if plant is selected
                if (plantSelect.value) {
                    plantSelect.dispatchEvent(new Event('change'));
                }
            }
        });
    </script>
@endpush
