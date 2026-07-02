@extends('admin.layouts.app')

@push('css')
    <style>
        /* ===== PAGE HERO ===== */
        .page-shell {
            padding-top: 1rem;
        }

        .page-hero {
            border-radius: 2rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, #ccf0f2 0%, #a7f3d0 100%) !important;
            color: #065f46;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .page-hero .eyebrow {
            text-transform: uppercase;
            letter-spacing: .28em;
            font-size: .72rem;
            color: #047857;
        }

        .page-hero h2 {
            color: #065f46;
            font-weight: 700;
        }

        .page-hero p {
            color: #047857;
        }

        .page-hero .btn-success {
            background: #065f46 !important;
            border: none !important;
            border-radius: 30px !important;
            padding: 0.6rem 1.5rem !important;
            font-weight: 600 !important;
            transition: all 0.2s ease;
        }

        .page-hero .btn-success:hover {
            background: #047857 !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(6, 95, 70, 0.25);
        }

        /* ===== SOFT CARD ===== */
        .soft-card {
            border-radius: 1.5rem;
            border: 1px solid #d1fae5;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .soft-card .card-header {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-bottom: 1px solid #d1fae5;
            padding: 1rem 1.5rem;
        }

        .soft-card .card-header h4 {
            color: #065f46;
            font-weight: 700;
        }

        .soft-card .card-header .text-muted {
            color: #047857 !important;
        }

        .stat-chip {
            border-radius: 999px;
            padding: 0.4rem 1rem;
            font-weight: 600;
            font-size: .82rem;
            background: #ecfdf5 !important;
            color: #065f46 !important;
            border: 1px solid #d1fae5;
        }

        /* ===== FILTER FORM ===== */
        .filter-form {
            background: #f8fafc;
            border-radius: 1.2rem;
            padding: 1rem 1.2rem;
            border: 1px solid #e2e8f0;
        }

        .filter-form .form-control,
        .filter-form .form-select {
            border-radius: 30px !important;
            border-color: #d1fae5;
        }

        .filter-form .btn-filter {
            border-radius: 30px !important;
            background: #065f46 !important;
            border: none !important;
            color: #fff !important;
            padding: 0.45rem 1.5rem !important;
            font-weight: 600 !important;
        }

        .filter-form .btn-filter:hover {
            background: #047857 !important;
        }

        .filter-form .btn-reset {
            border-radius: 30px !important;
            background: #e2e8f0 !important;
            border: none !important;
            color: #1e293b !important;
            padding: 0.45rem 1.5rem !important;
            font-weight: 600 !important;
        }

        .filter-form .btn-reset:hover {
            background: #cbd5e1 !important;
        }

        /* ===== TABLE ===== */
        .table thead th {
            background: #ecfdf5;
            color: #065f46;
            font-weight: 600;
            border-bottom: 2px solid #d1fae5;
            padding: 0.9rem 1rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .table tbody tr:hover {
            background: #f0fdf4;
        }

        .table tbody td {
            padding: 0.9rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .text-success {
            color: #059669 !important;
        }

        .text-danger {
            color: #dc2626 !important;
        }

        .text-primary {
            color: #065f46 !important;
        }

        /* ===== ACTION BUTTONS ===== */
        .action-buttons .btn-sm {
            font-size: 0.75rem;
            padding: 0.25rem 0.7rem;
            border-radius: 30px;
        }

        .action-buttons .btn-info {
            background: #0ea5e9 !important;
            border: none !important;
            color: #fff !important;
        }

        .action-buttons .btn-info:hover {
            background: #0284c7 !important;
        }

        .action-buttons .btn-warning {
            background: #f59e0b !important;
            border: none !important;
            color: #1e293b !important;
        }

        .action-buttons .btn-warning:hover {
            background: #d97706 !important;
            color: #fff !important;
        }

        .action-buttons .btn-danger {
            background: #ef4444 !important;
            border: none !important;
        }

        .action-buttons .btn-danger:hover {
            background: #dc2626 !important;
        }

        /* ===== SUMMARY STATS ===== */
        .summary-stats {
            background: #ffffff;
            border-radius: 1.5rem;
            padding: 1.25rem 1rem;
            text-align: center;
            border: 1px solid #d1fae5;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            transition: 0.2s;
            height: 100%;
        }

        .summary-stats:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
            border-color: #6ee7b7;
        }

        .summary-stats p {
            font-size: 0.85rem;
            color: #6b7280;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .summary-stats h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0;
        }

        /* ===== MODAL ===== */
        .modal-header.bg-primary {
            background: #065f46 !important;
        }

        .modal-header.bg-warning {
            background: #f59e0b !important;
        }

        .modal-header.bg-danger {
            background: #ef4444 !important;
        }

        .modal-header .btn-close-white {
            filter: brightness(0) invert(1);
        }

        .modal-footer .btn-primary {
            background: #065f46 !important;
            border: none !important;
            border-radius: 30px !important;
            padding: 0.5rem 1.5rem !important;
            font-weight: 600 !important;
        }

        .modal-footer .btn-primary:hover {
            background: #047857 !important;
        }

        .modal-footer .btn-secondary {
            background: rgba(6, 95, 70, 0.12) !important;
            border: 1px solid #d1fae5 !important;
            border-radius: 30px !important;
            color: #065f46 !important;
            font-weight: 600 !important;
        }

        .modal-footer .btn-secondary:hover {
            background: rgba(6, 95, 70, 0.2) !important;
        }

        .modal-footer .btn-warning {
            background: #f59e0b !important;
            border: none !important;
            border-radius: 30px !important;
            font-weight: 600 !important;
        }

        .modal-footer .btn-danger {
            background: #ef4444 !important;
            border: none !important;
            border-radius: 30px !important;
            font-weight: 600 !important;
        }

        /* ===== INPUT GROUP RUPIAH ===== */
        .input-group-rupiah .input-group-text {
            background: #ecfdf5;
            border: 1px solid #d1fae5;
            color: #065f46;
            font-weight: 600;
        }

        .input-group-rupiah .form-control {
            border: 1px solid #d1fae5;
            border-left: none;
        }

        .input-group-rupiah .form-control:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        .form-label {
            color: #065f46;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .page-hero {
                padding: 1rem;
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

            .summary-stats h3 {
                font-size: 1.2rem;
            }

            .summary-stats {
                padding: 0.75rem;
            }
        }
    </style>
@endpush

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            function formatRupiah(angka) {
                if (!angka) return '';
                var clean = angka.replace(/\D/g, '');
                if (clean === '') return '';
                var number = parseInt(clean, 10);
                if (isNaN(number)) return '';
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            $(document).on('input', '.rupiah-input', function() {
                var val = $(this).val();
                var formatted = formatRupiah(val);
                $(this).val(formatted);
            });

            function initSelect2(modalId) {
                $(modalId + ' .select2').select2({
                    theme: 'default',
                    width: '100%',
                    dropdownParent: $(modalId),
                    placeholder: 'Select an option',
                    allowClear: false
                });
            }

            $('#createModal').on('shown.bs.modal', function() {
                initSelect2('#createModal');
                $('#create_amount').val('');
            });

            $('#editModal').on('shown.bs.modal', function() {
                initSelect2('#editModal');
            });

            $('#editModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');
                const type = button.data('type');
                const amount = button.data('amount');
                const description = button.data('description');
                const transactionDate = button.data('transaction_date');

                const url = "{{ route('pos.cash_summary.update', ':id') }}".replace(':id', id);
                $('#editForm').attr('action', url);

                $('#edit_id').val(id);
                $('#edit_type').val(type).trigger('change');
                $('#edit_amount').val(formatRupiah(amount.toString()));
                $('#edit_description').val(description || '');
                $('#edit_transaction_date').val(transactionDate || '');
            });

            $('#deleteModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');
                const description = button.data('description');

                const url = "{{ route('pos.cash_summary.destroy', ':id') }}".replace(':id', id);
                $('#deleteForm').attr('action', url);
                $('#delete_description').text(description || 'this record');
            });
        });
    </script>
