@extends('sisca-v2.layouts.app')

@section('title', 'Mapping Area')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Mapping Area</h1>
        </div>

        <!-- Filter Section -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="card-title m-0 font-weight-bold">Filter</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('sisca-v2.mapping-area.index') }}" id="filterForm">
                    <div class="row g-3">
                        @if (in_array($userRole, ['Admin', 'Management']))
                            <!-- Plant Filter (Admin & Management only) -->
                            <div class="col-lg-3">
                                <label for="plant_id" class="form-label">Plant</label>
                                <select class="form-select" id="plant_id" name="plant_id" onchange="loadAreas()">
                                    <option value="">Select Plant</option>
                                    @foreach ($plants as $plant)
                                        <option value="{{ $plant->id }}"
                                            {{ request('plant_id') == $plant->id ? 'selected' : '' }}>
                                            {{ $plant->plant_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-lg-3">
                            <label for="equipment_type_id" class="form-label">Equipment Type</label>
                            <select class="form-select" id="equipment_type_id" name="equipment_type_id"
                                onchange="loadAreasWithEquipmentType()">
                                <option value="">All Equipment Types</option>
                                @foreach ($equipmentTypes as $type)
                                    <option value="{{ $type->id }}"
                                        {{ request('equipment_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->equipment_name }}
                                        @if ($type->equipment_type)
                                            ({{ $type->equipment_type }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Area Filter -->
                        <div class="col-lg-2">
                            <label for="area_id" class="form-label">Area</label>
                            <select class="form-select" id="area_id" name="area_id" onchange="submitFilter()">
                                <option value="">Select Area</option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}"
                                        {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                        {{ $area->area_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Month Filter -->
                        <div class="col-lg-2">
                            <label for="month" class="form-label">Month</label>
                            <select class="form-select" id="month" name="month" onchange="submitFilter()">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}"
                                        {{ $selectedMonth == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- Year Filter -->
                        <div class="col-lg-2">
                            <label for="year" class="form-label">Year</label>
                            <select class="form-select" id="year" name="year" onchange="submitFilter()">
                                @for ($i = date('Y') - 2; $i <= date('Y') + 1; $i++)
                                    <option value="{{ $i }}" {{ $selectedYear == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <!-- Status Filter -->
                        <div class="col-lg-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" onchange="submitFilter()">
                                <option value="all" {{ $selectedStatus == 'all' ? 'selected' : '' }}>All</option>
                                <option value="checked" {{ $selectedStatus == 'checked' ? 'selected' : '' }}>Checked
                                </option>
                                <option value="unchecked" {{ $selectedStatus == 'unchecked' ? 'selected' : '' }}>Unchecked
                                </option>
                            </select>
                        </div>
                        <!-- Equipment Search -->
                        <div class="col-lg-4">
                            <label for="search_equipment" class="form-label">Search Equipment</label>
                            <input type="text" class="form-control" id="search_equipment" name="search_equipment"
                                value="{{ $searchEquipment }}" placeholder="Search by equipment code or name...">
                        </div>
                        <div class="col-lg-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-search"></i> Search
                            </button>
                            <a href="{{ route('sisca-v2.mapping-area.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </a>
                        </div>
                    </div>

                    <div class="row">
                    </div>
                </form>
            </div>
        </div>

        @if ($selectedArea)
            <div class="row">
                <!-- Area Map -->
                <div class="col-xl-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="card-title m-0 font-weight-bold">
                                Area Map: {{ $selectedArea->area_name }}
                                @if ($selectedPlant)
                                    - {{ $selectedPlant->plant_name }}
                                @endif
                                @if ($selectedEquipmentType)
                                    <br><small class="text-muted">Equipment Type:
                                        {{ $selectedEquipmentType->equipment_name }}</small>
                                @endif
                            </h6>
                        </div>
                        <div class="card-body">
                            @if ($mappingImage)
                                <div class="mapping-container position-relative">
                                    <img src="{{ $mappingImage }}" class="img-fluid mapping-image" alt="Area Mapping"
                                        style="max-height: 600px; width: 100%; object-fit: contain;">

                                    <!-- Equipment markers would be positioned here -->
                                    @foreach ($equipments as $equipment)
                                        @if ($equipment->location && $equipment->location->coordinate_x && $equipment->location->coordinate_y)
                                            <div class="equipment-marker" data-equipment-id="{{ $equipment->id }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="{{ $equipment->code }} - {{ $equipment->name }}"
                                                style="position: absolute; 
                                            left: {{ $equipment->location->coordinate_x }}%; 
                                            top: {{ $equipment->location->coordinate_y }}%; 
                                            transform: translate(-50%, -50%);
                                            z-index: 10;">
                                                <div class="marker-icon {{ $equipment->is_checked ? 'checked' : 'unchecked' }}"
                                                    style="width: 20px; height: 20px; border-radius: 50%; 
                                                background-color: {{ $equipment->is_checked ? '#28a745' : '#dc3545' }};
                                                border: 2px solid white;
                                                cursor: pointer;
                                                box-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-image-fill text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2">No mapping image available for this area</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Equipment List -->
                <div class="col-xl-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="card-title m-0 font-weight-bold">
                                Equipment List
                                <span class="badge bg-primary">{{ $equipments->total() }}</span>
                            </h6>
                        </div>
                        <div class="card-body">
                            @if ($equipments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Equipment Code</th>
                                                <th>Type</th>
                                                <th>Location</th>
                                                <th>Status</th>
                                                <th>Last Check</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($equipments as $index => $equipment)
                                                <tr class="equipment-item" data-equipment-id="{{ $equipment->id }}"
                                                    style="cursor: pointer;">
                                                    <td>{{ $equipments->firstItem() + $index }}</td>
                                                    <td>
                                                        <div class="fw-bold text-primary">{{ $equipment->code }}</div>
                                                        <small
                                                            class="text-muted">{{ Str::limit($equipment->name, 30) }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info text-wrap" style="font-size: 0.7rem;">
                                                            {{ $equipment->equipmentType->equipment_name ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="fw-bold" style="font-size: 0.8rem;">
                                                            {{ $equipment->location->location_code ?? '-' }}
                                                        </div>
                                                        <small class="text-muted">
                                                            {{ $equipment->location->location_name ?? '-' }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="status-indicator {{ $equipment->is_checked ? 'checked' : 'unchecked' }}"
                                                                style="width: 10px; height: 10px; border-radius: 50%; 
                                                        background-color: {{ $equipment->is_checked ? '#28a745' : '#dc3545' }}; margin-right: 5px;">
                                                            </div>
                                                            <span
                                                                class="badge {{ $equipment->is_checked ? 'bg-success' : 'bg-danger' }}"
                                                                style="font-size: 0.65rem;">
                                                                {{ $equipment->is_checked ? 'Checked' : 'Unchecked' }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if ($equipment->is_checked && $equipment->latest_inspection)
                                                            <div class="small text-success">
                                                                <i class="bi bi-check-circle"></i>
                                                                {{ $equipment->latest_inspection->inspection_date->format('d/m/Y') }}
                                                            </div>
                                                            @if ($equipment->latest_inspection->inspector)
                                                                <small class="text-muted">
                                                                    {{ $equipment->latest_inspection->inspector->name ?? 'Unknown' }}
                                                                </small>
                                                            @endif
                                                        @else
                                                            <div class="small text-muted">
                                                                <i class="bi bi-x-circle"></i>
                                                                Not checked
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">No equipment found</p>
                                </div>
                            @endif

                            <!-- Pagination -->
                            @if ($equipments->hasPages())
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $equipments->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="row">
                <div class="col-xl-3 col-lg-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Equipment
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $equipments->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-tools fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Checked Equipment
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $equipments->where('is_checked', true)->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Unchecked Equipment
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $equipments->where('is_checked', false)->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Completion Rate
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $equipments->count() > 0 ? round(($equipments->where('is_checked', true)->count() / $equipments->count()) * 100, 1) : 0 }}%
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-percentage fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card shadow mb-4">
                <div class="card-body text-center py-5">
                    <i class="bi bi-geo-alt text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">Select Plant and Area</h5>
                    <p class="text-muted">Please select a plant and area to view the mapping and equipment status.</p>
                </div>
            </div>
        @endif
    </div>

    <style>
        .mapping-container {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 10px;
        }

        .equipment-marker {
            cursor: pointer;
        }

        .equipment-marker:hover .marker-icon {
            transform: scale(1.2);
            transition: transform 0.2s ease;
        }

        .equipment-item:hover {
            cursor: pointer;
        }

        .equipment-item.highlighted {
            border-left: 4px solid #2196f3 !important;
        }

        .status-indicator {
            display: inline-block;
        }
    </style>

    <script>
        // Load areas when plant changes (for Admin/Management)
        function loadAreas() {
            const plantId = document.getElementById('plant_id').value;
            const equipmentTypeId = document.getElementById('equipment_type_id').value;
            loadAreasByPlantAndType(plantId, equipmentTypeId);
        }

        // Load areas when equipment type changes
        function loadAreasWithEquipmentType() {
            @if (in_array($userRole, ['Admin', 'Management']))
                const plantId = document.getElementById('plant_id').value;
            @else
                const plantId = '{{ $user->plant_id ?? '' }}';
            @endif
            const equipmentTypeId = document.getElementById('equipment_type_id').value;

            // Reset area selection when equipment type changes
            document.getElementById('area_id').value = '';

            loadAreasByPlantAndType(plantId, equipmentTypeId);
        }

        // Generic function to load areas based on plant and equipment type
        function loadAreasByPlantAndType(plantId, equipmentTypeId = '') {
            const areaSelect = document.getElementById('area_id');

            // Clear current options
            areaSelect.innerHTML = '<option value="">Select Area</option>';

            if (plantId) {
                let url = `${window.location.origin}/sisca-v2/mapping-area/areas-by-plant?plant_id=${plantId}`;
                if (equipmentTypeId) {
                    url += `&equipment_type_id=${equipmentTypeId}`;
                }

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0 && equipmentTypeId) {
                            const option = document.createElement('option');
                            option.value = '';
                            option.textContent = 'No areas available for selected equipment type';
                            option.disabled = true;
                            areaSelect.appendChild(option);
                        } else {
                            data.forEach(area => {
                                const option = document.createElement('option');
                                option.value = area.id;
                                option.textContent = area.area_name;
                                areaSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error loading areas:', error);
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'Error loading areas';
                        option.disabled = true;
                        areaSelect.appendChild(option);
                    });
            }
        }

        // Submit filter automatically when select changes
        function submitFilter() {
            document.getElementById('filterForm').submit();
        }

        // Initialize tooltips and area loading on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Load areas on page load if plant is already selected
            @if (in_array($userRole, ['Admin', 'Management']))
                const initialPlantId = document.getElementById('plant_id').value;
            @else
                const initialPlantId = '{{ $user->plant_id ?? '' }}';
            @endif
            const initialEquipmentTypeId = document.getElementById('equipment_type_id').value;

            if (initialPlantId) {
                loadAreasByPlantAndType(initialPlantId, initialEquipmentTypeId);
            }

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Add click handlers for equipment markers and list items
            const equipmentMarkers = document.querySelectorAll('.equipment-marker');
            const equipmentItems = document.querySelectorAll('.equipment-item');

            // Highlight corresponding items when marker is clicked
            equipmentMarkers.forEach(marker => {
                marker.addEventListener('click', function() {
                    const equipmentId = this.getAttribute('data-equipment-id');
                    highlightEquipment(equipmentId);
                });
            });

            // Highlight corresponding marker when list item is clicked
            equipmentItems.forEach(item => {
                item.addEventListener('click', function() {
                    const equipmentId = this.getAttribute('data-equipment-id');
                    highlightEquipment(equipmentId);
                });
            });
        });

        function highlightEquipment(equipmentId) {
            // Remove previous highlights
            document.querySelectorAll('.equipment-marker, .equipment-item').forEach(el => {
                el.classList.remove('highlighted');
            });

            // Add highlight to selected equipment
            document.querySelectorAll(`[data-equipment-id="${equipmentId}"]`).forEach(el => {
                el.classList.add('highlighted');

                // Scroll to equipment item if it's in the table
                if (el.classList.contains('equipment-item')) {
                    // Scroll within the table container
                    const tableContainer = el.closest('.table-responsive');
                    if (tableContainer) {
                        const containerTop = tableContainer.scrollTop;
                        const containerBottom = containerTop + tableContainer.clientHeight;
                        const elementTop = el.offsetTop;
                        const elementBottom = elementTop + el.offsetHeight;

                        if (elementTop < containerTop || elementBottom > containerBottom) {
                            tableContainer.scrollTop = elementTop - (tableContainer.clientHeight / 2);
                        }
                    }
                }
            });
        }

        // Add CSS for highlight effect
        const style = document.createElement('style');
        style.textContent = `
    .highlighted .marker-icon {
        animation: pulse 1s infinite;
        transform: scale(1.3) !important;
    }
    
    .highlighted.equipment-item {
        border-left: 4px solid #2196f3 !important;
    }
    
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(33, 150, 243, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(33, 150, 243, 0); }
        100% { box-shadow: 0 0 0 0 rgba(33, 150, 243, 0); }
    }
`;
        document.head.appendChild(style);
    </script>
@endsection
