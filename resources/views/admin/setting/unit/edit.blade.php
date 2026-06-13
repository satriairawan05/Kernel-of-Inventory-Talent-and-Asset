@extends('admin.layouts.app')

@section('content')
    <div class="card">

        <div class="card-header">
            <h4>Update Unit</h4>
        </div>

        <div class="card-body">

            <form action="{{ route('setting.unit.update',$unit) }}" method="POST">
                @method('PUT')
                @csrf
                <div class="row">

                    {{-- Unit Name --}}
                    <div class="col-md-8">

                        <div class="mb-3">

                            <label class="form-label" for="unit_name">
                                Unit Name
                            </label>

                            <input
                                type="text"
                                id="unit_name"
                                name="unit_name"
                                value="{{ old('unit_name',$unit->unit_name) }}"
                                class="form-control @error('unit_name') is-invalid @enderror"
                                placeholder="Pieces">

                            @error('unit_name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                    {{-- Unit Code --}}
                    <div class="col-md-4">

                        <div class="mb-3">

                            <label class="form-label" for="unit_code">
                                Unit Code
                            </label>

                            <input
                                type="text"
                                id="unit_code"
                                name="unit_code"
                                value="{{ old('unit_code',$unit->unit_code) }}"
                                class="form-control @error('unit_code') is-invalid @enderror"
                                placeholder="PCS">

                            @error('unit_code')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                </div>

                {{-- Description --}}
                <div class="mb-3">

                    <label class="form-label" for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="Example: Product unit for accessories, food, beverages, etc.">{{ old('description',$unit->description) }}</textarea>

                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                {{-- Status --}}
                <div class="mb-4">

                    <label class="form-label d-block">
                        Status
                    </label>

                    <div class="form-check form-switch">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="is_active"
                            name="is_active"
                            value="1"
                            {{ old('is_active', $unit->is_active) ? 'checked' : '' }}>

                        <label class="form-check-label" for="is_active">
                            Active
                        </label>

                    </div>

                </div>

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('setting.unit.index') }}"
                        class="btn btn-secondary">
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        Update Unit
                    </button>

                </div>

            </form>

        </div>

    </div>
@endsection