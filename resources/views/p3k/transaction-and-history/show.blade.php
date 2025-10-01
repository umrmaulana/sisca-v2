@extends('layouts.app')
@section('title')
    First Aid Services: {{ $locations->location }}
@endsection

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
                    <li class="breadcrumb-item"><a href="{{ route('p3k.transaction-history.index') }}">First Aid Services
                            Locations</a></li>
                    <li class="breadcrumb-item active">First Aid Services Item</li>
                </ol>
            </nav>

            <div class="text-end mb-4">
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal"
                    data-bs-target="#historyModal">History</button>
            </div>

            <form id="checkoutForm" action="{{ route('p3k.transaction-history.store') }}" method="POST">
                @csrf
                @php
                    $fotoMap = [
                        'Kasa steril terbungkus' => 'kasaSteril.jpg',
                        'Perban (5 cm)' => 'perban5.jpg',
                        'Perban (10 cm)' => 'perban10.jpg',
                        'Plester (1,25 cm)' => 'plester.jpg',
                        'Plester Cepat' => 'plesterCepat.jpg',
                        'Kapas (25 gr)' => 'kapas.jpg',
                        'Kain segitiga/mittela' => 'mitela.jpg',
                        'Gunting' => 'gunting.jpg',
                        'Peniti' => 'peniti.jpg',
                        'Sarung tangan sekali pakai' => 'sarungTangan.jpg',
                        'Masker' => 'masker.jpg',
                        'Pinset' => 'pinset.jpg',
                        'Lampu senter' => 'lampuSenter.jpg',
                        'Gelas untuk cuci mata' => 'eyeflush.jpg',
                        'Kantong plastik bersih' => 'kantongPlastik.jpg',
                        'Aquades (100 ml lar. Saline)' => 'aquades.jpg',
                        'Povidon Iodin (60 ml)' => 'povidonIodine.jpg',
                        'Alkohol 70%' => 'alkohol.jpg',
                    ];
                @endphp
                <div class="row">
                    @foreach ($items as $item)
                        <div class="col-12 col-md-3 mb-4">
                            <div class="card item-card h-100">
                                <div class="card-body">
                                    @if (isset($fotoMap[$item->item]))
                                        <div class="d-flex justify-content-center mb-2">
                                            <img src="{{ asset('foto/p3k-item/' . $fotoMap[$item->item]) }}"
                                                alt="{{ $item->item }}" width="auto" height="200" class="img-fluid">
                                        </div>
                                    @endif
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="item-{{ $item->id }}"
                                            name="items[{{ $item->id }}][selected]">
                                        <label class="form-check-label fw-semibold fs-7 " for="item-{{ $item->id }}">
                                            {{ $item->item }}
                                        </label>
                                    </div>
                                    <div class="mt-3">
                                        <div class="form-label">Stok: {{ $item->actual_stock }}</div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white">
                                    <div class="mt-3">
                                        <label for="quantity-{{ $item->id }}"
                                            class="form-label text-muted small">Quantity</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="quantity-{{ $item->id }}"
                                                name="items[{{ $item->id }}][quantity]" min="1">
                                            <span class="input-group-text">pcs</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <input type="hidden" name="accident_id" value="{{ request('accident_id') }}">
                @foreach ($histories as $history)
                    <input type="hidden" id="npkValue" value="{{ $history->npk }}">
                @break
            @endforeach

            <div id="fixed-checkout">
                <button type="button" class="btn btn-primary px-4 py-2 shadow" data-bs-toggle="modal"
                    data-bs-target="#checkoutModal">
                    Check Out
                </button>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="checkoutModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="checkoutModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="checkoutModalLabel">List selected item</h5>
                        </div>
                        <div class="modal-body">
                            <ul id="selectedItemsList" class="list-group">
                                <!-- JS will populate this -->
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-success" id="confirmButton">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>

        </form>

        <!-- History Table -->
        <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                <div class="modal-content">
                    <div class="modal-header align-items-center">
                        <h4>
                            <i class="bi bi-clock-history me-2"></i>First Aid Transaction History
                        </h4>
                        <button type="button" class="btn-close mr-4" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        {{-- Tabel History --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle text-center"
                                id="customTable">
                                <thead class="table-sm">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Item</th>
                                        <th class="text-center">NPK</th>
                                        <th class="text-center">Activity</th>
                                        <th class="text-center">Quantity</th>
                                        <th >Accident</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($histories as $history)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>

                                            {{-- Menampilkan nama item jika relasi ada --}}
                                            <td>
                                                {{ $history->p3k->item }}
                                            </td>

                                            <td>{{ $history->npk }}</td>

                                            {{-- Action sebagai badge --}}
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
                                            <td class="text-start">
                                                @if ($history->accident)
                                                    <span>Victim:</span>
                                                    {{ $history->accident->victim_name }}<br>
                                                    <span>Victim NPK:</span>
                                                    {{ $history->accident->victim_npk }}<br>
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
                                                    <button type="submit" class="btn btn-sm btn-danger">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkoutButton = document.querySelector('[data-bs-target="#checkoutModal"]');
        const confirmButton = document.getElementById('confirmButton');
        const finishCheckoutButton = document.getElementById('finishCheckout');
        const itemsListContainer = document.getElementById('selectedItemsList');

        // Enable/Disable quantity input when checkbox is toggled
        document.querySelectorAll('input[type="checkbox"][name$="[selected]"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const itemId = this.id.replace('item-', '');
                const quantityInput = document.querySelector(
                    `input[name="items[${itemId}][quantity]"]`);
                quantityInput.disabled = !this.checked;
                if (!this.checked) quantityInput.value = '';
            });
        });

        checkoutButton.addEventListener('click', function() {
            itemsListContainer.innerHTML = '';
            confirmButton.disabled = true;

            const checkedItems = document.querySelectorAll(
                'input[type="checkbox"][name$="[selected]"]:checked');
            if (checkedItems.length === 0) return;

            let hasValidItem = false;

            checkedItems.forEach(function(checkbox) {
                const itemId = checkbox.id.replace('item-', '');
                const label = document.querySelector(`label[for="item-${itemId}"]`);
                const quantityInput = document.querySelector(
                    `input[name="items[${itemId}][quantity]"]`);
                const quantity = quantityInput?.value;

                if (label && quantity && parseInt(quantity) > 0) {
                    hasValidItem = true;
                    const listItem = document.createElement('li');
                    listItem.className =
                        'list-group-item d-flex justify-content-between align-items-center';
                    listItem.innerHTML = `
                ${label.textContent.trim()}
                <span class="badge bg-primary rounded-pill">${quantity} pcs</span>
            `;
                    itemsListContainer.appendChild(listItem);
                }
            });

            confirmButton.disabled = !hasValidItem;
        });


        confirmButton.addEventListener('click', function() {
            // Tutup modal checkout
            $('#checkoutModal').modal('hide');

            // Tampilkan modal barcode
            $('#barcodeModal').modal({
                backdrop: 'static',
                keyboard: false
            }).modal('show');


            // ambil NPK dari input hidden (atau bisa dari server side)
            let npk = document.getElementById("npkValue").value;

            // generate datetime sekarang
            let now = new Date();
            let formattedDate = now.getFullYear().toString() +
                String(now.getMonth() + 1).padStart(2, '0') +
                String(now.getDate()).padStart(2, '0') +
                String(now.getHours()).padStart(2, '0');

            // gabungan NPK + datetime
            let barcodeValue = npk + "-" + formattedDate;

            // generate barcode
            JsBarcode("#barcode", barcodeValue, {
                format: "CODE128",
                displayValue: true,
                lineColor: "#000",
                width: 2,
                height: 50
            });
        });


        finishCheckoutButton.addEventListener('click', function() {
            document.getElementById('checkoutForm').submit();
        });

    });
</script>
@endsection
<!-- Modal Barcode -->
<div class="modal fade" id="barcodeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Scan Barcode</h5>
        </div>
        <div class="modal-body text-center">
            <svg id="barcode"></svg>
            <p class="mt-2">Please scan the barcode to complete the transaction.</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" id="finishCheckout">OK</button>
        </div>
    </div>
</div>
</div>
