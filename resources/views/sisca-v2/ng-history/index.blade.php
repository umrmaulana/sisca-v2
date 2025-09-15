@extends('sisca-v2.layouts.app')

@section('title', 'NG History Management')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-danger mb-1">
                    <i class="fas fa-exclamation-triangle me-2"></i>NG History Management
                </h3>
                <p class="text-muted mb-0">Monitor and track all NG (Not Good) inspection items history</p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('sisca-v2.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">NG History</li>
                </ol>
            </nav>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $ngHistories->total() }}</h4>
                                <p class="mb-0">Total NG Items</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-times-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">
                                    {{ $ngHistories->where('created_at', '>=', now()->startOfMonth())->count() }}</h4>
                                <p class="mb-0">This Month</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar-month fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">
                                    {{ $ngHistories->where('created_at', '>=', now()->startOfWeek())->count() }}</h4>
                                <p class="mb-0">This Week</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar-week fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $ngHistories->unique('equipment_id')->count() }}</h4>
                                <p class="mb-0">Affected Equipment</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-cogs fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i>Filters
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('sisca-v2.ng-history.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="equipment_code" class="form-label">Equipment Code</label>
                            <input type="text" class="form-control" id="equipment_code" name="equipment_code"
                                value="{{ request('equipment_code') }}" placeholder="Search by equipment code...">
                        </div>
                        <div class="col-md-3">
                            <label for="equipment_type_id" class="form-label">Equipment Type</label>
                            <select class="form-select" id="equipment_type_id" name="equipment_type_id">
                                <option value="">All Types</option>
                                @foreach ($equipmentTypes as $type)
                                    <option value="{{ $type->id }}"
                                        {{ request('equipment_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->equipment_name }} - {{ $type->equipment_type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if (auth('sisca-v2')->user()->role === 'Admin')
                            <div class="col-md-2">
                                <label for="plant_id" class="form-label">Plant</label>
                                <select class="form-select" id="plant_id" name="plant_id">
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
                        <div class="col-md-2">
                            <label for="inspector_id" class="form-label">Inspector</label>
                            <select class="form-select" id="inspector_id" name="inspector_id">
                                <option value="">All Inspectors</option>
                                @foreach ($inspectors as $inspector)
                                    <option value="{{ $inspector->id }}"
                                        {{ request('inspector_id') == $inspector->id ? 'selected' : '' }}>
                                        {{ $inspector->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="from_date" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="from_date" name="from_date"
                                value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="to_date" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="to_date" name="to_date"
                                value="{{ request('to_date') }}">
                        </div>
                        <div class="col-md-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>Filter
                                </button>
                                <a href="{{ route('sisca-v2.ng-history.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- NG History Table -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>NG History Records
                </h6>
            </div>
            <div class="card-body">
                @if ($ngHistories->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Equipment</th>
                                    <th>Item Check</th>
                                    <th>Original Inspection</th>
                                    <th>Inspector</th>
                                    <th>Plant/Area</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ngHistories as $history)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <strong
                                                    class="text-primary">{{ $history->equipment->equipment_code }}</strong>
                                                <small class="text-muted">
                                                    {{ $history->equipment->equipmentType->equipment_name ?? 'N/A' }} -
                                                    {{ $history->equipment->equipmentType->equipment_type ?? '' }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <strong>{{ $history->checksheetTemplate->item_name ?? 'N/A' }}</strong>
                                                @if ($history->checksheetTemplate && $history->checksheetTemplate->standar_condition)
                                                    <small
                                                        class="text-muted">{{ $history->checksheetTemplate->standar_condition }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <strong>{{ $history->notes }}</strong>
                                                <small class="text-muted">
                                                    {{ $history->originalInspection->inspection_date->format('d/m/Y') }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <strong>{{ $history->user->name ?? 'N/A' }}</strong>
                                                <small class="text-muted">{{ $history->user->npk ?? '' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <strong>{{ $history->equipment->location->plant->plant_name ?? 'N/A' }}</strong>
                                                <small
                                                    class="text-muted">{{ $history->equipment->location->area->area_name ?? '' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $history->inspection_date->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i>{{ $history->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @if ($history->picture && \Storage::disk('public')->exists($history->picture))
                                                    <button type="button" class="btn btn-sm btn-outline-info"
                                                        onclick="showImageModal('{{ asset('storage/' . $history->picture) }}', '{{ $history->checksheetTemplate->item_name ?? 'NG Item' }}')">
                                                        <i class="fas fa-image"></i>
                                                    </button>
                                                @endif
                                                <a href="{{ route('sisca-v2.checksheets.show', $history->original_inspection_id) }}"
                                                    class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <small class="text-muted">
                                Showing {{ $ngHistories->firstItem() }} to {{ $ngHistories->lastItem() }}
                                of {{ $ngHistories->total() }} results
                            </small>
                        </div>
                        <div>
                            {{ $ngHistories->appends(request()->query())->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5 class="text-muted">No NG History Found</h5>
                        <p class="text-muted">
                            @if (request()->hasAny(['equipment_code', 'equipment_type_id', 'plant_id', 'inspector_id', 'from_date', 'to_date']))
                                No NG history records match your filter criteria.
                            @else
                                Great! No NG items have been recorded yet.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">NG Item Picture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Show image modal
        function showImageModal(src, title) {
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModalLabel').textContent = title || 'NG Item Picture';
            modal.show();
        }

        // Auto submit form on date change
        document.getElementById('from_date').addEventListener('change', function() {
            if (this.value && document.getElementById('to_date').value) {
                this.form.submit();
            }
        });

        document.getElementById('to_date').addEventListener('change', function() {
            if (this.value && document.getElementById('from_date').value) {
                this.form.submit();
            }
        });
    </script>
@endpush
