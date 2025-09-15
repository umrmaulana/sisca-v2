@extends('dashboard.app')
@section('title', 'Home')
@section('page-title', 'Dashboard')

@section('content')

    <div
        class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mt-4 p-4 mb-3 col-lg-12">
        <h3>Welcome, {{ auth()->user()->name }}</h3>
        <div class="btn-group">
            <button type="button" class="btn btn-success dropdown-toggle caret-none" data-bs-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                Pilih Tahun
            </button>
            <div class="dropdown-menu">
                @foreach ($availableYears as $year)
                    <a class="dropdown-item" href="{{ route('dashboard.index', ['year' => $year]) }}">{{ $year }}</a>
                @endforeach
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="row">
            @if (auth()->user()->role === 'MTE')
                <div class="col-lg-6 mb-4">
                    <div class="card-header bg-header-table header-table">
                            <h5 class="mb-0 fw-bold">Head Crane</h5>
                        </div>
                        <div class="card-body p-4" style="background: #f8fafc;">
                            <div class="chart-container" style="height: 300px; position: relative;">
                                <canvas id="HeadCraneChart" class="img-fluid"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Grafik Apar -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-lg border-0 h-100 card-grafik">
                        <div class="card-header bg-header-table header-table">
                            <h5 class="mb-0">Apar</h5>
                            <span class="badge bg-light text-dark px-3 py-2 rounded-pill">1/M</span>
                        </div>
                        <div class="card-body p-4" style="background: #f8fafc;">
                            <div class="chart-container" style="height: 300px; position: relative;">
                                <canvas id="barChart" class="img-fluid"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HYDRANT -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-lg border-0 h-100 card-grafik">
                        <div class="card-header bg-header-table header-table">
                            <h5 class="mb-0 fw-bold">Hydrant</h5>
                            <span class="badge bg-light text-dark px-3 py-2 rounded-pill">1/M</span>
                        </div>
                        <div class="card-body p-4" style="background: #f8fafc;">
                            <div class="chart-container" style="height: 300px; position: relative;">
                                <canvas id="hydrantChart" class="img-fluid"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- NITROGEN -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-lg border-0 h-100 card-grafik">
                        <div class="card-header bg-header-table header-table">
                            <h5 class="mb-0 fw-bold">Nitrogen</h5>
                            <span class="badge bg-light text-dark px-3 py-2 rounded-pill">1/M</span>
                        </div>
                        <div class="card-body p-4" style="background: #f8fafc;">
                            <div class="chart-container" style="height: 300px; position: relative;">
                                <canvas id="nitrogenChart" class="img-fluid"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CO2 -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-lg border-0 h-100 card-grafik">
                        <div class="card-header bg-header-table header-table">
                            <h5 class="mb-0 fw-bold">CO2</h5>
                            <span class="badge bg-light text-dark px-3 py-2 rounded-pill">1/M</span>
                        </div>
                        <div class="card-body p-4" style="background: #f8fafc;">
                            <div class="chart-container" style="height: 300px; position: relative;">
                                <canvas id="co2Chart" class="img-fluid"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TANDU -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-lg border-0 h-100 card-grafik">
                        <div class="card-header bg-header-table header-table">
                            <h5 class="mb-0 fw-bold">Tandu</h5>
                            <span class="badge bg-light text-dark px-3 py-2 rounded-pill">1/M</span>
                        </div>
                        <div class="card-body p-4" style="background: #f8fafc;">
                            <div class="chart-container" style="height: 300px; position: relative;">
                                <canvas id="tanduChart" class="img-fluid"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- EYEWASHER -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-lg border-0 h-100 card-grafik">
                        <div class="card-header bg-header-table header-table">
                            <h5 class="mb-0 fw-bold">Eye Washer</h5>
                            <span class="badge bg-light text-dark px-3 py-2 rounded-pill">1/M</span>
                        </div>
                        <div class="card-body p-4" style="background: #f8fafc;">
                            <div class="chart-container" style="height: 300px; position: relative;">
                                <canvas id="eyewasherChart" class="img-fluid"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SLING -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-lg border-0 h-100 card-grafik">
                        <div class="card-header bg-header-table header-table">
                            <h5 class="mb-0 fw-bold">Sling</h5>
                            <span class="badge bg-light text-dark px-3 py-2 rounded-pill">1/3M</span>
                        </div>
                        <div class="card-body p-4" style="background: #f8fafc;">
                            <div class="chart-container" style="height: 300px; position: relative;">
                                <canvas id="slingChart" class="img-fluid"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TEMBIN -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-lg border-0 h-100 card-grafik">
                        <div class="card-header bg-header-table header-table">
                            <h5 class="mb-0 fw-bold">Tembin</h5>
                            <span class="badge bg-light text-dark px-3 py-2 rounded-pill">1/3M</span>
                        </div>
                        <div class="card-body p-4" style="background: #f8fafc;">
                            <div class="chart-container" style="height: 300px; position: relative;">
                                <canvas id="tembinChart" class="img-fluid"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CHAIN BLOCK -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-lg border-0 h-100 card-grafik">
                        <div class="card-header bg-header-table header-table">
                            <h5 class="mb-0 fw-bold">Chain Block</h5>
                            <span class="badge bg-light text-dark px-3 py-2 rounded-pill">1/3M</span>
                        </div>
                        <div class="card-body p-4" style="background: #f8fafc;">
                            <div class="chart-container" style="height: 300px; position: relative;">
                                <canvas id="chainblockChart" class="img-fluid"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BODY HARNESS -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-lg border-0 h-100 card-grafik">
                        <div class="card-header bg-header-table header-table">
                            <h5 class="mb-0 fw-bold">Body Harness</h5>
                            <span class="badge bg-light text-dark px-3 py-2 rounded-pill">1/3M</span>
                        </div>
                        <div class="card-body p-4" style="background: #f8fafc;">
                            <div class="chart-container" style="height: 300px; position: relative;">
                                <canvas id="bodyharnestChart" class="img-fluid"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SAFETY BELT -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-lg border-0 h-100 card-grafik">
                        <div class="card-header bg-header-table header-table">
                            <h5 class="mb-0 fw-bold">Safety Belt</h5>
                            <span class="badge bg-light text-dark px-3 py-2 rounded-pill">1/3M</span>
                        </div>
                        <div class="card-body p-4" style="background: #f8fafc;">
                            <div class="chart-container" style="height: 300px; position: relative;">
                                <canvas id="safetybeltChart" class="img-fluid"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FACP -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-lg border-0 h-100 card-grafik">
                        <div class="card-header bg-header-table header-table">
                                <h5 class="mb-0 fw-bold">FACP</h5>
                            </div>
                            <div class="card-body p-4" style="background: #f8fafc;">
                                <div class="chart-container" style="height: 300px; position: relative;">
                                    <canvas id="facpChart" class="img-fluid"></canvas>
                                </div>
                            </div>
                    </div>
                </div>

                <!-- HEAD CRANE -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-lg border-0 h-100 card-grafik">
                        <div class="card-header bg-header-table header-table">
                                <h5 class="mb-0 fw-bold">Head Crane</h5>
                            </div>
                            <div class="card-body p-4" style="background: #f8fafc;">
                                <div class="chart-container" style="height: 300px; position: relative;">
                                    <canvas id="HeadCraneChart" class="img-fluid"></canvas>
                                </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>


    <script>
        // Modern Chart.js Configuration with Gradients and Animations
        
        // Create gradient function
        function createGradient(ctx, color1, color2) {
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, color1);
            gradient.addColorStop(1, color2);
            return gradient;
        }

        // Modern chart options
        const modernOptions = {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6B7280',
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(107, 114, 128, 0.1)',
                        borderDash: [5, 5]
                    },
                    ticks: {
                        color: '#6B7280',
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        color: '#374151',
                        font: {
                            size: 13,
                            weight: '600'
                        },
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(17, 24, 39, 0.95)',
                    titleColor: '#F9FAFB',
                    bodyColor: '#E5E7EB',
                    borderColor: 'rgba(75, 85, 99, 0.2)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    mode: 'index',
                    intersect: false
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart'
            }
        };

        // Grafik Apar dengan styling modern
        var ctx = document.getElementById('barChart').getContext('2d');
        var okGradient = createGradient(ctx, '#10B981', '#059669');
        var ngGradient = createGradient(ctx, '#EF4444', '#DC2626');
        
        var barChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($data_Apar['labels']) !!},
                datasets: [{
                    label: 'OK',
                    data: {!! json_encode($data_Apar['okData_Apar']) !!},
                    backgroundColor: okGradient,
                    borderColor: '#059669',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }, {
                    label: 'NG',
                    data: {!! json_encode($data_Apar['notOkData_Apar']) !!},
                    backgroundColor: ngGradient,
                    borderColor: '#DC2626',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: modernOptions
        });

        // Grafik Hydrant
        var ctxHydrant = document.getElementById('hydrantChart').getContext('2d');
        var okGradientH = createGradient(ctxHydrant, '#10B981', '#059669');
        var ngGradientH = createGradient(ctxHydrant, '#EF4444', '#DC2626');
        
        var hydrantChart = new Chart(ctxHydrant, {
            type: 'bar',
            data: {
                labels: {!! json_encode($data_Hydrant['labels']) !!},
                datasets: [{
                    label: 'OK',
                    data: {!! json_encode($data_Hydrant['okData_Hydrant']) !!},
                    backgroundColor: okGradientH,
                    borderColor: '#059669',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }, {
                    label: 'NG',
                    data: {!! json_encode($data_Hydrant['notOkData_Hydrant']) !!},
                    backgroundColor: ngGradientH,
                    borderColor: '#DC2626',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: modernOptions
        });

        // Grafik Nitrogen
        var ctxNitrogen = document.getElementById('nitrogenChart').getContext('2d');
        var okGradientN = createGradient(ctxNitrogen, '#10B981', '#059669');
        var ngGradientN = createGradient(ctxNitrogen, '#EF4444', '#DC2626');
        
        var nitrogenChart = new Chart(ctxNitrogen, {
            type: 'bar',
            data: {
                labels: {!! json_encode($data_Nitrogen['labels']) !!},
                datasets: [{
                    label: 'OK',
                    data: {!! json_encode($data_Nitrogen['okData_Nitrogen']) !!},
                    backgroundColor: okGradientN,
                    borderColor: '#059669',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }, {
                    label: 'NG',
                    data: {!! json_encode($data_Nitrogen['notOkData_Nitrogen']) !!},
                    backgroundColor: ngGradientN,
                    borderColor: '#DC2626',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: modernOptions
        });

        // Grafik Co2
        var ctxCo2 = document.getElementById('co2Chart').getContext('2d');
        var okGradientC = createGradient(ctxCo2, '#10B981', '#059669');
        var ngGradientC = createGradient(ctxCo2, '#EF4444', '#DC2626');
        
        var co2Chart = new Chart(ctxCo2, {
            type: 'bar',
            data: {
                labels: {!! json_encode($data_Tabungco2['labels']) !!},
                datasets: [{
                    label: 'OK',
                    data: {!! json_encode($data_Tabungco2['okData_Tabungco2']) !!},
                    backgroundColor: okGradientC,
                    borderColor: '#059669',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }, {
                    label: 'NG',
                    data: {!! json_encode($data_Tabungco2['notOkData_Tabungco2']) !!},
                    backgroundColor: ngGradientC,
                    borderColor: '#DC2626',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: modernOptions
        });

        // Grafik Tandu
        var ctxTandu = document.getElementById('tanduChart').getContext('2d');
        var okGradientT = createGradient(ctxTandu, '#10B981', '#059669');
        var ngGradientT = createGradient(ctxTandu, '#EF4444', '#DC2626');
        
        var tanduChart = new Chart(ctxTandu, {
            type: 'bar',
            data: {
                labels: {!! json_encode($data_Tandu['labels']) !!},
                datasets: [{
                    label: 'OK',
                    data: {!! json_encode($data_Tandu['okData_Tandu']) !!},
                    backgroundColor: okGradientT,
                    borderColor: '#059669',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }, {
                    label: 'NG',
                    data: {!! json_encode($data_Tandu['notOkData_Tandu']) !!},
                    backgroundColor: ngGradientT,
                    borderColor: '#DC2626',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: modernOptions
        });

        // Grafik Eyewasher
        var ctxEyewasher = document.getElementById('eyewasherChart').getContext('2d');
        var okGradientE = createGradient(ctxEyewasher, '#10B981', '#059669');
        var ngGradientE = createGradient(ctxEyewasher, '#EF4444', '#DC2626');
        
        var eyewasherChart = new Chart(ctxEyewasher, {
            type: 'bar',
            data: {
                labels: {!! json_encode($data_Eyewasher['labels']) !!},
                datasets: [{
                    label: 'OK',
                    data: {!! json_encode($data_Eyewasher['okData_Eyewasher']) !!},
                    backgroundColor: okGradientE,
                    borderColor: '#059669',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }, {
                    label: 'NG',
                    data: {!! json_encode($data_Eyewasher['notOkData_Eyewasher']) !!},
                    backgroundColor: ngGradientE,
                    borderColor: '#DC2626',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: modernOptions
        });

        // Grafik Sling
        var ctxSling = document.getElementById('slingChart').getContext('2d');
        var okGradientS = createGradient(ctxSling, '#10B981', '#059669');
        var ngGradientS = createGradient(ctxSling, '#EF4444', '#DC2626');
        
        var slingChart = new Chart(ctxSling, {
            type: 'bar',
            data: {
                labels: {!! json_encode($data_Sling['labels']) !!},
                datasets: [{
                    label: 'OK',
                    data: {!! json_encode($data_Sling['okData_Sling']) !!},
                    backgroundColor: okGradientS,
                    borderColor: '#059669',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }, {
                    label: 'NG',
                    data: {!! json_encode($data_Sling['notOkData_Sling']) !!},
                    backgroundColor: ngGradientS,
                    borderColor: '#DC2626',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: modernOptions
        });

        // Grafik Tembin
        var ctxTembin = document.getElementById('tembinChart').getContext('2d');
        var okGradientTm = createGradient(ctxTembin, '#10B981', '#059669');
        var ngGradientTm = createGradient(ctxTembin, '#EF4444', '#DC2626');
        
        var tembinChart = new Chart(ctxTembin, {
            type: 'bar',
            data: {
                labels: {!! json_encode($data_Tembin['labels']) !!},
                datasets: [{
                    label: 'OK',
                    data: {!! json_encode($data_Tembin['okData_Tembin']) !!},
                    backgroundColor: okGradientTm,
                    borderColor: '#059669',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }, {
                    label: 'NG',
                    data: {!! json_encode($data_Tembin['notOkData_Tembin']) !!},
                    backgroundColor: ngGradientTm,
                    borderColor: '#DC2626',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: modernOptions
        });

        // Grafik Chain Block
        var ctxChainblock = document.getElementById('chainblockChart').getContext('2d');
        var okGradientCb = createGradient(ctxChainblock, '#10B981', '#059669');
        var ngGradientCb = createGradient(ctxChainblock, '#EF4444', '#DC2626');
        
        var chainblockChart = new Chart(ctxChainblock, {
            type: 'bar',
            data: {
                labels: {!! json_encode($data_Chainblock['labels']) !!},
                datasets: [{
                    label: 'OK',
                    data: {!! json_encode($data_Chainblock['okData_Chainblock']) !!},
                    backgroundColor: okGradientCb,
                    borderColor: '#059669',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }, {
                    label: 'NG',
                    data: {!! json_encode($data_Chainblock['notOkData_Chainblock']) !!},
                    backgroundColor: ngGradientCb,
                    borderColor: '#DC2626',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: modernOptions
        });

        // Grafik Body Harnest
        var ctxBodyharnest = document.getElementById('bodyharnestChart').getContext('2d');
        var okGradientBh = createGradient(ctxBodyharnest, '#10B981', '#059669');
        var ngGradientBh = createGradient(ctxBodyharnest, '#EF4444', '#DC2626');
        
        var bodyharnestChart = new Chart(ctxBodyharnest, {
            type: 'bar',
            data: {
                labels: {!! json_encode($data_Bodyharnest['labels']) !!},
                datasets: [{
                    label: 'OK',
                    data: {!! json_encode($data_Bodyharnest['okData_Bodyharnest']) !!},
                    backgroundColor: okGradientBh,
                    borderColor: '#059669',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }, {
                    label: 'NG',
                    data: {!! json_encode($data_Bodyharnest['notOkData_Bodyharnest']) !!},
                    backgroundColor: ngGradientBh,
                    borderColor: '#DC2626',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: modernOptions
        });

        // Grafik Safety Belt
        var ctxSafetybelt = document.getElementById('safetybeltChart').getContext('2d');
        var okGradientSb = createGradient(ctxSafetybelt, '#10B981', '#059669');
        var ngGradientSb = createGradient(ctxSafetybelt, '#EF4444', '#DC2626');
        
        var safetybeltChart = new Chart(ctxSafetybelt, {
            type: 'bar',
            data: {
                labels: {!! json_encode($data_Safetybelt['labels']) !!},
                datasets: [{
                    label: 'OK',
                    data: {!! json_encode($data_Safetybelt['okData_Safetybelt']) !!},
                    backgroundColor: okGradientSb,
                    borderColor: '#059669',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }, {
                    label: 'NG',
                    data: {!! json_encode($data_Safetybelt['notOkData_Safetybelt']) !!},
                    backgroundColor: ngGradientSb,
                    borderColor: '#DC2626',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: modernOptions
        });



        // Grafik FACP dengan styling modern
        var ctxFacp = document.getElementById('facpChart').getContext('2d');
        
        // Create modern gradients for FACP
        var okGradientFacp = createGradient(ctxFacp, '#10B981', '#059669');
        var ngGradientFacp = createGradient(ctxFacp, '#EF4444', '#DC2626');
        
        var modernOptionsFacp = {
            ...modernOptions,
            scales: {
                x: {
                    stacked: true,
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6B7280',
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(107, 114, 128, 0.1)',
                        borderDash: [5, 5]
                    },
                    ticks: {
                        color: '#6B7280',
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        color: '#374151',
                        font: {
                            size: 12,
                            weight: '600'
                        },
                        padding: 15,
                        generateLabels: function(chart) {
                            return [{
                                text: 'OK',
                                fillStyle: '#10B981',
                                strokeStyle: '#059669'
                            }, {
                                text: 'NG',
                                fillStyle: '#EF4444',
                                strokeStyle: '#DC2626'
                            }];
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(17, 24, 39, 0.95)',
                    titleColor: '#F9FAFB',
                    bodyColor: '#E5E7EB',
                    borderColor: 'rgba(75, 85, 99, 0.2)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    mode: 'index',
                    intersect: false
                }
            }
        };
        
        var facpChart = new Chart(ctxFacp, {
            type: 'bar',
            data: {
                labels: {!! json_encode($data_Facp['labels']) !!},
                datasets: [{
                    label: 'OK Smoke Detector',
                    data: {!! json_encode($data_Facp['okData_Smoke_detector']) !!},
                    backgroundColor: okGradientFacp,
                    borderColor: '#059669',
                    borderWidth: 2,
                    borderRadius: 4,
                    borderSkipped: false,
                    stack: 'Stack 0',
                }, {
                    label: 'NG Smoke Detector',
                    data: {!! json_encode($data_Facp['notOkData_Smoke_detector']) !!},
                    backgroundColor: ngGradientFacp,
                    borderColor: '#DC2626',
                    borderWidth: 2,
                    borderRadius: 4,
                    borderSkipped: false,
                    stack: 'Stack 0',
                }, {
                    label: 'OK Heat Detector',
                    data: {!! json_encode($data_Facp['okData_Heat_detector']) !!},
                    backgroundColor: okGradientFacp,
                    borderColor: '#059669',
                    borderWidth: 2,
                    borderRadius: 4,
                    borderSkipped: false,
                    stack: 'Stack 1',
                }, {
                    label: 'NG Heat Detector',
                    data: {!! json_encode($data_Facp['notOkData_Heat_detector']) !!},
                    backgroundColor: ngGradientFacp,
                    borderColor: '#DC2626',
                    borderWidth: 2,
                    borderRadius: 4,
                    borderSkipped: false,
                    stack: 'Stack 1',
                }, {
                    label: 'OK Beam Detector',
                    data: {!! json_encode($data_Facp['okData_Beam_detector']) !!},
                    backgroundColor: okGradientFacp,
                    borderColor: '#059669',
                    borderWidth: 2,
                    borderRadius: 4,
                    borderSkipped: false,
                    stack: 'Stack 2',
                }, {
                    label: 'NG Beam Detector',
                    data: {!! json_encode($data_Facp['notOkData_Beam_detector']) !!},
                    backgroundColor: ngGradientFacp,
                    borderColor: '#DC2626',
                    borderWidth: 2,
                    borderRadius: 4,
                    borderSkipped: false,
                    stack: 'Stack 2',
                }, {
                    label: 'OK Push Button',
                    data: {!! json_encode($data_Facp['okData_Push_button']) !!},
                    backgroundColor: okGradientFacp,
                    borderColor: '#059669',
                    borderWidth: 2,
                    borderRadius: 4,
                    borderSkipped: false,
                    stack: 'Stack 3',
                }, {
                    label: 'NG Push Button',
                    data: {!! json_encode($data_Facp['notOkData_Push_button']) !!},
                    backgroundColor: ngGradientFacp,
                    borderColor: '#DC2626',
                    borderWidth: 2,
                    borderRadius: 4,
                    borderSkipped: false,
                    stack: 'Stack 3',
                }]
            },
            options: modernOptionsFacp
        });
    </script>
    <script>
        // Grafik HeadCrane dengan styling modern
        var ctxHeadCrane = document.getElementById('HeadCraneChart').getContext('2d');
        var okGradientHc = createGradient(ctxHeadCrane, '#10B981', '#059669');
        var ngGradientHc = createGradient(ctxHeadCrane, '#EF4444', '#DC2626');
        
        var HeadCraneChart = new Chart(ctxHeadCrane, {
            type: 'bar',
            data: {
                labels: {!! json_encode($data_HeadCrane['labels']) !!},
                datasets: [{
                    label: 'OK',
                    data: {!! json_encode($data_HeadCrane['okData_HeadCrane']) !!},
                    backgroundColor: okGradientHc,
                    borderColor: '#059669',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }, {
                    label: 'NG',
                    data: {!! json_encode($data_HeadCrane['notOkData_HeadCrane']) !!},
                    backgroundColor: ngGradientHc,
                    borderColor: '#DC2626',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: modernOptions
        });
    </script>
    @if (Auth::user()->role == 'MTE')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const today = new Date().toISOString().substr(0, 10);
                document.getElementById('tanggal_pengecekan').value = today;
            });
        </script>
    @endif

@endsection
