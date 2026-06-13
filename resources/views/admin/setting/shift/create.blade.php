@extends('admin.layouts.app')

@section('content')
    <div class="card">

        <div class="card-header">
            <h4>Add New Shift</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('setting.shift.store') }}" method="POST">

                @csrf

                {{-- Company --}}
                <div class="mb-3">

                    <label class="form-label" for="company_id">
                        Company
                    </label>

                    <select id="company_id" name="company_id" class="form-select @error('company_id') is-invalid @enderror">

                        <option value="">
                            -- Select Company --
                        </option>

                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" @selected(old('company_id') == $company->id)>
                                {{ $company->company_name }}
                            </option>
                        @endforeach

                    </select>

                    @error('company_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                {{-- Shift Name --}}
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">

                    <label class="form-label" for="shift_name">
                        Shift Name
                    </label>

                    <input type="text" id="shift_name" name="shift_name" value="{{ old('shift_name') }}"
                        class="form-control @error('shift_name') is-invalid @enderror" placeholder="Morning Shift">

                    @error('shift_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">

                    <label class="form-label" for="shift_code">
                        Shift Code
                    </label>

                    <input type="text" id="shift_code" name="shift_code" value="{{ old('shift_code') }}"
                        class="form-control @error('shift_code') is-invalid @enderror" placeholder="MORNING">

                    @error('shift_code')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>
                    </div>
                </div>

                <div class="row">

                    {{-- Start Time --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label" for="start_time">
                            Start Time
                        </label>

                        <input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}"
                            class="form-control @error('start_time') is-invalid @enderror">

                        @error('start_time')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- End Time --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label" for="end_time">
                            End Time
                        </label>

                        <input type="time" id="end_time" name="end_time" value="{{ old('end_time') }}"
                            class="form-control @error('end_time') is-invalid @enderror">

                        @error('end_time')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

                <div class="row">

                    {{-- Late Tolerance --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label" for="late_tolerance_minutes">
                            Late Tolerance (Minutes)
                        </label>

                        <input type="number" min="0" id="late_tolerance_minutes" name="late_tolerance_minutes"
                            value="{{ old('late_tolerance_minutes', 0) }}"
                            class="form-control @error('late_tolerance_minutes') is-invalid @enderror" placeholder="15">

                        @error('late_tolerance_minutes')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- Early Leave Tolerance --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label" for="early_leave_tolerance_minutes">
                            Early Leave Tolerance (Minutes)
                        </label>

                        <input type="number" min="0" id="early_leave_tolerance_minutes"
                            name="early_leave_tolerance_minutes" value="{{ old('early_leave_tolerance_minutes', 0) }}"
                            class="form-control @error('early_leave_tolerance_minutes') is-invalid @enderror"
                            placeholder="15">

                        @error('early_leave_tolerance_minutes')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('setting.shift.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Add Shift
                    </button>

                </div>

            </form>
        </div>

    </div>
@endsection
