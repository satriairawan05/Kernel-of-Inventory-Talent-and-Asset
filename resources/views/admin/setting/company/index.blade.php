@extends('admin.layouts.app')

@push('css')
    <style>
        .page-shell { padding-top: 1rem; }
        .page-hero {
            border-radius: 28px; padding: 24px;
            background: linear-gradient(135deg, #0f172a 0%, #2563eb 45%, #22d3ee 100%);
            color: #fff; box-shadow: 0 18px 40px rgba(15, 23, 42, .18);
        }
        .page-hero .eyebrow { text-transform: uppercase; letter-spacing: .28em; font-size: .72rem; color: rgba(255,255,255,.82); }
        .soft-card {
            border-radius: 24px; border: 1px solid rgba(148,163,184,.18);
            box-shadow: 0 18px 40px rgba(15,23,42,.08);
            overflow: hidden;
        }
        .soft-card .card-header {
            border-bottom: 1px solid rgba(148,163,184,.18);
            background: linear-gradient(180deg, #fff 0%, #f8fbff 100%);
        }
        .stat-chip {
            border-radius: 999px; padding: 6px 10px; font-weight: 600; font-size: .82rem;
        }
        .company-logo {
            max-width: 72px;
            max-height: 72px;
            object-fit: contain;
            border-radius: 14px;
        }
        .table thead th { background: #eef4ff; color: #334155; font-weight: 700; }
        .table tbody tr:hover { background: #f8fbff; }
        .action-buttons .btn-sm { font-size: 0.75rem; padding: 0.2rem 0.5rem; }
        .preview-box {
            width: 110px; height: 110px; border-radius: 18px;
            object-fit: cover; border: 1px solid #e5e7eb; background: #f8fafc;
        }
        .switch-card {
            border: 1px solid #e5e7eb; border-radius: 16px; padding: 14px; background: #fff;
        }
        @media (max-width: 768px) {
            .page-hero { padding: 18px; }
            .page-hero .btn { width: 100%; }
            .action-buttons { display:flex; flex-direction:column; gap:.45rem; min-width:120px; }
            .action-buttons .btn { width:100%; }
            .pagination-wrapper { flex-direction:column; gap:.75rem; align-items:stretch; }
            .pagination-wrapper > div { width:100%; }
        }
    </style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ========== PREVIEW LOGO UNTUK CREATE & EDIT ==========
            function setupLogoPreview(inputId, previewId) {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                if (!input || !preview) return;
                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) {
                        preview.src = 'https://placehold.co/110x110?text=Logo';
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        preview.src = ev.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Inisialisasi preview untuk create dan edit
            setupLogoPreview('create_company_logo', 'create_logo_preview');
            setupLogoPreview('edit_company_logo', 'edit_logo_preview');

            // ========== EDIT MODAL ==========
            const editModal = document.getElementById('editModal');
            const editForm = document.getElementById('editForm');
            const editName = document.getElementById('edit_company_name');
            const editEmail = document.getElementById('edit_company_email');
            const editPhone = document.getElementById('edit_company_phone');
            const editAddress = document.getElementById('edit_company_address');
            const editBusinessType = document.getElementById('edit_business_type');
            const editUseMenu = document.getElementById('edit_use_menu');
            const editUseInventory = document.getElementById('edit_use_inventory');
            const editUseService = document.getElementById('edit_use_service');
            const editLogoPreview = document.getElementById('edit_logo_preview');

            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const email = button.getAttribute('data-email');
                const phone = button.getAttribute('data-phone');
                const address = button.getAttribute('data-address');
                const businessType = button.getAttribute('data-business-type');
                const useMenu = button.getAttribute('data-use-menu') === '1';
                const useInventory = button.getAttribute('data-use-inventory') === '1';
                const useService = button.getAttribute('data-use-service') === '1';
                const logo = button.getAttribute('data-logo');

                // Set action URL
                const url = "{{ route('setting.company.update', ':id') }}".replace(':id', id);
                editForm.action = url;

                // Set values
                editName.value = name;
                editEmail.value = email;
                editPhone.value = phone;
                editAddress.value = address;
                editBusinessType.value = businessType;
                editUseMenu.checked = useMenu;
                editUseInventory.checked = useInventory;
                editUseService.checked = useService;
                // Set logo preview
                if (logo) {
                    editLogoPreview.src = logo;
                } else {
                    editLogoPreview.src = 'https://placehold.co/110x110?text=Logo';
                }
            });

            // ========== DELETE MODAL ==========
            const deleteModal = document.getElementById('deleteModal');
            const deleteForm = document.getElementById('deleteForm');
            const deleteName = document.getElementById('delete_company_name');

            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const url = "{{ route('setting.company.destroy', ':id') }}".replace(':id', id);
                deleteForm.action = url;
                deleteName.textContent = name;
            });

        });
    </script>
