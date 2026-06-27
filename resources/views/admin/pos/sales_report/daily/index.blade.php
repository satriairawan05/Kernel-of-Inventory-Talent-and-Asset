@extends('admin.layouts.app')

@push('css')
    <style>
        /* ===== HERO FILTER CARD ===== */
        .filter-card {
            background: linear-gradient(135deg, #ccf0f2 0%, #a7f3d0 100%) !important;
            border: none;
            border-radius: 2rem;
            margin-bottom: 2rem;
            padding: 0;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(16, 185, 129, 0.2);
            overflow: hidden;
        }

        .filter-card .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(16, 185, 129, 0.25);
            padding: 1.25rem 1.5rem;
        }

        .filter-card .card-header h4 {
            color: #065f46;
            font-weight: 700;
        }

        .filter-card .card-header p {
            color: #047857;
            margin: 0;
        }

        .filter-card .card-body {
            padding: 1.5rem;
        }

        .btn-add-report {
            background: #065f46;
            border: none;
            color: #fff;
            transition: 0.2s;
            border-radius: 30px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
        }

        .btn-add-report:hover {
            background: #047857;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(6, 95, 70, 0.25);
        }

        /* ===== SUMMARY STATS ===== */
        .summary-stats {
            background: #ffffff;
            border-radius: 1.5rem;
            padding: 1.25rem 1rem;
            text-align: center;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
            border: 1px solid #d1fae5;
            height: 100%;
            transition: 0.2s;
        }

        .summary-stats:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
            border-color: #6ee7b7;
        }

        .summary-stats p {
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 6px;
            font-weight: 500;
        }

        .summary-stats h3 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #065f46;
            margin: 0;
        }

        .summary-stats .icon-circle {
            width: 42px;
            height: 42px;
            background: #ecfdf5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 6px;
            color: #10b981;
            font-size: 1.2rem;
        }

        /* ===== REPORT CARD ===== */
        .report-card {
            border-radius: 1.5rem;
            overflow: hidden;
            border: 1px solid #d1fae5;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
            background: #fff;
        }

        .report-card .card-header {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            color: #065f46;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #d1fae5;
        }

        .report-card .card-header h5 {
            font-weight: 700;
        }

        .report-card .card-header .badge {
            background: #065f46 !important;
            color: #fff !important;
        }

        .report-card .card-footer {
            background: #f8fafc;
            border-top: 1px solid #d1fae5;
            padding: 0.75rem 1.5rem;
        }

        /* ===== TABLE ===== */
        .table-daily thead th {
            background: #ecfdf5;
            color: #065f46;
            font-weight: 600;
            border-bottom: 2px solid #d1fae5;
            padding: 0.9rem 1rem;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .table-daily tbody td {
            padding: 0.9rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-daily tbody tr:hover {
            background: #f0fdf4;
        }

        .table-daily tbody tr:last-child td {
            border-bottom: none;
        }

        /* ===== BADGE DAY ===== */
        .badge-day {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
            border-radius: 999px;
            padding: 0.3rem 1rem;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-weight: 600;
        }

        .badge-day i {
            font-size: 0.9rem;
        }

        /* ===== AMOUNT TEXT ===== */
        .amount-text {
            font-family: 'Courier New', monospace;
            font-size: 0.95rem;
            font-weight: 700;
        }

        .amount-text.text-success { color: #059669; }
        .amount-text.text-info { color: #0ea5e9; }
        .amount-text.text-warning { color: #d97706; }
        .amount-text.text-primary { color: #065f46; }

        /* ===== ACTION BUTTONS ===== */
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            justify-content: center;
        }

        .action-buttons .btn-sm {
            padding: 0.25rem 0.7rem;
            font-size: 0.75rem;
            border-radius: 30px;
        }

        .btn-subtle-primary {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #d1fae5;
        }

        .btn-subtle-primary:hover {
            background: #d1fae5;
            color: #047857;
            border-color: #6ee7b7;
        }

        .btn-edit-green {
            background: #10b981;
            color: #fff;
            border: none;
        }

        .btn-edit-green:hover {
            background: #059669;
            color: #fff;
        }

        .btn-delete-green {
            background: #ef4444;
            color: #fff;
            border: none;
        }

        .btn-delete-green:hover {
            background: #dc2626;
            color: #fff;
        }

        /* ===== MODAL DELETE ===== */
        .modal-header.bg-danger {
            background: #ef4444 !important;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .filter-card .card-body {
                padding: 1rem;
            }

            .btn-add-report {
                width: 100%;
            }

            .summary-stats h3 {
                font-size: 1.2rem;
            }

            .action-buttons .btn {
                width: 100%;
            }

            .pagination-wrapper {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .badge-day {
                font-size: 0.7rem;
                padding: 0.2rem 0.7rem;
            }

            .table-daily thead th,
            .table-daily tbody td {
                padding: 0.6rem 0.5rem;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 576px) {
            .filter-card .card-header {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.5rem;
            }

            .filter-card .card-header h4 {
                font-size: 1.1rem;
            }

            .summary-stats {
                padding: 0.75rem;
            }
        }
    </style>
@endpush

@section('content')
    <!-- ===== HERO FILTER CARD ===== -->
    <div class="card filter-card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="card-title mb-1"><i class="fas fa-calendar-day me-2"></i>Daily Sales Report</h4>
                <p class="small mb-0"><i class="fas fa-chart-line me-1"></i> Daily Recap</p>
            </div>
            @if ($access['Create'])
                <a href="{{ route('pos.report.create') }}" class="btn btn-add-report">
                    <i class="fas fa-plus me-1"></i> Add New Report
                </a>
            @endif
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="summary-stats">
                        <div class="icon-circle"><i class="fas fa-file-alt"></i></div>
                        <p>Total Reports</p>
                        <h3>{{ $reports->total() }}</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-stats">
                        <div class="icon-circle"><i class="fas fa-coins"></i></div>
                        <p>Grand Total</p>
                        <h3>{{ \Carbon\Carbon::rupiah($reports->sum('total_amount'), 0, ',', '.') }}</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-stats">
                        <div class="icon-circle"><i class="fas fa-chart-line"></i></div>
                        <p>Avg / Report</p>
                        <h3>{{ \Carbon\Carbon::rupiah($reports->total() > 0 ? $reports->sum('total_amount') / $reports->total() : 0, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== REPORT LIST ===== -->
    @if ($access['Read'] == 1)
        <div class="card report-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-table me-2"></i> Daily Report List</h5>
                <span class="badge bg-light text-dark">{{ $reports->count() }} item</span>
            </div>
            <div class="card-body p-0">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if (session('failed'))
                    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                        {{ session('failed') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive mb-0">
                    <table class="table table-daily align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center" width="60">#</th>
                                <th scope="col">Date</th>
                                <th scope="col">Company</th>
                                <th scope="col" class="text-end">Accessories</th>
                                <th scope="col" class="text-end">Service</th>
                                <th scope="col" class="text-end">Pulsa</th>
                                <th scope="col" class="text-end">Total</th>
                                <th scope="col" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $report)
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill">{{ $reports->firstItem() + $loop->index }}</span>
                                    </td>
                                    <td>
                                        <span class="badge-day"><i class="fas fa-calendar-alt"></i>
                                            {{ \Carbon\Carbon::parse($report->report_date ?? ($report->arrived_date ?? now()))->format('d M Y') }}</span>
                                        <br>
                                        <small class="text-muted">Arrived:
                                            {{ \Carbon\Carbon::parse($report->arrived_date ?? ($report->report_date ?? now()))->format('d M Y') }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $report->company->company_name ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">ID: {{ $report->company_id ?? '-' }}</small>
                                    </td>
                                    <td class="text-end">
                                        <span class="amount-text text-success">{{ \Carbon\Carbon::rupiah($report->accessories_amount ?? 0, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="amount-text text-info">{{ \Carbon\Carbon::rupiah($report->service_amount ?? 0, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="amount-text text-warning">{{ \Carbon\Carbon::rupiah($report->pulsa_amount ?? 0, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="text-end">
                                        <strong class="amount-text text-primary">{{ \Carbon\Carbon::rupiah($report->total_amount ?? 0, 0, ',', '.') }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-buttons">
                                            <a href="{{ route('pos.report.show', $report) }}" class="btn btn-sm btn-subtle-primary">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                            @if ($access['Update'] == 1)
                                                <a href="{{ route('pos.report.edit', $report) }}" class="btn btn-sm btn-edit-green">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            @endif
                                            @if ($access['Delete'] == 1)
                                                <button type="button" class="btn btn-sm btn-delete-green" data-bs-toggle="modal"
                                                    data-bs-target="#deleteReportModal{{ $report->id }}">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            @endif
                                        </div>

                                        @if ($access['Delete'] == 1)
                                            <div class="modal fade" id="deleteReportModal{{ $report->id }}"
                                                tabindex="-1" data-bs-backdrop="static"
                                                aria-labelledby="deleteReportModalLabel{{ $report->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title" id="deleteReportModalLabel{{ $report->id }}">
                                                                <i class="fas fa-triangle-exclamation me-2"></i> Delete Report
                                                            </h5>
                                                            <button class="btn p-1" type="button" data-bs-dismiss="modal"
                                                                aria-label="Close">
                                                                <i class="fas fa-times text-white"></i>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body text-center py-4">
                                                            <div class="mb-3"><i class="fas fa-trash-alt text-danger"
                                                                    style="font-size: 4rem;"></i></div>
                                                            <h5 class="mb-3">Are you sure?</h5>
                                                            <p class="text-muted mb-0">You are about to delete the daily
                                                                report for
                                                                <strong>{{ \Carbon\Carbon::parse($report->report_date ?? ($report->arrived_date ?? now()))->format('d M Y') }}</strong>.
                                                            </p>
                                                            <p class="text-danger small mt-2 mb-0">This action cannot be
                                                                undone.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-outline-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            <form action="{{ route('pos.report.destroy', $report) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger"><i
                                                                        class="fas fa-trash me-2"></i> Delete
                                                                    report</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        No daily report data found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pagination-wrapper">
                    <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Showing {{ $reports->count() }} of
                        {{ $reports->total() }} records</small>
                    {{ $reports->links() }}
                </div>
            </div>
        </div>
    @endif
@endsection