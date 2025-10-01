@extends('layouts.app')
@section('title', 'First Aid Services')

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
                    <li class="breadcrumb-item active">First Aid Services Locations</a></li>
                </ol>
            </nav>

            <div class="text-center mb-4">
                <h4 class="fw-bold">First Aid Services Locations</h4>
                <p class="text-muted">Where are you?</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row">
                @foreach ($locations as $location)
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card shadow border-0 rounded-3 h-100 text-center hover-card openModalBtn"
                            data-bs-toggle="modal" data-bs-target="#accidentModal" data-location-id="{{ $location->id }}"
                            data-location-name="{{ $location->location }}">
                            <div class="card-body py-4">
                                <i class="bi bi-geo-alt fs-1 text-primary mb-3"></i>
                                <p class="text-dark">{{ $location->location }}</p class="text-dark">
                                <span class="badge bg-light text-dark mt-2">Click for detail</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
@endsection

{{-- Modal DITEMPATKAN DI LUAR @section('content') supaya mudah dipindah ke <body> --}}
<div class="modal fade" id="accidentModal" tabindex="-1" aria-labelledby="accidentModalLabel" aria-hidden="true"
    data-bs-backdrop="static" {{-- OPSIONAL: klik backdrop tidak menutup modal --}} data-bs-keyboard="false" {{-- OPSIONAL: tombol ESC tidak menutup modal --}}>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title  text-white" id="accidentModalLabel">Accident Record</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form action="{{ route('p3k.accident.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="npk" id="modal_npk" value="{{ auth()->user()->npk }}">
                    {{-- Hidden input ID lokasi --}}
                    <input type="hidden" name="location_id" id="modal_location_id">
                    <input type="hidden" name="location_name" id="modal_location_name">

                    <div x-data="{ selected: '' }" class="mb-3">
                        <label for="accident_id" class="form-label">Accident</label>
                        <select name="accident_id" id="accident_id" x-model="selected" class="form-control" required>
                            <option value="">-- Select Accident --</option>
                            @foreach ($accidents as $accident)
                                <option value="{{ $accident->id }}">{{ $accident->name }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div>
                        <label for="victim_data" class="form-label">Victim Data</label>
                        <div class="mb-3">
                            <label for="npk_korban" class="form-label">NPK</label>
                            <input type="text" name="npk_korban" id="npk_korban" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="nama_korban" class="form-label">Name</label>
                            <input type="text" name="nama_korban" id="nama_korban" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="department_id" class="form-label">Department</label>
                            <select name="department_id" id="department_id" x-model="selected" class="form-control"
                                required>
                                <option value="">-- Select Department --</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <a href="/p3k/transaction-and-history" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-danger">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modalEl = document.getElementById('accidentModal');

        if (modalEl && modalEl.parentElement !== document.body) {
            document.body.appendChild(modalEl);
        }

        const modalButtons = document.querySelectorAll(".openModalBtn");

        modalButtons.forEach(btn => {
            btn.addEventListener("click", function() {
                const locationId = this.getAttribute("data-location-id");
                const locationName = this.getAttribute("data-location-name");

                document.getElementById("modal_location_id").value = locationId;
                document.getElementById("modal_location_name").value = locationName;
            });
        });
    });

    $(document).ready(function() {
        $('#accident_id').select2({
            placeholder: "-- Select Accident --",
            allowClear: true
        });

        $('#department_id').select2({
            placeholder: "-- Select Department --",
            allowClear: true
        });
    });
</script>
@endpush
