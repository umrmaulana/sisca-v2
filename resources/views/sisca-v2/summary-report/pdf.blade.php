<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Annual Summary Report - {{ $year }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            margin: 15px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px;
            border-radius: 8px;
        }

        .header h1 {
            color: #007bff;
            font-size: 28px;
            margin: 0 0 8px 0;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header h2 {
            color: #495057;
            font-size: 18px;
            margin: 5px 0;
            font-weight: 600;
        }

        .header p {
            color: #6c757d;
            font-size: 12px;
            margin: 8px 0 0 0;
        }

        .company-info {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }

        .company-info h3 {
            color: #007bff;
            font-size: 16px;
            margin: 0 0 5px 0;
            font-weight: bold;
        }

        .company-info p {
            font-size: 10px;
            color: #6c757d;
            margin: 2px 0;
        }

        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .info-left,
        .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        .info-left {
            border-right: none;
            border-top-left-radius: 5px;
            border-bottom-left-radius: 5px;
        }

        .info-right {
            border-top-right-radius: 5px;
            border-bottom-right-radius: 5px;
        }

        .info-item {
            margin-bottom: 8px;
            display: table;
            width: 100%;
        }

        .info-label {
            font-weight: bold;
            color: #495057;
            display: table-cell;
            width: 35%;
            padding-right: 10px;
        }

        .info-value {
            display: table-cell;
            color: #007bff;
            font-weight: 600;
        }

        .summary-stats {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #007bff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .summary-stats h3 {
            margin: 0 0 15px 0;
            color: #007bff;
            font-size: 16px;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stats-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
        }

        .stats-row {
            display: table-row;
        }

        .stat-item {
            display: table-cell;
            text-align: center;
            background-color: white;
            padding: 12px 8px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
            width: 25%;
        }

        .stat-number {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        .stat-label {
            font-size: 9px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .stat-approved {
            color: #28a745;
        }

        .stat-pending {
            color: #ffc107;
        }

        .stat-rejected {
            color: #dc3545;
        }

        .stat-ng {
            color: #dc3545;
        }

        .stat-completion {
            color: #17a2b8;
        }

        .stat-types {
            color: #6f42c1;
        }

        .stat-total {
            color: #007bff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        table caption {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
            text-align: left;
            caption-side: top;
        }

        th {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-right: 1px solid rgba(255, 255, 255, 0.2);
        }

        th:last-child {
            border-right: none;
        }

        td {
            padding: 10px 8px;
            font-size: 9px;
            vertical-align: middle;
            border-bottom: 1px solid #dee2e6;
            border-right: 1px solid #f8f9fa;
        }

        td:last-child {
            border-right: none;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tr:hover {
            background-color: #e9ecef;
        }

        .status-approved {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
            border: 1px solid #c3e6cb;
        }

        .status-pending {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            color: #856404;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
            border: 1px solid #ffeaa7;
        }

        .status-rejected {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
            border: 1px solid #f5c6cb;
        }

        .status-not-inspected {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
            border: 1px solid #f5c6cb;
        }

        .ng-badge {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 4px 8px;
            border-radius: 50%;
            font-weight: bold;
            font-size: 8px;
            min-width: 20px;
            display: inline-block;
            text-align: center;
        }

        .month-cell {
            text-align: center;
            padding: 5px;
            font-size: 8px;
            font-weight: bold;
        }

        .month-ok {
            background-color: #d4edda;
            color: #155724;
        }

        .month-ng {
            background-color: #f8d7da;
            color: #721c24;
        }

        .month-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .month-not-inspected {
            background-color: #e2e3e5;
            color: #6c757d;
        }

        .section-title {
            color: #007bff;
            font-size: 16px;
            font-weight: bold;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #007bff;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #007bff;
            font-size: 10px;
            color: #6c757d;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .footer-left {
            float: left;
            width: 50%;
        }

        .footer-right {
            float: right;
            width: 50%;
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-bold {
            font-weight: bold;
        }

        .page-break {
            page-break-before: always;
        }

        .no-data {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
            font-style: italic;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <!-- Company Header -->
    <div class="company-info">
        <h3>PT. AISIN INDONESIA</h3>
        <p>Safety Inspection System for Check Activity (SISCA) V2</p>
        <p>Equipment Inspection Management System</p>
    </div>
    <!-- Header -->
    <div class="header">
        <h1>Equipment Inspection Annual Summary Report</h1>
        <h2>Year {{ $year }}</h2>
        <p><strong>Plant:</strong> {{ $plantName }} | <strong>Area:</strong> {{ $areaName }}</p>
        @if ($equipmentTypeName !== 'All Equipment Types')
            <p><strong>Equipment Type:</strong> {{ $equipmentTypeName }}</p>
        @endif
    </div>

    <!-- Report Information -->
    <div class="info-section">
        <div class="info-left">
            <div class="info-item">
                <span class="info-label">Report Period:</span>
                <span class="info-value">Annual {{ $year }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Plant:</span>
                <span class="info-value">{{ $plantName }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Area:</span>
                <span class="info-value">{{ $areaName }}</span>
            </div>
            @if ($equipmentTypeName !== 'All Equipment Types')
                <div class="info-item">
                    <span class="info-label">Equipment Type:</span>
                    <span class="info-value">{{ $equipmentTypeName }}</span>
                </div>
            @endif
        </div>
        <div class="info-right">
            <div class="info-item">
                <span class="info-label">Generated By:</span>
                <span class="info-value">{{ $generatedBy }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Generated At:</span>
                <span class="info-value">{{ $generatedAt }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Total Equipment:</span>
                <span class="info-value">{{ $equipmentSummary->count() }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Report Type:</span>
                <span class="info-value">Annual Summary</span>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="summary-stats">
        <h3>Annual Summary Statistics</h3>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-item">
                    <span class="stat-number stat-total">{{ $equipmentSummary->count() }}</span>
                    <div class="stat-label">Total Equipment</div>
                </div>
                <div class="stat-item">
                    <span
                        class="stat-number stat-approved">{{ $equipmentSummary->where('has_inspections', true)->count() }}</span>
                    <div class="stat-label">Inspected</div>
                </div>
                <div class="stat-item">
                    <span
                        class="stat-number stat-pending">{{ $equipmentSummary->where('has_inspections', false)->count() }}</span>
                    <div class="stat-label">Not Inspected</div>
                </div>
                <div class="stat-item">
                    <span
                        class="stat-number stat-types">{{ $equipmentSummary->unique('equipment_type')->count() }}</span>
                    <div class="stat-label">Equipment Types</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipment Details Table -->
    <div class="section-title">Equipment Annual Inspection Status</div>
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">Code</th>
                <th style="width: 10%;">Type</th>
                <th style="width: 12%;">Name</th>
                <th style="width: 8%;">Location</th>
                <th style="width: 6%;">Area</th>
                <th style="width: 4%;">Jan</th>
                <th style="width: 4%;">Feb</th>
                <th style="width: 4%;">Mar</th>
                <th style="width: 4%;">Apr</th>
                <th style="width: 4%;">May</th>
                <th style="width: 4%;">Jun</th>
                <th style="width: 4%;">Jul</th>
                <th style="width: 4%;">Aug</th>
                <th style="width: 4%;">Sep</th>
                <th style="width: 4%;">Oct</th>
                <th style="width: 4%;">Nov</th>
                <th style="width: 4%;">Dec</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($equipmentSummary as $equipment)
                <tr>
                    <td class="text-bold">{{ $equipment['equipment_code'] }}</td>
                    <td>{{ $equipment['equipment_type'] }}</td>
                    <td>{{ $equipment['equipment_name'] }}</td>
                    <td>{{ $equipment['location'] }}</td>
                    <td>{{ $equipment['area'] }}</td>
                    @for ($month = 1; $month <= 12; $month++)
                        @php
                            $monthData = $equipment['monthly_data'][$month - 1] ?? null;
                            $status = $monthData ? $monthData['status'] : 'not_inspected';
                            $hasNg = $monthData ? $monthData['has_ng_items'] : false;
                        @endphp
                        <td
                            class="month-cell 
                            @if ($status === 'approved' && !$hasNg) month-ok
                            @elseif ($status === 'approved' && $hasNg) month-ng
                            @elseif ($status === 'pending') month-pending
                            @else month-not-inspected @endif">
                            @if ($status === 'approved')
                                {{ $hasNg ? 'NG' : 'OK' }}
                            @elseif ($status === 'pending')
                                P
                            @else
                                -
                            @endif
                        </td>
                    @endfor
                </tr>
            @empty
                <tr>
                    <td colspan="17" class="no-data">
                        No equipment data available for the selected filters
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Legend for Monthly Status -->
    @if ($equipmentSummary->isNotEmpty())
        <div style="margin-top: 10px; padding: 10px; background-color: #f8f9fa; border-radius: 5px; font-size: 10px;">
            <strong>Legend:</strong>
            <span class="month-cell month-ok" style="margin-left: 10px; padding: 2px 8px;">OK</span> = Inspected, No
            Issues &nbsp;
            <span class="month-cell month-ng" style="padding: 2px 8px;">NG</span> = Inspected, Has Issues &nbsp;
            <span class="month-cell month-pending" style="padding: 2px 8px;">P</span> = Pending Approval &nbsp;
            <span class="month-cell month-not-inspected" style="padding: 2px 8px;">-</span> = Not Inspected
        </div>
    @endif

    <!-- Equipment Type Summary -->
    @if ($equipmentSummary->isNotEmpty())
        <div class="page-break"></div>
        <div class="section-title">Equipment Type Summary</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">Equipment Type</th>
                    <th style="width: 10%;">Total</th>
                    <th style="width: 10%;">Inspected</th>
                    <th style="width: 10%;">Not Inspected</th>
                    <th style="width: 10%;">With NG Items</th>
                    <th style="width: 10%;">Completion %</th>
                    <th style="width: 25%;">Monthly Coverage</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $typeGroups = $equipmentSummary->groupBy('equipment_type');
                @endphp
                @foreach ($typeGroups as $type => $equipments)
                    @php
                        $total = $equipments->count();
                        $inspected = $equipments->where('has_inspections', true)->count();
                        $notInspected = $equipments->where('has_inspections', false)->count();
                        $withNgItems = $equipments->where('has_ng_items', true)->count();
                        $completionRate = $total > 0 ? round(($inspected / $total) * 100, 1) : 0;

                        // Calculate monthly coverage
                        $monthlyCoverage = [];
                        for ($month = 1; $month <= 12; $month++) {
                            $monthInspected = 0;
                            foreach ($equipments as $equipment) {
                                $monthData = $equipment['monthly_data'][$month - 1] ?? null;
                                if ($monthData && $monthData['status'] !== 'not_inspected') {
                                    $monthInspected++;
                                }
                            }
                            $monthlyCoverage[] = $total > 0 ? round(($monthInspected / $total) * 100) : 0;
                        }
                    @endphp
                    <tr>
                        <td class="text-bold">{{ $type }}</td>
                        <td class="text-center">{{ $total }}</td>
                        <td class="text-center">{{ $inspected }}</td>
                        <td class="text-center">{{ $notInspected }}</td>
                        <td class="text-center">{{ $withNgItems }}</td>
                        <td class="text-center text-bold">{{ $completionRate }}%</td>
                        <td class="text-center" style="font-size: 8px;">
                            @foreach ($monthlyCoverage as $index => $coverage)
                                <span
                                    style="color: {{ $coverage >= 80 ? '#28a745' : ($coverage >= 50 ? '#ffc107' : '#dc3545') }};">
                                    {{ $coverage }}%
                                </span>{{ $index < 11 ? ' | ' : '' }}
                            @endforeach
                        </td>
                    </tr>
                @endforeach
                <tr style="background-color: #e9ecef; font-weight: bold;">
                    <td class="text-bold">TOTAL</td>
                    <td class="text-center">{{ $equipmentSummary->count() }}</td>
                    <td class="text-center">{{ $equipmentSummary->where('has_inspections', true)->count() }}</td>
                    <td class="text-center">{{ $equipmentSummary->where('has_inspections', false)->count() }}</td>
                    <td class="text-center">{{ $equipmentSummary->where('has_ng_items', true)->count() }}</td>
                    <td class="text-center">
                        {{ $equipmentSummary->count() > 0 ? round(($equipmentSummary->where('has_inspections', true)->count() / $equipmentSummary->count()) * 100, 1) : 0 }}%
                    </td>
                    <td class="text-center">Overall Summary</td>
                </tr>
            </tbody>
        </table>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div class="footer-left">
            <div class="text-bold">SISCA V2 - Safety Inspection System for Check Activity</div>
            <div>PT. AISIN INDONESIA</div>
            <div>Generated on {{ $generatedAt }}</div>
        </div>
        <div class="footer-right">
            <div>Report Generated by: <span class="text-bold">{{ $generatedBy }}</span></div>
            <div>Annual Summary Report {{ $year }}</div>
            <div>{{ $plantName }} - {{ $areaName }}</div>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>

</html>
