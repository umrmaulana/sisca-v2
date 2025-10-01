@extends('layouts.app')

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
                <form method="GET" action="{{ route('mapping-area.index') }}" id="filterForm">
                    <div class="row g-3">
                        @if (in_array($userRole, ['Admin', 'Management']))
                            <!-- Company Filter (Admin & Management only) -->
                            <div class="col-lg-3">
                                <label for="company_id" class="form-label">Company</label>
                                <select class="form-select" id="company_id" name="company_id" onchange="loadAreas()">
                                    <option value="">Select Company</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}"
                                            {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Area Filter (combined with All Area option) -->
                            <div class="col-lg-3">
                                <label for="area_id" class="form-label">Area</label>
                                <select class="form-select" id="area_id" name="area_id" onchange="submitFilter()">
                                    <option value="">Select Area</option>
                                    @if (request('company_id') && $areas->count() > 0)
                                        <option value="all" {{ request('area_id') == 'all' ? 'selected' : '' }}>
                                            All Areas (Company View)
                                        </option>
                                        @foreach ($areas as $area)
                                            <option value="{{ $area->id }}"
                                                {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                                {{ $area->area_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        @else
                            <!-- For other roles, only show area dropdown -->
                            <div class="col-lg-3">
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
                            <a href="{{ route('mapping-area.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </a>
                        </div>
                    </div>

                    <div class="row">
                    </div>
                </form>
            </div>
        </div>

        @if (
            $selectedArea ||
                (request('view_mode') == 'company' && $selectedCompany && $mappingImage) ||
                (!$selectedArea && $selectedCompany && request('view_mode') != 'area'))
            <div class="row">
                <!-- Area Map -->
                <div class="col-xl-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="card-title m-0 font-weight-bold">
                                @if (request('area_id') == 'all' || (!request('area_id') && $selectedCompany))
                                    Company Map: {{ $selectedCompany->company_name ?? 'All Areas' }}
                                @else
                                    Area Map: {{ $selectedArea->area_name ?? 'Select Area' }}
                                    @if ($selectedCompany)
                                        - {{ $selectedCompany->company_name }}
                                    @endif
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
                                    <img src="{{ $mappingImage }}" class="img-fluid mapping-image"
                                        alt="@if (request('area_id') == 'all' || (!request('area_id') && $selectedCompany)) Company Mapping@else Area Mapping @endif"
                                        style="max-height: 100%; width: 100%; object-fit: contain;">

                                    <!-- Equipment markers would be positioned here -->
                                    @foreach ($equipments as $equipment)
                                        @php
                                            // Use company coordinates if area_id is 'all' (All Areas selected)
                                            $coordinateX = null;
                                            $coordinateY = null;

                                            if (
                                                request('area_id') == 'all' ||
                                                (!request('area_id') && $selectedCompany)
                                            ) {
                                                // For company view, use company coordinates (percentage 0-100)
                                                $coordinateX = $equipment->location->company_coordinate_x ?? null;
                                                $coordinateY = $equipment->location->company_coordinate_y ?? null;
                                            } else {
                                                // For area view, use area coordinates (decimal 0-1 converted to percentage)
                                                $coordinateX = $equipment->location->coordinate_x
                                                    ? $equipment->location->coordinate_x * 100
                                                    : null;
                                                $coordinateY = $equipment->location->coordinate_y
                                                    ? $equipment->location->coordinate_y * 100
                                                    : null;
                                            }

                                            // Debug coordinate values
                                            // dd([
                                            //     'equipment_id' => $equipment->id,
                                            //     'location' => $equipment->location,
                                            //     'area_id' => request('area_id'),
                                            //     'company_x' => $equipment->location->company_coordinate_x ?? 'null',
                                            //     'company_y' => $equipment->location->company_coordinate_y ?? 'null',
                                            //     'area_x' => $equipment->location->coordinate_x ?? 'null',
                                            //     'area_y' => $equipment->location->coordinate_y ?? 'null',
                                            //     'final_x' => $coordinateX,
                                            //     'final_y' => $coordinateY
                                            // ]);

                                        @endphp

                                        @if ($equipment->location && ($coordinateX !== null && $coordinateY !== null))
                                            <div class="equipment-marker" data-equipment-id="{{ $equipment->id }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="{{ $equipment->equipment_code }} - {{ $equipment->equipmentType->equipment_name ?? 'N/A' }} - {{ $equipment->location->area->area_name ?? 'N/A' }}"
                                                style="position: absolute; 
                                            left: {{ $coordinateX }}%; 
                                            top: {{ $coordinateY }}%; 
                                            transform: translate(-50%, -50%);
                                            z-index: 10;">
                                                @php
                                                    // Determine marker status based on overall status and check items
                                                    $overallStatus = $equipment->overall_status ?? 'unchecked';
                                                    $statusConfig = [
                                                        'ok' => [
                                                            'color' => '#28a745',
                                                            'icon' => '✓',
                                                            'class' => 'checked',
                                                        ],
                                                        'ng' => ['color' => '#dc3545', 'icon' => '✗', 'class' => 'ng'],
                                                        'unchecked' => [
                                                            'color' => '#6c757d',
                                                            'icon' => '?',
                                                            'class' => 'unchecked',
                                                        ],
                                                    ];
                                                    $config =
                                                        $statusConfig[$overallStatus] ?? $statusConfig['unchecked'];

                                                    // Create tooltip with check items details
                                                    $tooltipText =
                                                        $equipment->equipment_code .
                                                        ' - ' .
                                                        ($equipment->equipmentType->equipment_name ?? 'N/A');
                                                    $tooltipText .=
                                                        ' - ' . ($equipment->location->area->area_name ?? 'N/A');
                                                    $tooltipText .=
                                                        '\nPeriod: ' . ($equipment->periodCheck->period_check ?? 'N/A');

                                                    if (
                                                        isset($equipment->check_items_status) &&
                                                        count($equipment->check_items_status) > 0
                                                    ) {
                                                        $tooltipText .= '\nCheck Items:';
                                                        foreach ($equipment->check_items_status as $item) {
                                                            $statusSymbol =
                                                                $item['status'] == 'ok'
                                                                    ? '✓'
                                                                    : ($item['status'] == 'ng'
                                                                        ? '✗'
                                                                        : ($item['status'] == 'na'
                                                                            ? 'N/A'
                                                                            : '—'));
                                                            $tooltipText .=
                                                                '\n• ' . $item['item_name'] . ': ' . $statusSymbol;
                                                        }
                                                    }
                                                @endphp
                                                <div class="marker-icon {{ $config['class'] }}" data-bs-toggle="tooltip"
                                                    data-bs-html="true"
                                                    title="{{ str_replace('\n', '<br>', $tooltipText) }}"
                                                    style="width: 24px; height: 24px; border-radius: 50%; 
                                                background-color: {{ $config['color'] }};
                                                border: 3px solid white;
                                                cursor: pointer;
                                                box-shadow: 0 3px 6px rgba(0,0,0,0.4);
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                color: white;
                                                font-size: 12px;
                                                font-weight: bold;
                                                transition: all 0.2s ease;">
                                                    {{ $config['icon'] }}
                                                </div>
                                            </div>
                                        @else
                                            <!-- Debug missing coordinates -->
                                            @if (config('app.debug'))
                                                <!-- Equipment {{ $equipment->id }} ({{ $equipment->equipment_code }}) missing coordinates:
                                                                                                                Location: {{ $equipment->location ? 'exists' : 'missing' }}
                                                                                                                company X: {{ $equipment->location->company_coordinate_x ?? 'null' }}
                                                                                                                company Y: {{ $equipment->location->company_coordinate_y ?? 'null' }}
                                                                                                                Area X: {{ $equipment->location->coordinate_x ?? 'null' }}
                                                                                                                Area Y: {{ $equipment->location->coordinate_y ?? 'null' }}
                                                                                                                Final X: {{ $coordinateX ?? 'null' }}
                                                                                                                Final Y: {{ $coordinateY ?? 'null' }}
                                                                                                                -->
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-image-fill text-muted" style="font-size: 3rem;"></i>
                                    @if (request('area_id') == 'all' || (!request('area_id') && $selectedCompany))
                                        <p class="text-muted mt-2">No company mapping image available for
                                            {{ $selectedCompany->company_name ?? 'this company' }}</p>
                                        <small class="text-muted">Please upload a company mapping image in Company
                                            Management</small>
                                    @else
                                        <p class="text-muted mt-2">No area mapping image available for
                                            {{ $selectedArea->area_name ?? 'this area' }}</p>
                                        <small class="text-muted">Please upload an area mapping image in Area
                                            Management</small>
                                    @endif
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
                                                <th>Period</th>
                                                <th>Location</th>
                                                <th>Check Items</th>
                                                <th>Overall Status</th>
                                                <th>Last Check</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($equipments as $index => $equipment)
                                                <tr class="equipment-item" data-equipment-id="{{ $equipment->id }}"
                                                    style="cursor: pointer;">
                                                    <td>{{ $equipments->firstItem() + $index }}</td>
                                                    <td>
                                                        <div class="fw-bold text-primary">{{ $equipment->equipment_code }}
                                                        </div>
                                                        <small
                                                            class="text-muted">{{ Str::limit($equipment->desc, 30) }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info text-wrap" style="font-size: 0.7rem;">
                                                            {{ $equipment->equipmentType->equipment_name ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary" style="font-size: 0.65rem;">
                                                            {{ $equipment->periodCheck->period_check ?? 'N/A' }}
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
                                                        <div class="check-items-container" style="max-width: 200px;">
                                                            @if (isset($equipment->check_items_status) && count($equipment->check_items_status) > 0)
                                                                <div class="d-flex flex-wrap gap-1">
                                                                    @foreach ($equipment->check_items_status as $item)
                                                                        @php
                                                                            $iconClass = '';
                                                                            $colorClass = '';
                                                                            $icon = '';

                                                                            switch ($item['status']) {
                                                                                case 'ok':
                                                                                    $iconClass = 'text-success';
                                                                                    $icon = '✓';
                                                                                    break;
                                                                                case 'ng':
                                                                                    $iconClass = 'text-danger';
                                                                                    $icon = '✗';
                                                                                    break;
                                                                                case 'na':
                                                                                    $iconClass = 'text-warning';
                                                                                    $icon = 'N/A';
                                                                                    break;
                                                                                default:
                                                                                    $iconClass = 'text-muted';
                                                                                    $icon = '—';
                                                                            }
                                                                        @endphp
                                                                        <span class="check-item {{ $iconClass }}"
                                                                            title="{{ $item['item_name'] }}: {{ ucfirst($item['status']) }}"
                                                                            data-bs-toggle="tooltip"
                                                                            style="font-weight: bold; font-size: 1rem; cursor: help;">
                                                                            {{ $icon }}
                                                                        </span>
                                                                    @endforeach
                                                                </div>
                                                                <small class="text-muted mt-1 d-block">
                                                                    {{ count($equipment->check_items_status) }} items
                                                                </small>
                                                            @else
                                                                <span class="text-muted">No check items</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @php
                                                                $statusConfig = [
                                                                    'ok' => [
                                                                        'bg' => 'bg-success',
                                                                        'text' => 'All OK',
                                                                        'icon' => 'bi-check-circle',
                                                                    ],
                                                                    'ng' => [
                                                                        'bg' => 'bg-danger',
                                                                        'text' => 'Has NG',
                                                                        'icon' => 'bi-x-circle',
                                                                    ],
                                                                    'unchecked' => [
                                                                        'bg' => 'bg-secondary',
                                                                        'text' => 'Unchecked',
                                                                        'icon' => 'bi-dash-circle',
                                                                    ],
                                                                ];
                                                                $status = $equipment->overall_status ?? 'unchecked';
                                                                $config =
                                                                    $statusConfig[$status] ??
                                                                    $statusConfig['unchecked'];
                                                            @endphp
                                                            <i class="bi {{ $config['icon'] }} me-2"></i>
                                                            <span class="badge {{ $config['bg'] }}"
                                                                style="font-size: 0.65rem;">
                                                                {{ $config['text'] }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if ($equipment->is_checked && $equipment->latest_inspection)
                                                            <div class="small text-success">
                                                                <i class="bi bi-calendar-check"></i>
                                                                {{ $equipment->latest_inspection->inspection_date->format('d/m/Y') }}
                                                            </div>
                                                            @if ($equipment->latest_inspection->user)
                                                                <small class="text-muted">
                                                                    {{ $equipment->latest_inspection->user->name ?? 'Unknown' }}
                                                                </small>
                                                            @endif
                                                        @else
                                                            <div class="small text-muted">
                                                                <i class="bi bi-calendar-x"></i>
                                                                Not checked in current period
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
                                        All OK Equipment
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        @php
                                            $okCount = $equipments->where('overall_status', 'ok')->count();
                                        @endphp
                                        {{ $okCount }}
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
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        Has NG Items
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        @php
                                            $ngCount = $equipments->where('overall_status', 'ng')->count();
                                        @endphp
                                        {{ $ngCount }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-times-circle fa-2x text-gray-300"></i>
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
                                        @php
                                            $uncheckedCount = $equipments
                                                ->where('overall_status', 'unchecked')
                                                ->count();
                                        @endphp
                                        {{ $uncheckedCount }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-question-circle fa-2x text-gray-300"></i>
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
                                        @php
                                            $checkedCount = $equipments->where('is_checked', true)->count();
                                            $completionRate =
                                                $equipments->count() > 0
                                                    ? round(($checkedCount / $equipments->count()) * 100, 1)
                                                    : 0;
                                        @endphp
                                        {{ $completionRate }}%
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
                    @if (in_array($userRole, ['Admin', 'Management']))
                        @if (!$selectedCompany)
                            <h5 class="mt-3 text-muted">Select Company to View Mapping</h5>
                            <p class="text-muted">Please select a Company first to view either the complete company mapping
                                or
                                specific area mapping.</p>
                        @else
                            <h5 class="mt-3 text-muted">Select View Mode or Area</h5>
                            <p class="text-muted">
                                Choose "Whole Company" view to see the complete company mapping,
                                or select a specific area for detailed area mapping.
                            </p>
                        @endif
                    @else
                        @if (request('view_mode') == 'company')
                            <h5 class="mt-3 text-muted">Company View Available</h5>
                            <p class="text-muted">View the complete company mapping with all equipment positioned according
                                to company coordinates.</p>
                        @else
                            <h5 class="mt-3 text-muted">Select Area</h5>
                            <p class="text-muted">Please select an area to view the area mapping and equipment status.</p>
                        @endif
                    @endif
                </div>
            </div>
        @endif
    </div>

    @push('styles')
        <style>
            .mapping-container {
                background: #f8f9fa;
                border-radius: 8px;
                padding: 10px;
                overflow: hidden;
            }

            .mapping-image {
                max-width: 100%;
                height: auto;
                display: block;
            }

            .equipment-marker {
                cursor: pointer;
                position: absolute;
                z-index: 10;
            }

            .equipment-marker:hover .marker-icon {
                transform: scale(1.3);
                transition: transform 0.2s ease;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
            }

            .marker-icon {
                transition: all 0.2s ease;
            }

            .equipment-item:hover {
                cursor: pointer;
                background-color: rgba(0, 123, 255, 0.1);
            }

            .equipment-item.highlighted {
                border-left: 4px solid #2196f3 !important;
                background-color: rgba(33, 150, 243, 0.1);
            }

            .status-indicator {
                display: inline-block;
            }

            /* Improved marker visibility */
            .equipment-marker .marker-icon.checked {
                background-color: #28a745 !important;
                border: 3px solid #ffffff;
                box-shadow: 0 3px 8px rgba(40, 167, 69, 0.5);
            }

            .equipment-marker .marker-icon.ng {
                background-color: #dc3545 !important;
                border: 3px solid #ffffff;
                box-shadow: 0 3px 8px rgba(220, 53, 69, 0.5);
            }

            .equipment-marker .marker-icon.unchecked {
                background-color: #6c757d !important;
                border: 3px solid #ffffff;
                box-shadow: 0 3px 8px rgba(108, 117, 125, 0.5);
            }

            /* Check items styling */
            .check-items-container .check-item {
                display: inline-block;
                margin-right: 2px;
                padding: 2px 4px;
                border-radius: 3px;
                background-color: rgba(0, 0, 0, 0.05);
            }

            .check-items-container .text-success {
                background-color: rgba(40, 167, 69, 0.1);
            }

            .check-items-container .text-danger {
                background-color: rgba(220, 53, 69, 0.1);
            }

            .check-items-container .text-warning {
                background-color: rgba(255, 193, 7, 0.1);
            }

            .highlighted .marker-icon {
                animation: pulse 1s infinite;
                transform: scale(1.3) !important;
                box-shadow: 0 0 0 4px rgba(33, 150, 243, 0.3);
            }

            .highlighted.equipment-item {
                border-left: 4px solid #2196f3 !important;
            }

            @keyframes pulse {
                0% {
                    box-shadow: 0 0 0 0 rgba(33, 150, 243, 0.7), 0 2px 6px rgba(0, 0, 0, 0.3);
                }

                70% {
                    box-shadow: 0 0 0 10px rgba(33, 150, 243, 0), 0 2px 6px rgba(0, 0, 0, 0.3);
                }

                100% {
                    box-shadow: 0 0 0 0 rgba(33, 150, 243, 0), 0 2px 6px rgba(0, 0, 0, 0.3);
                }
            }
        </style>
    @endpush
    @push('scripts')
        <script>
            // Load areas when company changes (for Admin/Management)
            function loadAreas() {
                const companyId = document.getElementById('company_id').value;
                const equipmentTypeId = document.getElementById('equipment_type_id').value;

                loadAreasByCompanyAndType(companyId, equipmentTypeId);
            }

            // Load areas when equipment type changes
            function loadAreasWithEquipmentType() {
                @if (in_array($userRole, ['Admin', 'Management']))
                    const companyId = document.getElementById('company_id').value;
                @else
                    const companyId = '{{ $user->company_id ?? '' }}';
                @endif
                const equipmentTypeId = document.getElementById('equipment_type_id').value;

                // Reset area selection when equipment type changes
                document.getElementById('area_id').value = '';

                loadAreasByCompanyAndType(companyId, equipmentTypeId);
            }

            // Generic function to load areas based on company and equipment type
            function loadAreasByCompanyAndType(companyId, equipmentTypeId = '') {
                const areaSelect = document.getElementById('area_id');

                // Clear current options
                areaSelect.innerHTML = '<option value="">Select Area</option>';

                if (companyId) {
                    let url = `${window.location.origin}/mapping-area/areas-by-company?company_id=${companyId}`;
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
                            } else if (data.length > 0) {
                                // Add "All Areas" option for company view
                                const allAreasOption = document.createElement('option');
                                allAreasOption.value = 'all';
                                allAreasOption.textContent = 'All Areas (Company View)';
                                areaSelect.appendChild(allAreasOption);

                                // Add individual areas
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
                // Load areas on page load if company is already selected
                @if (in_array($userRole, ['Admin', 'Management']))
                    const initialCompanyId = document.getElementById('company_id').value;
                @else
                    const initialCompanyId = '{{ $user->company_id ?? '' }}';
                @endif
                const initialEquipmentTypeId = document.getElementById('equipment_type_id').value;

                if (initialCompanyId) {
                    loadAreasByCompanyAndType(initialCompanyId, initialEquipmentTypeId);
                }

                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                // Ensure mapping image is loaded and coordinates are properly positioned
                const mappingImage = document.querySelector('.mapping-image');
                if (mappingImage) {
                    mappingImage.onload = function() {
                        // Force recalculation of marker positions after image loads
                        setTimeout(function() {
                            const markers = document.querySelectorAll('.equipment-marker');
                            markers.forEach(function(marker) {
                                // Marker positions are already calculated server-side with correct percentages
                                // No additional calculation needed
                            });
                        }, 100);
                    };

                    // If image is already loaded
                    if (mappingImage.complete) {
                        mappingImage.onload();
                    }
                }

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

            // Add CSS for highlight effect - moved to main style section above
        </script>
    @endpush
@endsection
