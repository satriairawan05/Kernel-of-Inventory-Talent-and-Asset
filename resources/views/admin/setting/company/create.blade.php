@extends('admin.layouts.app')

@push('css')
    <style>
        .page-shell {
            padding-top: 1rem;
        }

        .page-hero {
            border-radius: 28px;
            padding: 24px;
            background: linear-gradient(135deg, #0f172a 0%, #2563eb 45%, #22d3ee 100%);
            color: #fff;
            box-shadow: 0 18px 40px rgba(15, 23, 42, .18);
        }

        .page-hero .eyebrow {
            text-transform: uppercase;
            letter-spacing: .28em;
            font-size: .72rem;
            color: rgba(255, 255, 255, .82);
        }

        .soft-card {
            border-radius: 24px;
            border: 1px solid rgba(148, 163, 184, .18);
            box-shadow: 0 18px 40px rgba(15, 23, 42, .08);
            overflow: hidden;
        }

        .soft-card .card-header {
            border-bottom: 1px solid rgba(148, 163, 184, .18);
            background: linear-gradient(180deg, #fff 0%, #f8fbff 100%);
        }

        .form-label {
            font-weight: 600;
            color: #334155;
        }

        .form-control,
        .form-select {
            border-radius: 14px;
            border-color: #cbd5e1;
            padding: .72rem .9rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 .18rem rgba(37, 99, 235, .15);
        }

        .switch-card {
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 14px;
            background: #fff;
        }

        .preview-box {
            width: 110px;
            height: 110px;
            border-radius: 18px;
            object-fit: cover;
            border: 1px solid #e5e7eb;
            background: #f8fafc;
        }

        .action-bar {
            border-top: 1px solid rgba(148, 163, 184, .18);
            margin-top: 1rem;
            padding-top: 1rem;
        }

        @media (max-width: 768px) {
            .page-hero {
                padding: 18px;
            }

            .action-bar .btn {
                width: 100%;
            }

            .action-bar {
                flex-direction: column-reverse;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('company_logo');
            const preview = document.getElementById('logoPreview');
            if (!input || !preview) return;
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
    <div class="container-fluid py-4 page-shell">
        <section class="page-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <p class="eyebrow mb-2">Settings</p>
                <h2 class="mb-1">Add New Outlet</h2>
            </div>
            <span class="badge bg-white text-primary fs-6 px-3 py-2"><i class="fas fa-store me-1"></i> New outlet</span>
        </section>

        <div class="card soft-card mt-4">
            <div class="card-header">
                <h4 class="mb-1">Outlet Details</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('setting.company.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('post')
                    <div class="mb-3">
                        <label class="form-label" for="company_name">Outlet Name</label>
                        <input class="form-control @error('company_name') is-invalid @enderror" id="company_name"
                            name="company_name" type="text" value="{{ old('company_name') }}"
                            placeholder="Raja Kepiting">
                        @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="company_email">Email Address</label>
                            <input class="form-control @error('company_email') is-invalid @enderror"
                                value="{{ old('company_email') }}" id="company_email" name="company_email" type="email"
                                placeholder="rajakepiting@gmail.com" />
                            @error('company_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="company_phone">Phone Number</label>
                            <input class="form-control @error('company_phone') is-invalid @enderror"
                                value="{{ old('company_phone') }}" id="company_phone" name="company_phone" type="text"
                                placeholder="08123456789" />
                            @error('company_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="company_address">Address</label>
                        <textarea class="form-control @error('company_address') is-invalid @enderror" id="company_address"
                            name="company_address" rows="4" placeholder="Ex: Jl. Geriliya, Kota Samarinda">{{ old('company_address') }}</textarea>
                        @error('company_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @php
                        $businessTypes = [
                            'Rumah Makan' => 'Rumah Makan',
                            'Counter HP' => 'Counter HP',
                        ];
                    @endphp

                    <div class="mb-3">
                        <label class="form-label" for="bussiness_type">Type</label>
                        <select class="form-select select2 @error('bussiness_type') is-invalid @enderror" id="bussiness_type"
                            name="bussiness_type">
                            <option value="">Select Business Type</option>
                            @foreach ($businessTypes as $value => $label)
                                <option value="{{ $value }}"
                                    {{ old('bussiness_type') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('bussiness_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="switch-card"><label class="form-label d-block">Menu Module</label>
                                <div class="form-check form-switch"><input class="form-check-input" type="checkbox"
                                        id="use_menu" name="use_menu" value="1"
                                        {{ old('use_menu', false) ? 'checked' : '' }}><label class="form-check-label"
                                        for="use_menu">Enable Menu</label></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="switch-card"><label class="form-label d-block">Inventory Module</label>
                                <div class="form-check form-switch"><input class="form-check-input" type="checkbox"
                                        id="use_inventory" name="use_inventory" value="1"
                                        {{ old('use_inventory', false) ? 'checked' : '' }}><label class="form-check-label"
                                        for="use_inventory">Enable Inventory</label></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="switch-card"><label class="form-label d-block">Service Module</label>
                                <div class="form-check form-switch"><input class="form-check-input" type="checkbox"
                                        id="use_service" name="use_service" value="1"
                                        {{ old('use_service', false) ? 'checked' : '' }}><label class="form-check-label"
                                        for="use_service">Enable Service</label></div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Outlet Logo</label>
                        <input type="file" name="company_logo" id="company_logo"
                            class="form-control @error('company_logo') is-invalid @enderror" accept="image/*">
                        @error('company_logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="d-flex justify-content-center align-items-center mt-3">
                            <img id="logoPreview" src="#" alt="Preview"
                                class="preview-box d-none border rounded">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 action-bar">
                        <a href="{{ route('setting.company.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Add
                            Outlet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
