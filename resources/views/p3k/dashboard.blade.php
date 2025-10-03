@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="container">
        {{-- Summary --}}
        <div class="row mb-2 g-3">
            <!-- Low Stock -->
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card shadow text-white text-center"
                    style="background-color: {{ $lowStockCount > 0 ? '#c0392b' : '#27ae60' }};">
                    <div class="card-body">
                        <div class="fs-4 fw-bold">{{ $lowStockCount }}</div>
                        <div class="fw-semibold small">Low Stock</div>
                    </div>
                </div>
            </div>

            <!-- Expired Stock -->
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card shadow text-white text-center"
                    style="background-color: {{ $expiredStockCount > 0 ? '#f39c12' : '#27ae60' }};">
                    <div class="card-body">
                        <div class="fs-4 fw-bold">{{ $expiredStockCount }}</div>
                        <div class="fw-semibold small">Expired Stock</div>
                    </div>
                </div>
            </div>

            <!-- Accidents -->
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card shadow text-white text-center"
                    style="background-color: {{ count($accidents) > 0 ? 'rgba(139, 0, 0)' : '#27ae60' }};">
                    <div class="card-body">
                        <div class="fs-4 fw-bold">{{ count($accidents) }}</div>
                        <div class="fw-semibold small">Accidents</div>
                    </div>
                </div>
            </div>
        </div>


        {{-- Stock --}}
        {{-- Low Stock --}}
        <div class="row mb-2 g-3 align-items-stretch">
            <div class="mt-4">
                <h6>Low Stock</h6>
            </div>

            {{-- Low Stock Chart --}}
            <div class="col-12 col-md-6 col-lg-4 mb-2">
                <div class="card shadow">
                    <div class="card-dashboard d-flex flex-column">
                        <div class="fw-bold mb-4">Low Stock Chart</div>
                        <canvas id="lowStockChart" style="max-height: 250px;"></canvas>
                    </div>
                </div>
            </div>

            {{-- Low Stock Table --}}
            <div class="col-12 col-md-6 col-lg-8 mb-2">
                <div class="card shadow">
                    <div class="card-dashboard d-flex flex-column">
                        <div class="fw-bold mb-4">Low Stock Information</div>
                        <div class="table-responsive flex-grow-1">
                            <table class="table table-sm table-hover text-center align-middle" id="table-dashboard">
                                <thead class="table-dark small">
                                    <tr>
                                        <th>#</th>
                                        <th class="text-start">Item</th>
                                        <th>Tag Number</th>
                                        <th>Standard</th>
                                        <th>Actual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lowStock as $stock)
                                        <tr>
                                            <td data-label="No">{{ $loop->iteration }}</td>
                                            <td class="text-start" data-label="Item">{{ $stock->item }}</td>
                                            <td data-label="Tag Number">{{ $stock->tag_number ?? '-' }}</td>
                                            <td data-label="Standard">{{ $stock->standard_stock ?? '-' }}</td>
                                            <td data-label="Actual" class="text-danger fw-bold">{{ $stock->actual_stock }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Expired Stock --}}
        <div class="row mb-2 g-3 align-item-stretch">
            <div class="mt-4">
                <h6>Expired Stock</h6>
            </div>

            {{-- Expired Stock Chart --}}
            <div class="col-12 col-md-6 col-lg-4 mb-2">
                <div class="card shadow">
                    <div class="card-dashboard d-flex flex-column">
                        <div class="fw-bold mb-4">Expired Stock Chart</div>
                        <canvas id="expiredStockChart" style="max-height: 250px;"></canvas>
                    </div>
                </div>
            </div>
            {{-- Expired Stock Table --}}
            <div class="col-12 col-md-6 col-lg-8 mb-2">
                <div class="card shadow">
                    <div class="card-dashboard d-flex flex-column">
                        <div class="fw-bold mb-4">Expired Stock Information</div>
                        <div class="table-responsive flex-grow-1">
                            <table class="table table-sm table-hover text-center align-middle" id="table-dashboard">
                                <thead class="small">
                                    <tr>
                                        <th>#</th>
                                        <th>Item</th>
                                        <th>Tag Number</th>
                                        <th>Expired</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expiredStock as $stock)
                                        <tr>
                                            <td data-label="No">{{ $loop->iteration }}</td>
                                            <td data-label="Item" class="text-start">{{ $stock->item }}</td>
                                            <td data-label="Tag Number">{{ $stock->tag_number ?? '-' }}</td>
                                            <td data-label="Expired" class="text-danger">{{ $stock->expired_at }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- Accident --}}
        <div class="row mb-2 g-3 align-item-stretch">
            <div class="mt-4">
                <h6>Accident</h6>
            </div>

            {{-- Line Chart --}}
            <div class="col-12 col-md-6 mb-2">
                <div class="card shadow">
                    <div class="card-dashboard d-flex flex-column">
                        <div class="fw-bold mb-2">Accident Line Chart ({{ $year }})</div>
                        <div class="flex-grow-1">
                            <canvas id="accidentChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table with Filter --}}
            <div class="col-12 col-md-6 mb-2">
                <div class="card shadow">
                    <div class="card-dashboard d-flex flex-column">
                        <div class="fw-bold mb-4">Accident Records</div>

                        {{-- Filter Form --}}
                        <form method="GET" class="row g-2 mb-3">
                            <div class="col-12 col-lg-5">
                                <select name="year" class="form-select">
                                    @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-12 col-lg-5">
                                <select name="month" class="form-select">
                                    <option value="">All Months</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-12 col-lg-2">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </form>

                        {{-- Table --}}
                        <div class="table-responsive flex-grow-1">
                            <table class="table table-sm table-hover text-center align-middle" id="table-dashboard">
                                <thead class="small">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Accident</th>
                                        <th>Location</th>
                                        <th>Department</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($accidents as $accident)
                                        <tr>
                                            <td data-label="No">{{ $loop->iteration }}</td>
                                            <td data-label="Date">
                                                {{ \Carbon\Carbon::parse($accident->created_at)->format('d M Y') }}</td>
                                            <td data-label="Accident">
                                                @if ($accident->accident_id && $accident->masterAccident)
                                                    {{ $accident->masterAccident->name }}
                                                @elseif ($accident->accident_other)
                                                    {{ $accident->accident_other }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td data-label="Location">{{ $accident->location->location }}</td>
                                            <td data-label="Department">{{ $accident->department->name ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Chart Script --}}
    <script>
        const lowStockCtx = document.getElementById('lowStockChart').getContext('2d');
        new Chart(lowStockCtx, {
            type: 'doughnut',
            data: {
                labels: ['Low Stock', 'In Stock'],
                datasets: [{
                    data: [{{ $lowStockCount }}, {{ max(1, 105 - $lowStockCount) }}],
                    backgroundColor: [
                        'rgba(192, 57, 43)', // Deep Red
                        'rgba(39, 174, 96)', // Emerald Green
                    ],
                    hoverOffset: 3
                }]
            },
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

        const expiredStockCtx = document.getElementById('expiredStockChart').getContext('2d');
        new Chart(expiredStockCtx, {
            type: 'doughnut',
            data: {
                labels: ['Expired', 'Valid'],
                datasets: [{
                    data: [{{ $expiredStockCount }}, {{ max(1, 105 - $expiredStockCount) }}],
                    backgroundColor: [
                        'rgba(251, 191, 36, 0.7)', // soft amber
                        'rgba(52, 152, 219, 0.8)', // soft indigo

                    ],
                    hoverOffset: 3
                }]
            },
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

        const accidentCtx = document.getElementById('accidentChart').getContext('2d');
        new Chart(accidentCtx, {
            type: 'line',
            data: {
                labels: [
                    'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                ],
                datasets: [{
                    label: 'Accidents',
                    data: @json(array_values($chartData)),
                    borderColor: 'rgba(139, 0, 0, 0.8)', // wine red
                    backgroundColor: 'rgba(139, 0, 0, 0.3)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