@endpush

@section('content')
<div class="container-fluid py-4 page-shell">
    <section class="page-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <p class="eyebrow mb-2">Settings</p>
            <h2 class="mb-1">Outlet Management</h2>
        </div>
        @if ($access['Create'] == 1)
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="fas fa-plus me-1"></i> Add New Outlet
            </button>
        @endif
    </section>

    @if ($access['Read'] == 1)
    <div class="card soft-card mt-4">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h4 class="mb-1">List Outlet</h4>
            </div>
            <span class="stat-chip bg-primary-subtle text-primary"><i class="fas fa-building me-1"></i>
                {{ $companies->count() }} outlets</span>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('failed'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('failed') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            <div class="table-responsive mb-3">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Address</th>
                            <th scope="col">Type</th>
                            <th scope="col">Logo</th>
                            <th scope="col">Handle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($companies as $company)
                        <tr>
                            <th scope="row">{{ $companies->firstItem() + $loop->index }}</th>
                            <td>{{ $company->company_name }}</td>
                            <td>{{ $company->company_email }}</td>
                            <td>{{ $company->company_phone }}</td>
                            <td>{{ Str::limit($company->company_address, 50) ?? '-' }}</td>
                            <td>{{ $company->bussiness_type }}</td>
                            <td><img src="{{ $company->logo_url }}" alt="{{ $company->company_name }}"
                                    class="company-logo rounded"></td>
                            <td class="action-buttons d-md-flex flex-md-row align-items-md-center gap-2">
                                @if ($access['Update'] == 1)
                                    <button type="button" class="btn btn-sm btn-warning btn-edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal"
                                        data-id="{{ $company->id }}"
                                        data-name="{{ $company->company_name }}"
                                        data-email="{{ $company->company_email }}"
                                        data-phone="{{ $company->company_phone }}"
                                        data-address="{{ $company->company_address }}"
                                        data-business-type="{{ $company->bussiness_type }}"
                                        data-use-menu="{{ $company->use_menu ? '1' : '0' }}"
                                        data-use-inventory="{{ $company->use_inventory ? '1' : '0' }}"
                                        data-use-service="{{ $company->use_service ? '1' : '0' }}"
                                        data-logo="{{ $company->logo_url }}">
                                        <i class="fas fa-edit"></i> Edit Outlet
                                    </button>
                                @endif
                                @if ($access['Delete'] == 1)
                                    <button type="button" class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-id="{{ $company->id }}"
                                        data-name="{{ $company->company_name }}">
                                        <i class="fas fa-trash"></i> Delete Outlet
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No Data Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3 pagination-wrapper">
                {{ $companies->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

@if ($access['Create'] == 1)
<!-- ============================================================== -->
<!-- MODAL CREATE -->
<!-- ============================================================== -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add New Outlet</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('setting.company.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label" for="create_company_name">Outlet Name</label>
                        <input class="form-control @error('company_name') is-invalid @enderror" id="create_company_name"
                            name="company_name" type="text" value="{{ old('company_name') }}"
                            placeholder="Raja Kepiting">
                        @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="create_company_email">Email Address</label>
                            <input class="form-control @error('company_email') is-invalid @enderror"
                                value="{{ old('company_email') }}" id="create_company_email" name="company_email" type="email"
                                placeholder="outlet@email.com" />
                            @error('company_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="create_company_phone">Phone Number</label>
                            <input class="form-control @error('company_phone') is-invalid @enderror"
                                value="{{ old('company_phone') }}" id="create_company_phone" name="company_phone" type="text"
                                placeholder="08123456789" />
                            @error('company_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="create_company_address">Address</label>
                        <textarea class="form-control @error('company_address') is-invalid @enderror" id="create_company_address"
                            name="company_address" rows="3" placeholder="Ex: Jl. Geriliya, Kota Samarinda">{{ old('company_address') }}</textarea>
                        @error('company_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @php
                        $businessTypes = [
                            'Rumah Makan' => 'Rumah Makan',
                            'Counter HP'  => 'Counter HP',
                        ];
                    @endphp
                    <div class="mb-3">
                        <label class="form-label" for="create_business_type">Type</label>
                        <select class="form-select select2 @error('bussiness_type') is-invalid @enderror" id="create_business_type"
                            name="bussiness_type">
                            <option value="">Select Business Type</option>
                            @foreach ($businessTypes as $value => $label)
                                <option value="{{ $value }}" {{ old('bussiness_type') == $value ? 'selected' : '' }}>
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
                            <div class="switch-card">
                                <label class="form-label d-block">Menu Module</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="create_use_menu" name="use_menu" value="1" {{ old('use_menu', false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="create_use_menu">Enable Menu</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="switch-card">
                                <label class="form-label d-block">Inventory Module</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="create_use_inventory" name="use_inventory" value="1" {{ old('use_inventory', false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="create_use_inventory">Enable Inventory</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="switch-card">
                                <label class="form-label d-block">Service Module</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="create_use_service" name="use_service" value="1" {{ old('use_service', false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="create_use_service">Enable Service</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Outlet Logo</label>
                        <input type="file" name="company_logo" id="create_company_logo"
                            class="form-control @error('company_logo') is-invalid @enderror" accept="image/*">
                        @error('company_logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="d-flex justify-content-center align-items-center mt-3">
                            <img id="create_logo_preview" src="https://placehold.co/110x110?text=Logo"
                                alt="Preview" class="preview-box border rounded">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Add Outlet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@if ($access['Update'] == 1)
<!-- ============================================================== -->
<!-- MODAL EDIT -->
<!-- ============================================================== -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Outlet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label" for="edit_company_name">Outlet Name</label>
                        <input class="form-control @error('company_name') is-invalid @enderror" id="edit_company_name"
                            name="company_name" type="text" placeholder="Raja Kepiting">
                        @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="edit_company_email">Email Address</label>
                            <input class="form-control @error('company_email') is-invalid @enderror"
                                id="edit_company_email" name="company_email" type="email"
                                placeholder="outlet@email.com">
                            @error('company_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="edit_company_phone">Phone Number</label>
                            <input class="form-control @error('company_phone') is-invalid @enderror"
                                id="edit_company_phone" name="company_phone" type="text"
                                placeholder="08123456789">
                            @error('company_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="edit_company_address">Address</label>
                        <textarea class="form-control @error('company_address') is-invalid @enderror" id="edit_company_address"
                            name="company_address" rows="3" placeholder="Ex: Jl. Geriliya, Kota Samarinda"></textarea>
                        @error('company_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="edit_business_type">Type</label>
                        <select class="form-select select2 @error('bussiness_type') is-invalid @enderror" id="edit_business_type"
                            name="bussiness_type">
                            <option value="">Select Business Type</option>
                            @foreach ($businessTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('bussiness_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="switch-card">
                                <label class="form-label d-block">Menu Module</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="edit_use_menu" name="use_menu" value="1">
                                    <label class="form-check-label" for="edit_use_menu">Enable Menu</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="switch-card">
                                <label class="form-label d-block">Inventory Module</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="edit_use_inventory" name="use_inventory" value="1">
                                    <label class="form-check-label" for="edit_use_inventory">Enable Inventory</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="switch-card">
                                <label class="form-label d-block">Service Module</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="edit_use_service" name="use_service" value="1">
                                    <label class="form-check-label" for="edit_use_service">Enable Service</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Outlet Logo</label>
                        <input type="file" name="company_logo" id="edit_company_logo"
                            class="form-control @error('company_logo') is-invalid @enderror" accept="image/*">
                        @error('company_logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="d-flex justify-content-center mt-3">
                            <img id="edit_logo_preview" src="https://placehold.co/110x110?text=Logo"
                                alt="Preview" class="preview-box border rounded">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-dark"><i class="fas fa-save me-1"></i> Update Outlet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@if ($access['Delete'] == 1)
<!-- ============================================================== -->
<!-- MODAL DELETE -->
<!-- ============================================================== -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Delete Outlet</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center py-4">
                    <div class="mb-3"><i class="fas fa-trash-alt text-danger" style="font-size: 4rem;"></i></div>
                    <h5 class="mb-3">Are you sure?</h5>
                    <p class="text-muted mb-0">You are about to delete <strong id="delete_company_name"></strong>.</p>
                    <p class="text-danger small mt-2 mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-2"></i> Delete Outlet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif


@endsection