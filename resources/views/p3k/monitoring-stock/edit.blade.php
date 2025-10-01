@extends('layouts.app')
@section('title', 'Update Stock')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('p3k.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('p3k.monitoring-stock.index') }}">Monitoring Stock</a></li>
            <li class="breadcrumb-item active">Update Stock</li>
        </ol>
    </nav>
    <div
        class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mt-2 pb-2 mb-3 border-bottom col-lg-12">
        <h6>Restock First Aid Item</h6>
    </div>
    <form action="{{ route('p3k.monitoring-stock.update', $stocks->id) }}" method="POST" class="mb-5 col-lg-12"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="mb-3 col-md-6">
                <label for="item" class="form-label">Item</label>
                <input type="text" name="item" id="item" placeholder="Masukkan Item"
                    class="form-control @error('item') is-invalid @enderror" value="{{ old('item') ?? $stocks->item }}"
                    readonly>
                @error('item')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3 col-md-6">
                <label for="standard_stock" class="form-label">Standard Stock</label>
                <input type="text" name="item" id="item" placeholder="Masukkan Item"
                    class="form-control @error('item') is-invalid @enderror"
                    value="{{ old('standard_stock') ?? $stocks->standard_stock }}" readonly>
                @error('standard_stock')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3 col-md-6">
                <label for="actual_stock" class="form-label">Actual Stock</label>
                <input type="number" name="actual_stock" id="actual_stock" placeholder="Masukkan actual_stock"
                    class="form-control @error('actual_stock') is-invalid @enderror"
                    value="{{ old('actual_stock') ?? $stocks->actual_stock }}">
                @error('actual_stock')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            @if (in_array($stocks->item, [
                    'Kasa steril terbungkus',
                    'Aquades (100 ml lar.Saline)',
                    'Povidon Iodin (60 ml)',
                    'Alkohol 70%',
                ]))
                <div class="mb-3 col-md-6">
                    <label for="expired_at" class="form-label">Expired Date</label>
                    <input type="date" name="expired_at" id="expired_at" placeholder="Masukkan expired_at"
                        class="form-control @error('expired_at') is-invalid @enderror"
                        value="{{ old('expired_at') ?? ($stocks->expired_at ? \Carbon\Carbon::parse($stocks->expired_at)->format('Y-m-d') : '') }}">
                    @error('expired_at')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            @endif
        </div>
        <a class="btn btn-secondary" href="/p3k/monitoring-stock" role="button">Cancel</a>
        <button type="submit" class="btn btn-warning">Update</button>
    </form>
@endsection
