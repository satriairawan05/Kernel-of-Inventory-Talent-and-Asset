@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="card soft-card">
            <div class="card-header">
                <h4><i class="fas fa-plus me-2"></i>Generate Daily Report</h4>
            </div>
            <div class="card-body">
                @if (session('failed'))
                    <div class="alert alert-danger">{{ session('failed') }}</div>
                @endif

                <form action="{{ route('inventory.report.generate') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                                value="{{ old('date', now()->toDateString()) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Shift Period <span class="text-danger">*</span></label>
                            <select name="period_id" class="form-select @error('period_id') is-invalid @enderror" required>
                                <option value="">Pilih Shift</option>
                                @foreach ($periods as $period)
                                    <option value="{{ $period->id }}"
                                        {{ old('period_id') == $period->id ? 'selected' : '' }}>
                                        {{ $period->name }} ({{ $period->shift->name ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('period_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Location <span class="text-danger">*</span></label>
                            <input type="text" name="location"
                                class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}"
                                placeholder="Cabang / Toko" required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Report by <span class="text-danger">*</span></label>
                            <select name="reported_by" class="form-select @error('reported_by') is-invalid @enderror"
                                required>
                                <option value="">Select User</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->name }}"
                                        {{ old('reported_by') == $user->name ? 'selected' : '' }}>{{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('reported_by')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Cashier Name <span class="text-danger">*</span></label>
                            <select name="cashier_name" class="form-select @error('cashier_name') is-invalid @enderror"
                                required>
                                <option value="">Select User</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->name }}"
                                        {{ old('cashier_name') == $user->name ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cashier_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Open at</label>
                            <input type="datetime-local" name="opened_at"
                                class="form-control @error('opened_at') is-invalid @enderror"
                                value="{{ old('opened_at') }}">
                            @error('opened_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Close at</label>
                            <input type="datetime-local" name="closed_at"
                                class="form-control @error('closed_at') is-invalid @enderror"
                                value="{{ old('closed_at', now()) }}">
                            @error('closed_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <a href="{{ route('inventory.report.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Generate
                            Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
