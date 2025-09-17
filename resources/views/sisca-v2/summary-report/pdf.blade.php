<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Annual Summary Report - {{ $year }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            margin: 0;
            color: #333;
            line-height: 1.2;
            background-color: #fff;
        }

        .container {
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #1a56db;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .header h1 {
            color: #1a56db;
            font-size: 22px;
            margin: 0 0 8px 0;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header h2 {
            color: #374151;
            font-size: 16px;
            margin: 5px 0;
            font-weight: 600;
        }

        .header p {
            color: #6b7280;
            font-size: 11px;
            margin: 8px 0 0 0;
        }

        .company-info {
            text-align: center;
            margin-bottom: 25px;
            padding: 12px;
            background-color: #f1f5f9;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
        }

        .company-info h3 {
            color: #1a56db;
            font-size: 14px;
            margin: 0 0 5px 0;
            font-weight: bold;
        }

        .company-info p {
            font-size: 9px;
            color: #6b7280;
            margin: 2px 0;
        }

        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .info-left,
        .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 12px;
            background-color: #f8f9fa;
            border: 1px solid #e5e7eb;
        }

        .info-left {
            border-right: none;
            border-top-left-radius: 6px;
            border-bottom-left-radius: 6px;
        }

        .info-right {
            border-top-right-radius: 6px;
            border-bottom-right-radius: 6px;
        }

        .info-item {
            margin-bottom: 6px;
            display: table;
            width: 100%;
        }

        .info-label {
            font-weight: bold;
            color: #374151;
            display: table-cell;
            width: 35%;
            padding-right: 8px;
            font-size: 9px;
        }

        .info-value {
            display: table-cell;
            color: #1a56db;
            font-weight: 600;
            font-size: 9px;
        }

        .summary-stats {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #1a56db;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .summary-stats h3 {
            margin: 0 0 12px 0;
            color: #1a56db;
            font-size: 14px;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stats-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
        }

        .stats-row {
            display: table-row;
        }

        .stat-item {
            display: table-cell;
            text-align: center;
            background-color: white;
            padding: 10px 5px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            width: 25%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .stat-number {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
            display: block;
        }

        .stat-label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .stat-approved {
            color: #059669;
        }

        .stat-pending {
            color: #d97706;
        }

        .stat-rejected {
            color: #dc2626;
        }

        .stat-ng {
            color: #dc2626;
        }

        .stat-completion {
            color: #0891b2;
        }

        .stat-types {
            color: #7c3aed;
        }

        .stat-total {
            color: #1a56db;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            font-size: 8px;
        }

        table caption {
            font-size: 14px;
            font-weight: bold;
            color: #fff;
            margin-bottom: 8px;
            text-align: left;
            caption-side: top;
        }

        th {
            background: #f1f5f9;
            color: #1a56db;
            padding: 8px 4px;
            text-align: center;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-right: 1px solid rgba(255, 255, 255, 0.2);
        }

        th:last-child {
            border-right: none;
        }

        td {
            padding: 6px 4px;
            font-size: 8px;
            vertical-align: middle;
            border-bottom: 1px solid #f3f4f6;
            border-right: 1px solid #f3f4f6;
            text-align: center;
        }

        td:first-child {
            text-align: left;
            font-weight: bold;
        }

        td:last-child {
            border-right: none;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        tr:hover {
            background-color: #f3f4f6;
        }

        .status-approved {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            padding: 3px 6px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 7px;
            text-transform: uppercase;
            border: 1px solid #a7f3d0;
        }

        .status-pending {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            padding: 3px 6px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 7px;
            text-transform: uppercase;
            border: 1px solid #fde68a;
        }

        .status-rejected {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            padding: 3px 6px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 7px;
            text-transform: uppercase;
            border: 1px solid #fecaca;
        }

        .status-not-inspected {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            color: #4b5563;
            padding: 3px 6px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 7px;
            text-transform: uppercase;
            border: 1px solid #e5e7eb;
        }

        .month-cell {
            text-align: center;
            padding: 4px;
            font-size: 7px;
            font-weight: bold;
            border-radius: 3px;
        }

        .month-ok {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .month-ng {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .month-pending {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .month-not-inspected {
            background-color: #f3f4f6;
            color: #6b7280;
            border: 1px solid #e5e7eb;
        }

        .section-title {
            color: #ffff;
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 12px 0;
            padding: 10px;
            border-bottom: 2px solid #1a56db;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background-color: #1a56db;
            border-radius: 4px;
        }

        .footer {
            margin-top: 25px;
            padding-top: 12px;
            border-top: 2px solid #1a56db;
            font-size: 9px;
            color: #6b7280;
            background-color: #f8f9fa;
            padding: 12px;
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
            padding: 30px 15px;
            color: #6b7280;
            font-style: italic;
            background-color: #f9fafb;
            border-radius: 5px;
            font-size: 10px;
        }

        .legend {
            margin-top: 10px;
            padding: 8px;
            background-color: #f9fafb;
            border-radius: 5px;
            font-size: 8px;
            border: 1px solid #e5e7eb;
        }

        .legend-item {
            display: inline-block;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
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
            <p><strong>Company:</strong> {{ $plantName }} | <strong>Area:</strong> {{ $areaName }}</p>
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
                    <span class="info-label">Company:</span>
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

        <div class="page-break"></div>

        <!-- Equipment Details Table -->
        <div class="section-title">Equipment Annual Inspection Status</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">Code</th>
                    <th style="width: 10%;">Type</th>
                    <th style="width: 15%;">Name</th>
                    <th style="width: 10%;">Location</th>
                    <th style="width: 8%;">Area</th>
                    <th style="width: 3%;">Jan</th>
                    <th style="width: 3%;">Feb</th>
                    <th style="width: 3%;">Mar</th>
                    <th style="width: 3%;">Apr</th>
                    <th style="width: 3%;">May</th>
                    <th style="width: 3%;">Jun</th>
                    <th style="width: 3%;">Jul</th>
                    <th style="width: 3%;">Aug</th>
                    <th style="width: 3%;">Sep</th>
                    <th style="width: 3%;">Oct</th>
                    <th style="width: 3%;">Nov</th>
                    <th style="width: 3%;">Dec</th>
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
            <div class="legend">
                <strong>Legend:</strong>
                <span class="legend-item"><span class="month-cell month-ok">OK</span> = Inspected, No Issues</span>
                <span class="legend-item"><span class="month-cell month-ng">NG</span> = Inspected, Has Issues</span>
                <span class="legend-item"><span class="month-cell month-pending">P</span> = Pending Approval</span>
                <span class="legend-item"><span class="month-cell month-not-inspected">-</span> = Not Inspected</span>
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
                            <td class="text-center" style="font-size: 7px;">
                                @foreach ($monthlyCoverage as $index => $coverage)
                                    <span
                                        style="color: {{ $coverage >= 80 ? '#059669' : ($coverage >= 50 ? '#d97706' : '#dc2626') }};">
                                        {{ $coverage }}%
                                    </span>{{ $index < 11 ? ' | ' : '' }}
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                    <tr style="background-color: #f3f4f6; font-weight: bold;">
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
    </div>
</body>

</html>
