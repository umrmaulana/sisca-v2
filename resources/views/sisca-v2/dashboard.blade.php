@extends('sisca-v2.layouts.app')

@section('title', 'Dashboard - SISCA V2')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tachometer-alt text-primary"></i> Dashboard SISCA V2
            </h1>
            <p class="text-muted mb-0">Welcome back, {{ auth('sisca-v2')->user()->name }}!</p>
        </div>

        <!-- Filter Section -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('sisca-v2.dashboard') }}" id="filterForm">
                    <div class="row g-3">
                        @if (in_array($userRole, ['Admin', 'Management']))
                            <!-- Plant Filter -->
                            <div class="col-lg-3">
                                <label for="plant_id" class="form-label">Plant</label>
                                <select class="form-select" id="plant_id" name="plant_id" onchange="loadAreas()">
                                    <option value="">All Plants</option>
                                    @foreach ($plants as $plant)
                                        <option value="{{ $plant->id }}"
                                            {{ $selectedPlantId == $plant->id ? 'selected' : '' }}>
                                            {{ $plant->plant_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Equipment Type Filter -->
                        <div class="col-lg-3">
                            <label for="equipment_type_id" class="form-label">Equipment Type</label>
                            <select class="form-select" id="equipment_type_id" name="equipment_type_id"
                                onchange="loadAreasWithEquipmentType()">
                                <option value="">All Equipment Types</option>
                                @foreach ($equipmentTypes as $type)
                                    <option value="{{ $type->id }}"
                                        {{ $selectedEquipmentTypeId == $type->id ? 'selected' : '' }}>
                                        {{ $type->equipment_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Area Filter -->
                        <div class="col-lg-2">
                            <label for="area_id" class="form-label">Area</label>
                            <select class="form-select" id="area_id" name="area_id" onchange="submitFilter()">
                                <option value="">All Areas</option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}"
                                        {{ $selectedAreaId == $area->id ? 'selected' : '' }}>
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
                    </div>
                </form>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <!-- NG Items Analysis -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="card-title m-0 font-weight-bold">
                            <i class="fas fa-exclamation-triangle"></i> NG Items Analysis
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="ngItemsChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Equipment Type Distribution Chart -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="card-title m-0 font-weight-bold">
                            <i class="fas fa-chart-bar"></i> Equipment Type Distribution
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="equipmentTypeChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            @if (in_array($userRole, ['Admin', 'Management']))
                <!-- Plant-wise Performance Chart -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="card-title m-0 font-weight-bold">
                                <i class="fas fa-chart-bar"></i> Plant-wise Performance
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="plantPerformanceChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Area-wise Performance Chart -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="card-title m-0 font-weight-bold">
                            <i class="fas fa-chart-line"></i> Area-wise Performance
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="areaPerformanceChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Monthly Trend by Equipment Type Chart -->
            <div class="col-lg-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="card-title m-0 font-weight-bold">
                            <i class="fas fa-chart-line"></i> Monthly Inspection Trends by Equipment Type
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyTrendChart" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Equipment Type Status Charts -->
        @if ($monthlyStatusByEquipmentType->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="text-gray-800 mb-0">
                            <i class="fas fa-chart-bar text-primary"></i> Monthly Inspection Status by Equipment Type
                        </h4>
                        <div class="text-muted small">
                            <i class="fas fa-info-circle"></i> Shows OK vs NG status for each equipment type over the last
                            12 months
                        </div>
                    </div>
                </div>

                @foreach ($monthlyStatusByEquipmentType as $equipmentData)
                    <div class="col-lg-6 col-xl-4 mb-4">
                        <div class="card shadow h-100">
                            <div class="card-header py-3 bg-gradient-primary">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-title m-0 font-weight-bold">
                                        <i class="fas fa-cogs"></i> {{ $equipmentData['equipment_type']->equipment_name }}
                                        - {{ $equipmentData['equipment_type']->equipment_type }}
                                    </h6>
                                    <span class="badge badge-light">
                                        <i class="fas fa-list"></i> {{ $equipmentData['total_equipment'] }} units
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div style="position: relative; height: 300px;">
                                    <canvas id="equipmentChart{{ $loop->index }}"></canvas>
                                </div>
                                <div class="mt-3 text-center">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-success font-weight-bold">
                                                <i class="fas fa-square"></i> OK Status
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-danger font-weight-bold">
                                                <i class="fas fa-square"></i> NG Status
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No equipment data available for the current filters. Please
                        adjust your filter settings.
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Prepare chart data using equipmentSummaryAll for complete data
        const chartData = {
            equipmentTypeData: {
                labels: [
                    @foreach ($equipmentSummaryAll->groupBy('equipment_type') as $type => $items)
                        '{{ $type }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Equipment Count',
                    data: [
                        @foreach ($equipmentSummaryAll->groupBy('equipment_type') as $type => $items)
                            {{ $items->count() }},
                        @endforeach
                    ],
                    backgroundColor: [
                        '#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8',
                        '#6f42c1', '#e83e8c', '#fd7e14', '#20c997', '#6c757d'
                    ],
                    borderWidth: 1
                }]
            },
            @if (in_array($userRole, ['Admin', 'Management']))
                plantData: {
                    labels: [
                        @foreach ($equipmentSummaryAll->groupBy('plant') as $plant => $items)
                            '{{ $plant }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Approved',
                        data: [
                            @foreach ($equipmentSummaryAll->groupBy('plant') as $plant => $items)
                                {{ $items->where('status', 'approved')->count() }},
                            @endforeach
                        ],
                        backgroundColor: '#28a745'
                    }, {
                        label: 'Pending',
                        data: [
                            @foreach ($equipmentSummaryAll->groupBy('plant') as $plant => $items)
                                {{ $items->where('status', 'pending')->count() }},
                            @endforeach
                        ],
                        backgroundColor: '#ffc107'
                    }, {
                        label: 'Rejected',
                        data: [
                            @foreach ($equipmentSummaryAll->groupBy('plant') as $plant => $items)
                                {{ $items->where('status', 'rejected')->count() }},
                            @endforeach
                        ],
                        backgroundColor: '#dc3545'
                    }, {
                        label: 'Not Inspected',
                        data: [
                            @foreach ($equipmentSummaryAll->groupBy('plant') as $plant => $items)
                                {{ $items->where('status', 'not_inspected')->count() }},
                            @endforeach
                        ],
                        backgroundColor: '#6c757d'
                    }]
                },
            @endif
            areaData: {
                labels: [
                    @foreach ($equipmentSummaryAll->groupBy('area')->take(10) as $area => $items)
                        '{{ $area }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Approved',
                    data: [
                        @foreach ($equipmentSummaryAll->groupBy('area')->take(10) as $area => $items)
                            {{ $items->where('status', 'approved')->count() }},
                        @endforeach
                    ],
                    backgroundColor: '#28a745'
                }, {
                    label: 'Pending',
                    data: [
                        @foreach ($equipmentSummaryAll->groupBy('area')->take(10) as $area => $items)
                            {{ $items->where('status', 'pending')->count() }},
                        @endforeach
                    ],
                    backgroundColor: '#ffc107'
                }, {
                    label: 'Rejected',
                    data: [
                        @foreach ($equipmentSummaryAll->groupBy('area')->take(10) as $area => $items)
                            {{ $items->where('status', 'rejected')->count() }},
                        @endforeach
                    ],
                    backgroundColor: '#dc3545'
                }, {
                    label: 'Not Inspected',
                    data: [
                        @foreach ($equipmentSummaryAll->groupBy('area')->take(10) as $area => $items)
                            {{ $items->where('status', 'not_inspected')->count() }},
                        @endforeach
                    ],
                    backgroundColor: '#6c757d'
                }]
            },
            ngItemsData: {
                labels: ['No NG Items', 'With NG Items'],
                datasets: [{
                    data: [
                        {{ $equipmentSummaryAll->where('ng_count', 0)->count() }},
                        {{ $equipmentSummaryAll->where('ng_count', '>', 0)->count() }}
                    ],
                    backgroundColor: ['#28a745', '#dc3545'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            }
        };

        // Equipment Type Bar Chart
        const equipmentTypeCtx = document.getElementById('equipmentTypeChart').getContext('2d');
        new Chart(equipmentTypeCtx, {
            type: 'bar',
            data: chartData.equipmentTypeData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        @if (in_array($userRole, ['Admin', 'Management']))
            // Plant Performance Chart
            const plantCtx = document.getElementById('plantPerformanceChart').getContext('2d');
            new Chart(plantCtx, {
                type: 'bar',
                data: chartData.plantData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true
                        }
                    }
                }
            });
        @endif

        // Area Performance Chart
        const areaCtx = document.getElementById('areaPerformanceChart').getContext('2d');
        new Chart(areaCtx, {
            type: 'bar',
            data: chartData.areaData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true
                    }
                }
            }
        });

        // Monthly Trend Chart by Equipment Type
        const monthlyTrendCtx = document.getElementById('monthlyTrendChart').getContext('2d');

        const monthlyTrendData = {
            labels: [
                @foreach ($monthlyTrendsByEquipmentType['trends'] as $trend)
                    '{{ $trend['month'] }}',
                @endforeach
            ],
            datasets: [
                @php $colorIndex = 0; @endphp
                @foreach ($monthlyTrendsByEquipmentType['equipmentTypes'] as $equipmentType)
                    @php
                        $colors = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6f42c1', '#e83e8c', '#fd7e14', '#20c997', '#6c757d'];
                        $color = $colors[$colorIndex % count($colors)];
                        $colorIndex++;
                    @endphp {
                        label: '{{ $equipmentType->equipment_name }}',
                        data: [
                            @foreach ($monthlyTrendsByEquipmentType['trends'] as $trend)
                                {{ $trend[$equipmentType->equipment_name] ?? 0 }},
                            @endforeach
                        ],
                        borderColor: '{{ $color }}',
                        backgroundColor: '{{ $color }}33',
                        tension: 0.3,
                        fill: false
                    },
                @endforeach
            ]
        };

        new Chart(monthlyTrendCtx, {
            type: 'line',
            data: monthlyTrendData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });

        // NG Items Chart
        const ngItemsCtx = document.getElementById('ngItemsChart').getContext('2d');
        new Chart(ngItemsCtx, {
            type: 'doughnut',
            data: chartData.ngItemsData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Equipment Type Status Charts
        @foreach ($monthlyStatusByEquipmentType as $index => $equipmentData)
            const equipmentChart{{ $index }}Ctx = document.getElementById('equipmentChart{{ $index }}')
                .getContext('2d');

            // Destroy existing chart if it exists
            if (Chart.getChart(equipmentChart{{ $index }}Ctx)) {
                Chart.getChart(equipmentChart{{ $index }}Ctx).destroy();
            }

            const equipmentChart{{ $index }} = new Chart(equipmentChart{{ $index }}Ctx, {
                type: 'bar',
                data: {
                    labels: [
                        @foreach ($equipmentData['monthly_data'] as $monthData)
                            '{{ $monthData['month'] }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'OK',
                        data: [
                            @foreach ($equipmentData['monthly_data'] as $monthData)
                                {{ $monthData['ok_count'] }},
                            @endforeach
                        ],
                        backgroundColor: '#28a745',
                        borderColor: '#28a745',
                        borderWidth: 1,
                        maxBarThickness: 30
                    }, {
                        label: 'NG',
                        data: [
                            @foreach ($equipmentData['monthly_data'] as $monthData)
                                {{ $monthData['ng_count'] }},
                            @endforeach
                        ],
                        backgroundColor: '#dc3545',
                        borderColor: '#dc3545',
                        borderWidth: 1,
                        maxBarThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 10
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [2, 2],
                                color: '#e3e6f0'
                            },
                            ticks: {
                                stepSize: 1,
                                precision: 0,
                                font: {
                                    size: 10
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            callbacks: {
                                title: function(tooltipItems) {
                                    return 'Month: ' + tooltipItems[0].label;
                                },
                                afterBody: function(tooltipItems) {
                                    const dataIndex = tooltipItems[0].dataIndex;
                                    const monthData = {!! json_encode($equipmentData['monthly_data']) !!}[dataIndex];
                                    return [
                                        `Total Equipment: ${monthData.total_equipment}`,
                                        `Inspected: ${monthData.inspected_count}`
                                    ];
                                }
                            }
                        }
                    }
                }
            });
        @endforeach

        // Load areas when plant changes (for Admin/Management)
        function loadAreas() {
            const plantId = document.getElementById('plant_id')?.value;
            const equipmentTypeId = document.getElementById('equipment_type_id')?.value;

            if (plantId) {
                loadAreasByPlantAndType(plantId, equipmentTypeId);
            }
        }

        // Load areas when plant changes (for Admin/Management)
        function loadAreas() {
            const plantId = document.getElementById('plant_id')?.value;
            const equipmentTypeId = document.getElementById('equipment_type_id')?.value;

            if (plantId) {
                loadAreasByPlantAndType(plantId, equipmentTypeId);
            }
        }

        // Load areas when equipment type changes
        function loadAreasWithEquipmentType() {
            @if (in_array($userRole, ['Admin', 'Management']))
                const plantId = document.getElementById('plant_id')?.value;
            @else
                const plantId = '{{ $user->plant_id ?? '' }}';
            @endif
            const equipmentTypeId = document.getElementById('equipment_type_id')?.value;

            // Reset area selection when equipment type changes
            const areaSelect = document.getElementById('area_id');
            if (areaSelect) {
                areaSelect.value = '';
            }

            if (plantId) {
                loadAreasByPlantAndType(plantId, equipmentTypeId);
            }
        }

        // Generic function to load areas based on plant and equipment type
        function loadAreasByPlantAndType(plantId, equipmentTypeId = '') {
            const areaSelect = document.getElementById('area_id');

            if (!areaSelect) {
                console.warn('Area select element not found');
                return;
            }

            // Clear current options and show loading state
            areaSelect.innerHTML = '<option value="">Loading areas...</option>';
            areaSelect.disabled = true;

            if (plantId) {
                let url = `${window.location.origin}/sisca-v2/dashboard/areas-by-plant?plant_id=${plantId}`;
                if (equipmentTypeId) {
                    url += `&equipment_type_id=${equipmentTypeId}`;
                }

                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Areas response:', data);

                        // Reset select
                        areaSelect.innerHTML = '<option value="">All Areas</option>';
                        areaSelect.disabled = false;

                        // Handle different response formats
                        let areas = [];
                        if (Array.isArray(data)) {
                            areas = data;
                        } else if (data && Array.isArray(data.areas)) {
                            areas = data.areas;
                        } else if (data && typeof data === 'object') {
                            areas = Object.values(data).filter(item => item && item.id && item.area_name);
                        }

                        if (areas.length === 0 && equipmentTypeId) {
                            const option = document.createElement('option');
                            option.value = '';
                            option.textContent = 'No areas available for selected equipment type';
                            option.disabled = true;
                            areaSelect.appendChild(option);
                        } else if (areas.length > 0) {
                            areas.forEach(area => {
                                if (area && area.id && area.area_name) {
                                    const option = document.createElement('option');
                                    option.value = area.id;
                                    option.textContent = area.area_name;
                                    areaSelect.appendChild(option);
                                }
                            });
                        } else {
                            console.warn('No valid areas found in response:', data);
                            const option = document.createElement('option');
                            option.value = '';
                            option.textContent = areas.length === 0 ? 'No areas available' :
                                'Error loading areas - invalid data format';
                            option.disabled = true;
                            areaSelect.appendChild(option);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading areas:', error);
                        areaSelect.innerHTML = '<option value="">All Areas</option>';
                        areaSelect.disabled = false;

                        const errorOption = document.createElement('option');
                        errorOption.value = '';
                        errorOption.textContent = 'Failed to load areas - please try again';
                        errorOption.disabled = true;
                        areaSelect.appendChild(errorOption);
                    });
            } else {
                areaSelect.innerHTML = '<option value="">All Areas</option>';
                areaSelect.disabled = false;
            }
        }

        // Submit filter form
        function submitFilter() {
            document.getElementById('filterForm').submit();
        }

        // Initialize area loading on page load
        document.addEventListener('DOMContentLoaded', function() {
            @if (in_array($userRole, ['Admin', 'Management']))
                const initialPlantId = document.getElementById('plant_id')?.value;
            @else
                const initialPlantId = '{{ $user->plant_id ?? '' }}';
            @endif
            const initialEquipmentTypeId = document.getElementById('equipment_type_id')?.value;

            if (initialPlantId) {
                setTimeout(() => {
                    loadAreasByPlantAndType(initialPlantId, initialEquipmentTypeId);
                }, 100);
            }
        });
    </script>
@endsection
