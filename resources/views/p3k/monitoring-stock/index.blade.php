@extends('layouts.app')
@section('title', 'Monitoring Stock')

@section('content')
    <div class="container">
        <div class="mb-5">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('p3k.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Monitoring Stock</a></li>
                </ol>
            </nav>

            <div class="rounded shadow">
                {{-- Location Button --}}
                @foreach ($locations as $locationBtn)
                    <a href="{{ route('p3k.monitoring-stock.index', ['location_id' => $locationBtn->id]) }}"
                        class="btn btn-folder {{ request('location_id') == $locationBtn->id ? 'active' : '' }}">
                        {{ $locationBtn->location }}
                    </a>
                @endforeach

                <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#historyModal">
                    History
                </button>
                {{-- Table item --}}
                <div class="card px-4">
                    {{-- Kalau ada lokasi terpilih, tampilkan tabel --}}
                    @if ($location)
                        <div class="table-responsive mt-3">
                            <table class="table table-hover align-middle text-center table-sm" id="customTable">
                                <thead class="table-dark small">
                                    <tr>
                                        <th>#</th>
                                        <th>Item</th>
                                        <th>Tag Number</th>
                                        <th>Expired</th>
                                        <th>Standard Stock</th>
                                        <th>Actual Stock</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stocks as $stock)
                                        <tr>
                                            <td data-label="#">{{ $loop->iteration }}</td>
                                            <td data-label="Item:" class="text-start">{{ $stock->item }}</td>
                                            <td data-label="Tag Number:" class="text-start">{{ $stock->tag_number }}</td>
                                            <td data-label="expired"
                                                class="{{ isset($stock->expired_at) && $stock->expired_at < now() ? 'text-danger' : 'text-success' }}">
                                                {{ $stock->expired_at ?? 'N/A' }}
                                            </td>
                                            <td data-label="Standard:">{{ $stock->standard_stock }} pcs</td>
                                            <td data-label="Actual:">
                                                <span
                                                    class="fw-semibold {{ $stock->actual_stock < $stock->standard_stock ? 'text-danger' : 'text-success' }}">
                                                    {{ $stock->actual_stock }} pcs
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('p3k.monitoring-stock.edit', $stock->id) }}"
                                                    class="btn btn-sm btn-outline-warning">
                                                    <i class="bi bi-pencil-square"></i>Restock
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No stock data available.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- History Table -->
            <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="fw-semibold mb-4">
                                <i class="bi bi-clock-history me-2"></i>First Aid Transaction History
                            </h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            {{-- Filter Section --}}
                            <form id="filterForm" class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label for="location_id" class="form-label">Location</label>
                                    <select name="location_id" id="modal-filter-location" class="form-select">
                                        <option value="">All Locations</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}"
                                                {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                                {{ $location->location }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label for="month" class="form-label">Month</label>
                                    <select name="month" id="filter-month" class="form-select">
                                        <option value="">All Months</option>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}"
                                                {{ request('month') == $m ? 'selected' : '' }}>
                                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label for="year" class="form-label">Year</label>
                                    <select name="year" id="filter-year" class="form-select">
                                        <option value="">All Years</option>
                                        @for ($y = now()->year; $y >= 2020; $y--)
                                            <option value="{{ $y }}"
                                                {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                    <button type="button" id="clearFilter" class="btn btn-secondary w-100">Clear</button>
                                </div>
                            </form>
                            <hr>
                            {{-- Tabel History --}}
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle text-center" id="historyTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Item</th>
                                            <th>NPK</th>
                                            <th>Activity</th>
                                            <th>Quantity</th>
                                            <th>Accident</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($histories as $history)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="text-center">{{ $history->p3k->item }}</td>
                                                <td>{{ $history->npk }}</td>
                                                <td>
                                                    @php
                                                        $badgeColors = [
                                                            'add' => 'success',
                                                            'remove' => 'danger',
                                                            'restock' => 'primary',
                                                            'take' => 'warning',
                                                        ];
                                                    @endphp
                                                    <span
                                                        class="badge bg-{{ $badgeColors[$history->action] ?? 'secondary' }}">
                                                        {{ ucfirst($history->action) }}
                                                    </span>
                                                </td>
                                                <td>{{ $history->quantity }} pcs</td>
                                                <td>
                                                    @if ($history->accident)
                                                        <span>Victim:</span> {{ $history->accident->victim_name }}<br>
                                                        <span>Victim NPK:</span> {{ $history->accident->victim_npk }}<br>
                                                        <span>Accident:</span>
                                                        {{ $history->accident->masterAccident->name ?? '-' }}<br>
                                                        <span>Department:</span>
                                                        {{ $history->accident->department->name ?? '-' }}<br>
                                                    @else
                                                        <em>No data</em>
                                                    @endif
                                                </td>
                                                <td>{{ $history->updated_at->format('d-m-Y H:i') }}</td>
                                                <td>
                                                    <form
                                                        action="{{ route('p3k.transaction-history.destroy', $history->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Are you sure to delete this history?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-muted text-center">No transaction history.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    // Filter History on Modal
    document.addEventListener('DOMContentLoaded', function() {
        const historyModal = document.getElementById('historyModal');
        if (!historyModal) return;

        historyModal.addEventListener('shown.bs.modal', function() {
            const filterForm = document.getElementById('filterForm');
            const locationSelect = document.getElementById('modal-filter-location');
            const monthSelect = document.getElementById('filter-month');
            const yearSelect = document.getElementById('filter-year');
            const clearButton = document.getElementById('clearFilter');
            const historyTableBody = document.querySelector('#historyTable tbody');

            function fetchFilteredData(location = '', month = '', year = '') {
                const url = "{{ route('p3k.monitoring-stock.filterHistory') }}";
                const params = new URLSearchParams({
                    location_id: location,
                    month: month,
                    year: year
                });

                return fetch(`${url}?${params.toString()}`)
                    .then(response => {
                        if (!response.ok) throw new Error("Network response was not ok");
                        return response.json();
                    })
                    .then(data => {
                        historyTableBody.innerHTML = data.histories;
                    })
                    .catch(error => {
                        console.error('Error fetching history:', error);
                    });
            }

            // Submit filter
            const submitBtn = filterForm.querySelector('button[type="submit"]');
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();

                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    `<span class="spinner-border spinner-border-sm me-2"></span>Loading...`;

                fetchFilteredData(locationSelect.value, monthSelect.value, yearSelect.value)
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Filter';
                    });
            });

            // Clear filter
            clearButton.addEventListener('click', function() {
                locationSelect.value = '';
                monthSelect.value = '';
                yearSelect.value = '';
                fetchFilteredData();
            });

            fetchFilteredData(locationSelect.value, monthSelect.value, yearSelect.value)
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Filter';
                });

        });
    });
    // End Filter History on Modal
</script>
