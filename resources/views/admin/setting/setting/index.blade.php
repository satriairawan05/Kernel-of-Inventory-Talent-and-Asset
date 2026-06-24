@extends('admin.layouts.app')

@push('css')
    <style>
        .page-shell {
            padding-top: 1rem;
        }

        .page-hero {
            border-radius: 28px;
            padding: 24px;
            background: linear-gradient(135deg, #111827 0%, #2563eb 45%, #22d3ee 100%);
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

        .stat-chip {
            border-radius: 999px;
            padding: 6px 10px;
            font-weight: 600;
            font-size: .82rem;
        }

        .table thead th {
            background: #eef4ff;
            color: #334155;
            font-weight: 700;
        }

        .table tbody tr:hover {
            background: #f8fbff;
        }

        .action-buttons .btn-sm {
            font-size: 0.75rem;
            padding: 0.2rem 0.5rem;
        }

        .switch-card {
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 14px;
            background: #fff;
        }

        @media (max-width: 768px) {
            .page-hero {
                padding: 18px;
            }

            .page-hero .btn {
                width: 100%;
            }

            .action-buttons {
                display: flex;
                flex-direction: column;
                gap: .45rem;
                min-width: 120px;
            }

            .action-buttons .btn {
                width: 100%;
            }

            .pagination-wrapper {
                flex-direction: column;
                gap: .75rem;
                align-items: stretch;
            }

            .pagination-wrapper>div {
                width: 100%;
            }
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
            const editKey = document.getElementById('edit_key');
            const editValue = document.getElementById('edit_value');

            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const companyId = button.getAttribute('data-company-id');
                const key = button.getAttribute('data-key');
                const value = button.getAttribute('data-value');

                const url = "{{ route('setting.system_setting.update', ':id') }}".replace(':id', id);
                editForm.action = url;

                editCompany.value = companyId;
                editKey.value = key;
                editValue.value = value || '';
            });

            // ========== DELETE MODAL ==========
            const deleteModal = document.getElementById('deleteModal');
            const deleteForm = document.getElementById('deleteForm');
            const deleteKey = document.getElementById('delete_setting_key');

            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const key = button.getAttribute('data-key');
                const url = "{{ route('setting.system_setting.destroy', ':id') }}".replace(':id', id);
                deleteForm.action = url;
                deleteKey.textContent = key;
            });

        });
    </script>
@endpush

@section('content')
    <div class="container-fluid py-4 page-shell">
        <section class="page-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <p class="eyebrow mb-2">Settings</p>
                <h2 class="mb-1">System Setting Management</h2>
            </div>
            @if ($access['Create'] == 1)
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="fas fa-plus me-1"></i> Add New System Setting
                </button>
            @endif
        </section>

        @if ($access['Read'] == 1)
            <div class="card soft-card mt-4">
                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div>
                        <h4 class="mb-1">Settings List</h4>
                    </div>
                    <span class="stat-chip bg-primary-subtle text-primary"><i class="fas fa-cog me-1"></i>
                        {{ $settings->count() }} settings</span>
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
                                    <th scope="col">Key</th>
                                    <th scope="col">Value</th>
                                    <th scope="col">Handle</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settings as $setting)
                                    <tr>
                                        <th scope="row">{{ $settings->firstItem() + $loop->index }}</th>
                                        <td>{{ $setting->company->company_name ?? '-' }}</td>
                                        <td><code>{{ $setting->key }}</code></td>
                                        <td>
                                            @if (is_numeric($setting->value))
                                                {{ \Carbon\Carbon::rupiah($setting->value) }}
                                            @else
                                                {{ $setting->value ?? '-' }}
                                            @endif
                                        </td>
                                        <td class="action-buttons d-md-flex flex-md-row align-items-md-center gap-2">
                                            @if ($access['Update'] == 1)
                                                <button type="button" class="btn btn-sm btn-warning btn-edit"
                                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                                    data-id="{{ $setting->id }}"
                                                    data-company-id="{{ $setting->company_id }}"
                                                    data-key="{{ $setting->key }}" data-value="{{ $setting->value }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                            @endif
                                            @if ($access['Delete'] == 1)
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal" data-id="{{ $setting->id }}"
                                                    data-key="{{ $setting->key }}">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No Data Found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3 pagination-wrapper">
                        {{ $settings->links() }}
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
                        <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add New System Setting</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('setting.system_setting.store') }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label" for="create_company_id">Outlet</label>
                                <select id="create_company_id" name="company_id"
                                    class="form-select @error('company_id') is-invalid @enderror">
                                    <option value="">-- Select Outlet --</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}"
                                            {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="create_key">Key</label>
                                <input type="text" id="create_key" name="key" value="{{ old('key') }}"
                                    class="form-control @error('key') is-invalid @enderror" placeholder="opening_balance">
                                @error('key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="create_value">Value</label>
                                <input type="text" id="create_value" name="value" value="{{ old('value') }}"
                                    class="form-control @error('value') is-invalid @enderror" placeholder="150000">
                                @error('value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Add
                                Setting</button>
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
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit System Setting</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label" for="edit_company_id">Outlet</label>
                                <select id="edit_company_id" name="company_id"
                                    class="form-select @error('company_id') is-invalid @enderror">
                                    <option value="">-- Select Outlet --</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="edit_key">Key</label>
                                <input type="text" id="edit_key" name="key"
                                    class="form-control @error('key') is-invalid @enderror" placeholder="opening_balance">
                                @error('key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="edit_value">Value</label>
                                <input type="text" id="edit_value" name="value"
                                    class="form-control @error('value') is-invalid @enderror" placeholder="150000">
                                @error('value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning text-dark"><i class="fas fa-save me-1"></i>
                                Update Setting</button>
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
                        <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Delete System Setting</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-body text-center py-4">
                            <div class="mb-3"><i class="fas fa-trash-alt text-danger" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="mb-3">Are you sure?</h5>
                            <p class="text-muted mb-0">You are about to delete setting <strong
                                    id="delete_setting_key"></strong>.</p>
                            <p class="text-danger small mt-2 mb-0">This action cannot be undone.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-2"></i> Delete
                                Setting</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection
