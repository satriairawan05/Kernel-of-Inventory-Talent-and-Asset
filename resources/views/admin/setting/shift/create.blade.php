@extends('admin.layouts.app')

@push('css')
<style>
    .page-shell { padding-top: 1rem; }
    .page-hero { border-radius: 28px; padding: 24px; background: linear-gradient(135deg, #111827 0%, #2563eb 45%, #22d3ee 100%); color:#fff; box-shadow: 0 18px 40px rgba(15,23,42,.18); }
    .page-hero .eyebrow { text-transform: uppercase; letter-spacing: .28em; font-size: .72rem; color: rgba(255,255,255,.82); }
    .soft-card { border-radius: 24px; border: 1px solid rgba(148,163,184,.18); box-shadow: 0 18px 40px rgba(15,23,42,.08); overflow: hidden; }
    .soft-card .card-header { border-bottom: 1px solid rgba(148,163,184,.18); background: linear-gradient(180deg, #fff 0%, #f8fbff 100%); }
    .form-label { font-weight: 600; color: #334155; }
    .form-control, .form-select { border-radius: 14px; border-color: #cbd5e1; padding: .72rem .9rem; }
    .form-control:focus, .form-select:focus { border-color: #2563eb; box-shadow: 0 0 0 .18rem rgba(37,99,235,.15); }
    .action-bar { border-top: 1px solid rgba(148,163,184,.18); margin-top: 1rem; padding-top: 1rem; }
    @media (max-width: 768px) { .page-hero { padding: 18px; } .action-bar .btn { width: 100%; } .action-bar { flex-direction: column-reverse; } }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 page-shell">
    <section class="page-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <p class="eyebrow mb-2">Settings</p>
            <h2 class="mb-1">Add New Shift</h2>
        </div>
        <span class="badge bg-white text-primary fs-6 px-3 py-2"><i class="fas fa-clock me-1"></i> New shift</span>
    </section>
    <div class="card soft-card mt-4">
        <div class="card-header">
            <h4 class="mb-1">Shift Details</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('setting.shift.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="company_id">Company</label>
                    <select id="company_id" name="company_id" class="form-select select2 @error('company_id') is-invalid @enderror">
                        <option value="">-- Select Company --</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" @selected(old('company_id') == $company->id)>{{ $company->company_name }}</option>
                        @endforeach
                    </select>
                    @error('company_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row g-3">
                    <div class="col-md-6 mb-3"><label class="form-label" for="shift_name">Shift Name</label><input type="text" id="shift_name" name="shift_name" value="{{ old('shift_name') }}" class="form-control @error('shift_name') is-invalid @enderror" placeholder="Morning Shift">@error('shift_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6 mb-3"><label class="form-label" for="shift_code">Shift Code</label><input type="text" id="shift_code" name="shift_code" value="{{ old('shift_code') }}" class="form-control @error('shift_code') is-invalid @enderror" placeholder="MORNING">@error('shift_code')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6 mb-3"><label class="form-label" for="start_time">Start Time</label><input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}" class="form-control @error('start_time') is-invalid @enderror">@error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6 mb-3"><label class="form-label" for="end_time">End Time</label><input type="time" id="end_time" name="end_time" value="{{ old('end_time') }}" class="form-control @error('end_time') is-invalid @enderror">@error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6 mb-3"><label class="form-label" for="late_tolerance_minutes">Late Tolerance (Minutes)</label><input type="number" min="0" id="late_tolerance_minutes" name="late_tolerance_minutes" value="{{ old('late_tolerance_minutes', 0) }}" class="form-control @error('late_tolerance_minutes') is-invalid @enderror" placeholder="15">@error('late_tolerance_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6 mb-3"><label class="form-label" for="early_leave_tolerance_minutes">Early Leave Tolerance (Minutes)</label><input type="number" min="0" id="early_leave_tolerance_minutes" name="early_leave_tolerance_minutes" value="{{ old('early_leave_tolerance_minutes', 0) }}" class="form-control @error('early_leave_tolerance_minutes') is-invalid @enderror" placeholder="15">@error('early_leave_tolerance_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                </div>
                <div class="d-flex justify-content-end gap-2 action-bar">
                    <a href="{{ route('setting.shift.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Add Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
