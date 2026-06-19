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
            const editCompany = document.getElementById('edit_company_id');
            const editName = document.getElementById('edit_shift_name');
            const editCode = document.getElementById('edit_shift_code');
            const editStart = document.getElementById('edit_start_time');
            const editEnd = document.getElementById('edit_end_time');
            const editLate = document.getElementById('edit_late_tolerance');
            const editEarly = document.getElementById('edit_early_leave_tolerance');

            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const companyId = button.getAttribute('data-company-id');
                const name = button.getAttribute('data-name');
                const code = button.getAttribute('data-code');
                const start = button.getAttribute('data-start-time');
                const end = button.getAttribute('data-end-time');
                const late = button.getAttribute('data-late-tolerance');
                const early = button.getAttribute('data-early-leave-tolerance');

                // Set action URL
                const url = "{{ route('setting.shift.update', ':id') }}".replace(':id', id);
                editForm.action = url;

                // Set values
                editCompany.value = companyId;
                editName.value = name;
                editCode.value = code;
                editStart.value = start;
                editEnd.value = end;
                editLate.value = late;
                editEarly.value = early;
            });

            // ========== DELETE MODAL ==========
            const deleteModal = document.getElementById('deleteModal');
            const deleteForm = document.getElementById('deleteForm');
            const deleteName = document.getElementById('delete_shift_name');

            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const url = "{{ route('setting.shift.destroy', ':id') }}".replace(':id', id);
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
            <h2 class="mb-1">Shift Management</h2>
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
                <h4 class="mb-1">Shift List</h4>
            </div>
            <span class="stat-chip bg-primary-subtle text-primary"><i class="fas fa-clock me-1"></i>
                {{ $shifts->count() }} shifts</span>
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
                            <th scope="col">Outlet</th>
                            <th scope="col">Shift Name</th>
                            <th scope="col">Shift Code</th>
                            <th scope="col">Start Time</th>
                            <th scope="col">End Time</th>
                            <th scope="col">Late Tolerance</th>
                            <th scope="col">Early Leave Tol.</th>
                            <th scope="col">Handle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shifts as $shift)
                        <tr>
                            <th scope="row">{{ $shifts->firstItem() + $loop->index }}</th>
                            <td>{{ $shift->company->company_name ?? '-' }}</td>
                            <td>{{ $shift->shift_name }}</td>
                            <td><code>{{ $shift->shift_code }}</code></td>
                            <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}</td>
                            <td>{{ $shift->late_tolerance_minutes }} min</td>
                            <td>{{ $shift->early_leave_tolerance_minutes }} min</td>
                            <td class="action-buttons d-md-flex flex-md-row align-items-md-center gap-2">
                                @if ($access['Update'] == 1)
                                    <button type="button" class="btn btn-sm btn-warning btn-edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal"
                                        data-id="{{ $shift->id }}"
                                        data-company-id="{{ $shift->company_id }}"
                                        data-name="{{ $shift->shift_name }}"
                                        data-code="{{ $shift->shift_code }}"
                                        data-start-time="{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}"
                                        data-end-time="{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}"
                                        data-late-tolerance="{{ $shift->late_tolerance_minutes }}"
                                        data-early-leave-tolerance="{{ $shift->early_leave_tolerance_minutes }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                @endif
                                @if ($access['Delete'] == 1)
                                    <button type="button" class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-id="{{ $shift->id }}"
                                        data-name="{{ $shift->shift_name }}">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">No Data Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3 pagination-wrapper">
                {{ $shifts->links() }}
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
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add New Shift</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('setting.shift.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label" for="create_company_id">Company</label>
                        <select id="create_company_id" name="company_id" class="form-select select2 @error('company_id') is-invalid @enderror">
                            <option value="">-- Select Company --</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->company_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('company_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="create_shift_name">Shift Name</label>
                            <input type="text" id="create_shift_name" name="shift_name"
                                value="{{ old('shift_name') }}"
                                class="form-control @error('shift_name') is-invalid @enderror"
                                placeholder="Morning Shift">
                            @error('shift_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="create_shift_code">Shift Code</label>
                            <input type="text" id="create_shift_code" name="shift_code"
                                value="{{ old('shift_code') }}"
                                class="form-control @error('shift_code') is-invalid @enderror"
                                placeholder="MORNING">
                            @error('shift_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="create_start_time">Start Time</label>
                            <input type="time" id="create_start_time" name="start_time"
                                value="{{ old('start_time') }}"
                                class="form-control @error('start_time') is-invalid @enderror">
                            @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="create_end_time">End Time</label>
                            <input type="time" id="create_end_time" name="end_time"
                                value="{{ old('end_time') }}"
                                class="form-control @error('end_time') is-invalid @enderror">
                            @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="create_late_tolerance">Late Tolerance (Minutes)</label>
                            <input type="number" min="0" id="create_late_tolerance" name="late_tolerance_minutes"
                                value="{{ old('late_tolerance_minutes', 0) }}"
                                class="form-control @error('late_tolerance_minutes') is-invalid @enderror"
                                placeholder="15">
                            @error('late_tolerance_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="create_early_leave_tolerance">Early Leave Tolerance (Minutes)</label>
                            <input type="number" min="0" id="create_early_leave_tolerance" name="early_leave_tolerance_minutes"
                                value="{{ old('early_leave_tolerance_minutes', 0) }}"
                                class="form-control @error('early_leave_tolerance_minutes') is-invalid @enderror"
                                placeholder="15">
                            @error('early_leave_tolerance_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Add Shift</button>
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
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Shift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label" for="edit_company_id">Company</label>
                        <select id="edit_company_id" name="company_id" class="form-select select2 @error('company_id') is-invalid @enderror">
                            <option value="">-- Select Company --</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                            @endforeach
                        </select>
                        @error('company_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="edit_shift_name">Shift Name</label>
                            <input type="text" id="edit_shift_name" name="shift_name"
                                class="form-control @error('shift_name') is-invalid @enderror"
                                placeholder="Morning Shift">
                            @error('shift_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="edit_shift_code">Shift Code</label>
                            <input type="text" id="edit_shift_code" name="shift_code"
                                class="form-control @error('shift_code') is-invalid @enderror"
                                placeholder="MORNING">
                            @error('shift_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="edit_start_time">Start Time</label>
                            <input type="time" id="edit_start_time" name="start_time"
                                class="form-control @error('start_time') is-invalid @enderror">
                            @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="edit_end_time">End Time</label>
                            <input type="time" id="edit_end_time" name="end_time"
                                class="form-control @error('end_time') is-invalid @enderror">
                            @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="edit_late_tolerance">Late Tolerance (Minutes)</label>
                            <input type="number" min="0" id="edit_late_tolerance" name="late_tolerance_minutes"
                                class="form-control @error('late_tolerance_minutes') is-invalid @enderror"
                                placeholder="15">
                            @error('late_tolerance_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="edit_early_leave_tolerance">Early Leave Tolerance (Minutes)</label>
                            <input type="number" min="0" id="edit_early_leave_tolerance" name="early_leave_tolerance_minutes"
                                class="form-control @error('early_leave_tolerance_minutes') is-invalid @enderror"
                                placeholder="15">
                            @error('early_leave_tolerance_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-dark"><i class="fas fa-save me-1"></i> Update Shift</button>
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
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Delete Shift</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center py-4">
                    <div class="mb-3"><i class="fas fa-trash-alt text-danger" style="font-size: 4rem;"></i></div>
                    <h5 class="mb-3">Are you sure?</h5>
                    <p class="text-muted mb-0">You are about to delete <strong id="delete_shift_name"></strong>.</p>
                    <p class="text-danger small mt-2 mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-2"></i> Delete Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif


@endsection