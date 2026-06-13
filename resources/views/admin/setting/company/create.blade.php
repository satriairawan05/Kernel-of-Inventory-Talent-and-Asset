@extends('admin.layouts.app')

@push('css')
    <style>
        #logoPreview {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
@endpush

@push('js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {

            const input = document.getElementById('company_logo');
            const preview = document.getElementById('logoPreview');

            input.addEventListener('change', function(e) {

                const file = e.target.files[0];

                if (!file) {
                    preview.classList.add('d-none');
                    return;
                }

                const reader = new FileReader();

                reader.onload = function(event) {
                    preview.src = event.target.result;
                    preview.classList.remove('d-none');
                };

                reader.readAsDataURL(file);

            });

        });
    </script>
@endpush

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Add New Outlet</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('setting.company.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')
                <div class="mb-3">
                    <label class="form-label" for="company_name">Outlet Name</label>

                    <input class="form-control @error('company_name') is-invalid @enderror" id="company_name"
                        name="company_name" type="text" value="{{ old('company_name') }}" placeholder="Raja Kepiting">

                    @error('company_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-6">
                        <label class="form-label" for="company_email">Email address</label>
                        <input
                            class="form-control @error('company_email')
                            is-invalid
                        @enderror"
                            value="{{ old('company_email') }}" id="company_email" name="company_email" type="email"
                            placeholder="rajakepiting@gmail.com" />
                        @error('company_email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="company_phone">Phone Number</label>
                        <input
                            class="form-control @error('company_phone')
                            is-invalid
                        @enderror"
                            value="{{ old('company_phone') }}" id="company_phone" name="company_phone" type="text"
                            placeholder="08123456789" />
                        @error('company_phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="company_address">Address</label>

                    <textarea class="form-control value="{{ old('company_address') }}" @error('company_address') is-invalid @enderror"
                        id="company_address" name="company_address" rows="4" placeholder="Ex: Jl. Geriliya, Kota Samarinda">{{ old('company_address') }}</textarea>

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

                            <input class="form-check-input" type="checkbox" id="use_menu" name="use_menu" value="{{ old('use_menu') }}"
                                {{ old('use_menu', false) ? 'checked' : '' }}>

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
                                value="{{ old('use_inventory') }}" {{ old('use_inventory', false) ? 'checked' : '' }}>
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
                                value="{{ old('use_service') }}" {{ old('use_service', false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="use_service">
                                Enable Service
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <input type="file" name="company_logo" id="company_logo"
                        class="form-control @error('company_logo') is-invalid @enderror" accept="image/*">
                    <div class="d-flex justify-content-center align-items-center mt-3">
                        <img id="logoPreview" src="#" alt="Preview" class="d-none border rounded" width="100"
                            height="100" style="object-fit: cover;">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('setting.company.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Add Outlet
                    </button>

                </div>
            </form>
        </div>
    </div>
@endsection
