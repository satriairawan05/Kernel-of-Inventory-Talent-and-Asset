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
        .amount-text { font-family: monospace; font-weight: 700; }
        .badge-type {
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-cash-in { background: #dcfce7; color: #16a34a; }
        .badge-cash-out { background: #fee2e2; color: #dc2626; }
        .summary-stats {
            background: #f8fafc;
            border-radius: 12px;
            padding: 12px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }
        .summary-stats h3 { font-size: 1.2rem; font-weight: 700; margin-bottom: 0; }
        .summary-stats p { font-size: 0.8rem; color: #64748b; margin-bottom: 4px; }
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {

            // ==================== FORMAT RUPIAH ====================
            function formatRupiah(angka) {
                if (!angka) return '';
                var clean = angka.replace(/\D/g, '');
                if (clean === '') return '';
                var number = parseInt(clean, 10);
                if (isNaN(number)) return '';
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function parseRupiah(value) {
                if (!value) return 0;
                var clean = value.replace(/\D/g, '');
                return parseInt(clean, 10) || 0;
            }

            $(document).on('input', '.rupiah-input', function() {
                var val = $(this).val();
                var formatted = formatRupiah(val);
                $(this).val(formatted);
            });

            // ==================== SELECT2 ====================
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

            // ==================== EDIT MODAL ====================
            $('#editModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');
                const type = button.data('type');
                const amount = button.data('amount');
                const description = button.data('description');
                const transactionDate = button.data('transaction_date');

                const url = "{{ route('cash-summary.update', ':id') }}".replace(':id', id);
                $('#editForm').attr('action', url);

                $('#edit_id').val(id);
                $('#edit_type').val(type).trigger('change');
                $('#edit_amount').val(formatRupiah(amount.toString()));
                $('#edit_description').val(description || '');
                $('#edit_transaction_date').val(transactionDate || '');
            });

            // ==================== DELETE MODAL ====================
            $('#deleteModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');
                const description = button.data('description');

                const url = "{{ route('cash-summary.destroy', ':id') }}".replace(':id', id);
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
            <p class="eyebrow mb-2">Cash</p>
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
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="summary-stats">
                <p><i class="fas fa-arrow-down text-success"></i> Total Cash In</p>
                <h3 class="text-success">{{ $summary['formatted_in'] ?? 'Rp 0' }}</h3>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="summary-stats">
                <p><i class="fas fa-arrow-up text-danger"></i> Total Cash Out</p>
                <h3 class="text-danger">{{ $summary['formatted_out'] ?? 'Rp 0' }}</h3>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="summary-stats">
                <p><i class="fas fa-calculator text-primary"></i> Net Balance</p>
                <h3 class="text-primary">{{ $summary['formatted_balance'] ?? 'Rp 0' }}</h3>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="summary-stats">
                <p><i class="fas fa-receipt"></i> Total Records</p>
                <h3>{{ $summary['count'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    @if ($access['Read'] == 1)
    <div class="card soft-card mt-0">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h4 class="mb-1">Cash Records</h4>
                <p class="text-muted mb-0">All cash in and cash out transactions.</p>
            </div>
            <span class="stat-chip bg-primary-subtle text-primary"><i class="fas fa-cash-register me-1"></i>
                {{ $cashSummaries->total() }} total records</span>
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
                            <th scope="col">Type</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Description</th>
                            <th scope="col">Transaction Date</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cashSummaries as $item)
                            <tr>
                                <th scope="row">{{ $cashSummaries->firstItem() + $loop->index }}</th>
                                <td>
                                    @php
                                        $badgeClass = $item->type === 'cash_in' ? 'badge-cash-in' : 'badge-cash-out';
                                    @endphp
                                    <span class="badge-type {{ $badgeClass }}">
                                        <i class="fas {{ $item->type === 'cash_in' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                                        {{ $item->type_label }}
                                    </span>
                                </td>
                                <td>
                                    <span class="amount-text {{ $item->type === 'cash_in' ? 'text-success' : 'text-danger' }}">
                                        {{ $item->formatted_amount }}
                                    </span>
                                </td>
                                <td>{{ $item->description ?? '-' }}</td>
                                <td>{{ $item->transaction_date->format('d M Y') }}</td>
                                <td class="action-buttons d-md-flex flex-md-row align-items-md-center gap-2">
                                    @if ($access['Update'] == 1)
                                        <button type="button" class="btn btn-sm btn-warning btn-edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal"
                                            data-id="{{ $item->id }}"
                                            data-type="{{ $item->type }}"
                                            data-amount="{{ $item->amount }}"
                                            data-description="{{ $item->description }}"
                                            data-transaction_date="{{ $item->transaction_date->format('Y-m-d') }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    @endif
                                    @if ($access['Delete'] == 1)
                                        <button type="button" class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            data-id="{{ $item->id }}"
                                            data-description="{{ $item->description ?? 'record #' . $item->id }}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No cash records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3 pagination-wrapper">
                {{ $cashSummaries->links() }}
            </div>
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
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add New Cash Record</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="createForm" action="{{ route('cash-summary.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="create_type">Type <span class="text-danger">*</span></label>
                            <select id="create_type" name="type" class="form-select select2 @error('type') is-invalid @enderror">
                                <option value="">Select Type</option>
                                @foreach($types as $value => $label)
                                    <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="create_amount">Amount (Rp) <span class="text-danger">*</span></label>
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
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Enter description...">{{ old('description') }}</textarea>
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
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Record</button>
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
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Cash Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="edit_type">Type <span class="text-danger">*</span></label>
                            <select id="edit_type" name="type" class="form-select select2 @error('type') is-invalid @enderror">
                                <option value="">Select Type</option>
                                @foreach($types as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="edit_amount">Amount (Rp) <span class="text-danger">*</span></label>
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
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Enter description..."></textarea>
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
                    <button type="submit" class="btn btn-warning text-dark"><i class="fas fa-save me-1"></i> Update Record</button>
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
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Delete Record</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center py-4">
                    <div class="mb-3"><i class="fas fa-trash-alt text-danger" style="font-size: 4rem;"></i></div>
                    <h5 class="mb-3">Are you sure?</h5>
                    <p class="text-muted mb-0">You are about to delete cash record: <strong id="delete_description"></strong>.</p>
                    <p class="text-danger small mt-2 mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-2"></i> Delete Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection