@extends('admin.layouts.app')

@push('css')
    <style>
        .page-shell { padding-top: 1rem; }
        .page-hero {
            border-radius: 28px; padding: 24px;
            background: linear-gradient(135deg, #111827 0%, #2563eb 45%, #22d3ee 100%);
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
        .table thead th { background: #eef4ff; color: #334155; font-weight: 700; }
        .table tbody tr:hover { background: #f8fbff; }
        .action-buttons .btn-sm { font-size: 0.75rem; padding: 0.2rem 0.5rem; }
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

            // ========== EDIT MODAL ==========
            const editModal = document.getElementById('editModal');
            const editForm = document.getElementById('editForm');
            const editName = document.getElementById('edit_unit_name');
            const editCode = document.getElementById('edit_unit_code');
            const editDesc = document.getElementById('edit_description');
            const editActive = document.getElementById('edit_is_active');

            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const code = button.getAttribute('data-code');
                const desc = button.getAttribute('data-description');
                const isActive = button.getAttribute('data-is-active') === '1';

                // Set action URL
                const url = "{{ route('setting.unit.update', ':id') }}".replace(':id', id);
                editForm.action = url;

                // Set values
                editName.value = name;
                editCode.value = code;
                editDesc.value = desc || '';
                editActive.checked = isActive;
            });

            // ========== DELETE MODAL ==========
            const deleteModal = document.getElementById('deleteModal');
            const deleteForm = document.getElementById('deleteForm');
            const deleteName = document.getElementById('delete_unit_name');

            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const url = "{{ route('setting.unit.destroy', ':id') }}".replace(':id', id);
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
            <h2 class="mb-1">Unit Management</h2>
        </div>
        @if ($access['Create'] == 1)
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="fas fa-plus me-1"></i> Add New
            </button>
        @endif
    </section>

    @if ($access['Read'] == 1)
    <div class="card soft-card mt-4">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h4 class="mb-1">Unit List</h4>
            </div>
            <span class="stat-chip bg-primary-subtle text-primary"><i class="fas fa-box me-1"></i>
                {{ $units->count() }} units</span>
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
                            <th scope="col">Unit Name</th>
                            <th scope="col">Unit Code</th>
                            <th scope="col">Description</th>
                            <th scope="col">Status</th>
                            <th scope="col">Handle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($units as $unit)
                        <tr>
                            <th scope="row">{{ $units->firstItem() + $loop->index }}</th>
                            <td>{{ $unit->unit_name }}</td>
                            <td><code>{{ $unit->unit_code }}</code></td>
                            <td>{{ Str::limit($unit->description, 30) ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $unit->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $unit->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="action-buttons d-md-flex flex-md-row align-items-md-center gap-2">
                                @if ($access['Update'] == 1)
                                    <button type="button" class="btn btn-sm btn-warning btn-edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal"
                                        data-id="{{ $unit->id }}"
                                        data-name="{{ $unit->unit_name }}"
                                        data-code="{{ $unit->unit_code }}"
                                        data-description="{{ $unit->description }}"
                                        data-is-active="{{ $unit->is_active ? '1' : '0' }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                @endif
                                @if ($access['Delete'] == 1)
                                    <button type="button" class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-id="{{ $unit->id }}"
                                        data-name="{{ $unit->unit_name }}">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No Data Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3 pagination-wrapper">
                {{ $units->links() }}
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
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add New Unit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('setting.unit.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8 mb-3">
                            <label class="form-label" for="create_unit_name">Unit Name</label>
                            <input type="text" id="create_unit_name" name="unit_name"
                                value="{{ old('unit_name') }}"
                                class="form-control @error('unit_name') is-invalid @enderror"
                                placeholder="Pieces">
                            @error('unit_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="create_unit_code">Unit Code</label>
                            <input type="text" id="create_unit_code" name="unit_code"
                                value="{{ old('unit_code') }}"
                                class="form-control @error('unit_code') is-invalid @enderror"
                                placeholder="PCS">
                            @error('unit_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="create_description">Description</label>
                        <textarea id="create_description" name="description" rows="3"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Example: Product unit for accessories, food, beverages, etc.">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <div class="switch-card">
                            <label class="form-label d-block">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="create_is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="create_is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Add Unit</button>
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
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8 mb-3">
                            <label class="form-label" for="edit_unit_name">Unit Name</label>
                            <input type="text" id="edit_unit_name" name="unit_name"
                                class="form-control @error('unit_name') is-invalid @enderror"
                                placeholder="Pieces">
                            @error('unit_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="edit_unit_code">Unit Code</label>
                            <input type="text" id="edit_unit_code" name="unit_code"
                                class="form-control @error('unit_code') is-invalid @enderror"
                                placeholder="PCS">
                            @error('unit_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="edit_description">Description</label>
                        <textarea id="edit_description" name="description" rows="3"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Example: Product unit for accessories, food, beverages, etc."></textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <div class="switch-card">
                            <label class="form-label d-block">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                                <label class="form-check-label" for="edit_is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-dark"><i class="fas fa-save me-1"></i> Update Unit</button>
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
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Delete Unit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center py-4">
                    <div class="mb-3"><i class="fas fa-trash-alt text-danger" style="font-size: 4rem;"></i></div>
                    <h5 class="mb-3">Are you sure?</h5>
                    <p class="text-muted mb-0">You are about to delete <strong id="delete_unit_name"></strong>.</p>
                    <p class="text-danger small mt-2 mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-2"></i> Delete Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif


@endsection