@endpush

@section('content')
    <div class="container-fluid py-4 page-shell">
        <section class="page-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <p class="eyebrow mb-2"><i class="fas fa-cash-register me-1"></i> Cash</p>
                <h2 class="mb-1">Cash Summary Management</h2>
                <p class="mb-0">Manage all cash in and cash out records for your outlet.</p>
            </div>
            @if ($access['Create'] == 1)
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="fas fa-plus me-1"></i> Add New Record
                </button>
            @endif
        </section>

        <!-- Summary Stats -->
        <div class="row g-3 mb-4 mt-2">
            <div class="col-md-3 col-6">
                <div class="summary-stats">
                    <p><i class="fas fa-arrow-down text-success"></i> Total Cash In</p>
                    <h3 class="text-success">{{ $summary['formatted_in'] ?? 'Rp 0' }}</h3>
                    <small class="text-muted">
                        @if (!empty($filters['start_date']) && !empty($filters['end_date']))
                            {{ \Carbon\Carbon::parse($filters['start_date'])->format('d M Y') }} -
                            {{ \Carbon\Carbon::parse($filters['end_date'])->format('d M Y') }}
                        @else
                            Today
                        @endif
                    </small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="summary-stats">
                    <p><i class="fas fa-arrow-up text-danger"></i> Total Cash Out</p>
                    <h3 class="text-danger">{{ $summary['formatted_out'] ?? 'Rp 0' }}</h3>
                    <small class="text-muted">{{ $summary['count'] ?? 0 }} transaksi</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="summary-stats">
                    <p><i class="fas fa-calculator text-primary"></i> Net Balance</p>
                    <h3 class="text-primary {{ ($summary['balance'] ?? 0) < 0 ? 'text-danger' : 'text-primary' }}">
                        {{ $summary['formatted_balance'] ?? 'Rp 0' }}
                    </h3>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="summary-stats">
                    <p><i class="fas fa-receipt"></i> Total Hari</p>
                    <h3>{{ $dailySummaries->count() }}</h3>
                    <small class="text-muted">
                        @if (!empty($filters['type']))
                            Filter: {{ ucfirst($filters['type']) }}
                        @endif
                    </small>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="filter-form mb-4">
            <form method="GET" action="{{ route('pos.cash_summary.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label" for="start_date">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control"
                        value="{{ $filters['start_date'] ?? date('Y-m-01') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="end_date">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control"
                        value="{{ $filters['end_date'] ?? date('Y-m-t') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="type">Type</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">All Types</option>
                        @foreach ($types as $value => $label)
                            <option value="{{ $value }}"
                                {{ ($filters['type'] ?? '') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="search">Search</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Description..."
                        value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-filter w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                    <a href="{{ route('pos.cash_summary.index') }}" class="btn btn-reset w-100"><i
                            class="fas fa-times me-1"></i> Reset</a>
                </div>
            </form>
        </div>

        @if ($access['Read'] == 1)
            <div class="card soft-card mt-0">
                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div>
                        <h4 class="mb-1"><i class="fas fa-list me-2"></i> Daily Summary</h4>
                        <p class="text-muted mb-0">Grouped by transaction date.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="stat-chip"><i class="fas fa-calendar-day me-1"></i>
                            {{ $dailySummaries->count() }} day{{ $dailySummaries->count() > 1 ? 's' : '' }}
                        </span>
                        @if (auth()->user()->group_id == 1)
                            <form action="{{ route('pos.cash_summary.destroyAll') }}" method="POST" class="d-inline"
                                onsubmit="return confirm('⚠️ Are you sure you want to delete ALL cash records? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash-alt me-1"></i> Delete All
                                </button>
                            </form>
                        @endif
                    </div>
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
                                    <th scope="col">Date</th>
                                    <th scope="col" class="text-end">Cash In</th>
                                    <th scope="col" class="text-end">Cash Out</th>
                                    <th scope="col" class="text-end">Net Balance</th>
                                    <th scope="col" class="text-center">Transactions</th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dailySummaries as $index => $row)
                                    <tr>
                                        <td>{{ $loop->iteration + ($dailySummaries->currentPage() - 1) * $dailySummaries->perPage() }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($row['date'])->format('d M Y') }}</td>
                                        <td class="text-end text-success fw-bold">{{ $row['formatted_in'] }}</td>
                                        <td class="text-end text-danger fw-bold">{{ $row['formatted_out'] }}</td>
                                        <td
                                            class="text-end fw-bold {{ $row['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $row['formatted_balance'] }}
                                        </td>
                                        <td class="text-center">{{ $row['count'] }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('pos.cash_summary.detail', ['date' => $row['date']]) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-eye me-1"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No cash records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $dailySummaries->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- ============================================================== -->
    <!-- MODAL CREATE -->
    <!-- ============================================================== -->
    @if ($access['Create'] == 1)
        <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white"><i class="fas fa-plus me-2"></i>Add New Cash Record</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="createForm" action="{{ route('pos.cash_summary.store') }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="create_type">Type <span
                                            class="text-danger">*</span></label>
                                    <select id="create_type" name="type"
                                        class="form-select select2 @error('type') is-invalid @enderror">
                                        <option value="">Select Type</option>
                                        @foreach ($types as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('type') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="create_amount">Amount (Rp) <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group input-group-rupiah">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="create_amount" name="amount"
                                            value="{{ old('amount') ? number_format(old('amount'), 0, ',', '.') : '' }}"
                                            class="form-control rupiah-input @error('amount') is-invalid @enderror"
                                            placeholder="Contoh: 50.000">
                                    </div>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="create_description">Description</label>
                                <textarea id="create_description" name="description" rows="3"
                                    class="form-control @error('description') is-invalid @enderror" placeholder="Enter description...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="create_transaction_date">Transaction Date</label>
                                <input type="date" id="create_transaction_date" name="transaction_date"
                                    value="{{ old('transaction_date', date('Y-m-d')) }}"
                                    class="form-control @error('transaction_date') is-invalid @enderror">
                                @error('transaction_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save
                                Record</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL EDIT -->
    <!-- ============================================================== -->
    @if ($access['Update'] == 1)
        <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title text-dark"><i class="fas fa-edit me-2"></i>Edit Cash Record</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit_id" name="id">
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="edit_type">Type <span
                                            class="text-danger">*</span></label>
                                    <select id="edit_type" name="type"
                                        class="form-select select2 @error('type') is-invalid @enderror">
                                        <option value="">Select Type</option>
                                        @foreach ($types as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="edit_amount">Amount (Rp) <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group input-group-rupiah">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="edit_amount" name="amount"
                                            class="form-control rupiah-input @error('amount') is-invalid @enderror"
                                            placeholder="Contoh: 50.000">
                                    </div>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="edit_description">Description</label>
                                <textarea id="edit_description" name="description" rows="3"
                                    class="form-control @error('description') is-invalid @enderror" placeholder="Enter description..."></textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="edit_transaction_date">Transaction Date</label>
                                <input type="date" id="edit_transaction_date" name="transaction_date"
                                    class="form-control @error('transaction_date') is-invalid @enderror">
                                @error('transaction_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning text-dark"><i class="fas fa-save me-1"></i>
                                Update Record</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL DELETE -->
    <!-- ============================================================== -->
    @if ($access['Delete'] == 1)
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white"><i class="fas fa-trash me-2"></i>Delete Record</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-body text-center py-4">
                            <div class="mb-3"><i class="fas fa-trash-alt text-danger" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="mb-3">Are you sure?</h5>
                            <p class="text-muted mb-0">You are about to delete cash record: <strong
                                    id="delete_description"></strong>.</p>
                            <p class="text-danger small mt-2 mb-0">This action cannot be undone.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-2"></i> Delete
                                Record</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection
