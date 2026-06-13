@extends('admin.layouts.app')

@push('css')
    <style>
        #logoPreview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const input = document.getElementById('company_logo');
            const preview = document.getElementById('logoPreview');

            input.addEventListener('change', function(e) {

                const file = e.target.files[0];

                if (!file) {
                    return;
                }

                const reader = new FileReader();

                reader.onload = function(event) {
                    preview.src = event.target.result;
                };

                reader.readAsDataURL(file);

            });

        });
    </script>
@endpush

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Edit Outlet</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('setting.company.update', $company->id) }}" method="POST" enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label" for="company_name">
                        Outlet Name
                    </label>

                    <input type="text" id="company_name" name="company_name"
                        class="form-control @error('company_name') is-invalid @enderror"
                        value="{{ old('company_name', $company->company_name) }}" placeholder="Raja Kepiting">

                    @error('company_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="company_email">
                            Email Address
                        </label>

                        <input type="email" id="company_email" name="company_email"
                            class="form-control @error('company_email') is-invalid @enderror"
                            value="{{ old('company_email', $company->company_email) }}" placeholder="company@email.com">

                        @error('company_email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="company_phone">
                            Phone Number
                        </label>

                        <input type="text" id="company_phone" name="company_phone"
                            class="form-control @error('company_phone') is-invalid @enderror"
                            value="{{ old('company_phone', $company->company_phone) }}" placeholder="08123456789">

                        @error('company_phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>

                <div class="mb-3">
                    <label class="form-label" for="company_address">
                        Address
                    </label>

                    <textarea id="company_address" name="company_address" rows="4"
                        class="form-control @error('company_address') is-invalid @enderror" placeholder="Jl. Geriliya, Kota Samarinda">{{ old('company_address', $company->company_address) }}</textarea>

                    @error('company_address')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="row mb-4">

                    {{-- Use Menu Module --}}
                    <div class="col-md-4">

                        <label class="form-label d-block">
                            Menu Module
                        </label>

                        <div class="form-check form-switch">

                            <input class="form-check-input" type="checkbox" id="use_menu" name="use_menu" value="{{ old('use_service', $company->use_menu) }}"
                                {{ old('use_menu', $company->use_menu) ? 'checked' : '' }}>

                            <label class="form-check-label" for="use_menu">
                                Enable Menu
                            </label>

                        </div>

                    </div>

                    {{-- Use Inventory Module --}}
                    <div class="col-md-4">

                        <label class="form-label d-block">
                            Inventory Module
                        </label>

                        <div class="form-check form-switch">

                            <input class="form-check-input" type="checkbox" id="use_inventory" name="use_inventory"
                                value="{{ old('use_service', $company->use_inventory) }}" {{ old('use_inventory', $company->use_inventory) ? 'checked' : '' }}>

                            <label class="form-check-label" for="use_inventory">
                                Enable Inventory
                            </label>

                        </div>

                    </div>

                    {{-- Use Service Module --}}
                    <div class="col-md-4">

                        <label class="form-label d-block">
                            Service Module
                        </label>

                        <div class="form-check form-switch">

                            <input class="form-check-input" type="checkbox" id="use_service" name="use_service"
                                value="{{ old('use_service', $company->use_service) }}" {{ old('use_service', $company->use_service) ? 'checked' : '' }}>

                            <label class="form-check-label" for="use_service">
                                Enable Service
                            </label>

                        </div>

                    </div>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Outlet Logo
                    </label>

                    <input type="file" id="company_logo" name="company_logo"
                        class="form-control @error('company_logo') is-invalid @enderror" accept="image/*">

                    @error('company_logo')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="d-flex justify-content-center mt-3">

                        @if ($company->company_logo)
                            <img id="logoPreview" src="{{ $company->logo_url }}?v={{ $company->updated_at?->timestamp }}"
                                alt="{{ $company->company_name }}">
                        @else
                            <img id="logoPreview" src="https://placehold.co/100x100?text=Logo" alt="No Logo">
                        @endif

                    </div>

                </div>

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('setting.company.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Update Outlet
                    </button>

                </div>

            </form>
        </div>
    </div>
@endsection
