@extends('layouts.app')
@section('title')
    P3K Item Master
@endsection

@section('content')
    <div class="container">
        <div class="mb-5">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="text-start">
                <a href="{{ route('p3k.master.index', ['type' => 'item']) }}"
                    class="btn btn-folder {{ $type === 'item' ? 'active' : '' }}">
                    First Aid Item
                </a>
                <a href="{{ route('p3k.master.index', ['type' => 'location']) }}"
                    class="btn btn-folder {{ $type === 'location' ? 'active' : '' }}">
                    Location
                </a>
            </div>

            <div class="card px-4">
                <div class="text-end mb-3">
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#formModal"
                        data-mode="create" data-type="{{ $type }}">
                        <i class="bi bi-plus"></i> Add {{ ucfirst($type) }}
                    </button>

                </div>
                {{-- Table Item --}}
                @if ($type === 'item')
                    <div class="table-container mt-3">
                        <table class="table table-hover align-middle text-center" id="customTable">
                            <thead class="table-dark small">
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Location</th>
                                    <th>Tag Number</th>
                                    <th>Expired</th>
                                    <th>Standard Stock</th>
                                    <th>Actual Stock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stocks as $stock)
                                    <tr>
                                        <td data-label="#">{{ $loop->iteration }}</td>
                                        <td data-label="Item:" class="text-start">{{ $stock->item }}</td>
                                        <td data-label="Location:" >{{ optional($stock->location)->location ?? '-' }}</td>
                                        <td data-label="Tag Number:" class="text-start">{{ $stock->tag_number }}</td>
                                        <td data-label="Expired:" class="{{ isset($stock->expired_at) && $stock->expired_at < now() ? 'text-danger' : 'text-success' }}">
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
                                            <button class="btn btn-sm btn-outline-warning"
                                                data-bs-toggle="modal"
                                                data-bs-target="#formModal"
                                                data-mode="edit"
                                                data-type="item"
                                                data-id="{{ $stock->id }}"
                                                data-item="{{ $stock->item }}"
                                                data-tag_number="{{ $stock->tag_number }}"
                                                data-expired_at="{{ optional($stock->expired_at)->format('Y-m-d') }}"
                                                data-standard_stock="{{ $stock->standard_stock }}"
                                                data-actual_stock="{{ $stock->actual_stock }}"
                                                data-location_id="{{ $stock->location_id }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <form action="{{ route('p3k.master.destroy', $stock->id) }}?type=item"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Are you sure want to delete this item?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>

                                        </td>
                                    </tr>
                                @endforeach

                                @if ($stocks->isEmpty())
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No stock data available.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- Table Location --}}
                @if ($type === 'location')
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center" id="customTable">
                            <thead class="small">
                                <tr>
                                    <th>#</th>
                                    <th>Location</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($locations as $location)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-start">{{ $location->location }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                                                data-bs-target="#formModal" data-mode="edit" data-type="location"
                                                data-id="{{ $location->id }}" data-location="{{ $location->location }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <form action="{{ route('p3k.master.destroy', $location->id) }}?type=location"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Are you sure want to delete this location?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach

                                @if ($locations->isEmpty())
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No location data available.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
{{-- Modal --}}
<div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="modalForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModalLabel">Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- ITEM FORM --}}
                    <div class="form-group item-fields d-none">
                        <label>Item</label>
                        <input type="text" name="item" class="form-control" id="inputItem">
                        <label>Location</label>
                        @if (!empty($locations))
                            <select name="location_id" class="form-select" id="inputLocationID">
                                <option value="" disabled selected>Select Location</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->location }}</option>
                                @endforeach
                            </select>
                        @else
                            <div class="text-danger">No locations available</div>
                        @endif

                        <label>Tag Number</label>
                        <input type="text" name="tag_number" class="form-control" id="inputTagNumber">
                        <label>Expired At</label>
                        <input type="date" name="expired_at" class="form-control" id="inputExpired">
                        <label>Standard Stock</label>
                        <input type="number" name="standard_stock" class="form-control" id="inputStandard">
                        <label>Actual Stock</label>
                        <input type="number" name="actual_stock" class="form-control" id="inputActual">
                    </div>

                    {{-- LOCATION FORM --}}
                    <div class="form-group location-fields d-none">
                        <label>Location</label>
                        <input type="text" name="location" class="form-control" id="inputLocationName">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('formModal');
        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const mode = button.getAttribute('data-mode');
            const type = button.getAttribute('data-type');

            const form = modal.querySelector('#modalForm');
            const methodInput = modal.querySelector('#formMethod');
            const title = modal.querySelector('#formModalLabel');
            const submitBtn = modal.querySelector('#modalSubmitBtn');

            // Reset form
            form.reset();
            methodInput.value = 'POST';
            submitBtn.textContent = 'Save';
            title.textContent = mode === 'edit' ? 'Edit ' + type : 'Add ' + type;

            // Toggle field visibility
            modal.querySelector('.item-fields').classList.toggle('d-none', type !== 'item');
            modal.querySelector('.location-fields').classList.toggle('d-none', type !== 'location');

            if (mode === 'create') {
                // Set action
                if (type === 'item') {
                    form.action = "/p3k/master?type=item";
                } else {
                    form.action = "/p3k/master?type=location";
                }
            } else {
                const id = button.getAttribute('data-id');
                if (type === 'item') {
                    form.action = "/p3k/master/" + id + "?type=item";
                    methodInput.value = "PUT";
                    form.querySelector('#inputItem').value = button.getAttribute('data-item');
                    form.querySelector('#inputLocationID').value = button.getAttribute('data-location_id');
                    form.querySelector('#inputTagNumber').value = button.getAttribute(
                        'data-tag_number');
                    form.querySelector('#inputExpired').value = button.getAttribute('data-expired_at');
                    form.querySelector('#inputStandard').value = button.getAttribute(
                        'data-standard_stock');
                    form.querySelector('#inputActual').value = button.getAttribute('data-actual_stock');
                } else {
                    form.action = "/p3k/master/" + id + "?type=location";
                    methodInput.value = "PUT";
                    form.querySelector('#inputLocationName').value = button.getAttribute(
                        'data-location');
                }
                submitBtn.textContent = 'Update';
            }
        });
    });
</script>
