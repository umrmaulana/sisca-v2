@extends('layouts.app')

@section('title', 'Edit Period Check')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Edit Period Check</h3>
                <p class="text-muted mb-0">Update period check information</p>
            </div>
            <a href="{{ route('period-checks.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>

        <!-- Edit Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Period Check Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('period-checks.update', $period_check) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="period_check" class="form-label required">
                                    Period Check
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('period_check') is-invalid @enderror"
                                    id="period_check" name="period_check"
                                    value="{{ old('period_check', $period_check->period_check) }}"
                                    placeholder="Enter period check..." required>
                                @error('period_check')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">The name of the period check (e.g., Period Check 1, Main Period
                                    Check)
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input @error('is_active') is-invalid @enderror" type="checkbox"
                                        id="is_active" name="is_active" value="1"
                                        {{ old('is_active', $period_check->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <strong>Active Status</strong>
                                    </label>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Toggle to activate or deactivate this period check</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('period-checks.index') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Period Check
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
