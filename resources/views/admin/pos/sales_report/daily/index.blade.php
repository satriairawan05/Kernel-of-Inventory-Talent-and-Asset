@extends('admin.layouts.app')

@push('css')
    <style>
        .filter-card {
            background: linear-gradient(135deg, #0f172a 0%, #2563eb 45%, #22d3ee 100%);
            border: none;
            border-radius: 18px;
            margin-bottom: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        }

        .filter-card .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(255,255,255,0.18);
            padding: 18px 22px;
        }

        .filter-card .card-header h4,
        .filter-card .card-header p {
            color: #fff;
            margin: 0;
        }

        .filter-card .card-body {
            padding: 22px;
        }

        .report-card {
            border-radius: 18px;
            overflow: hidden;
            border: none;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }

        .report-card .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 15px 18px;
        }

        .summary-stats {
            background: #fff;
            border-radius: 14px;
            padding: 16px;
            text-align: center;
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
            height: 100%;
        }

        .summary-stats p {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 6px;
        }

        .summary-stats h3 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #4f46e5;
            margin: 0;
        }

        .table-daily thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
            padding: 14px;
        }

        .table-daily tbody td {
            padding: 14px;
            vertical-align: middle;
        }

        .table-daily tbody tr:hover {
            background: #f8f9fa;
        }

        .badge-day {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .amount-text {
            font-family: monospace;
            font-size: 0.95rem;
            font-weight: 700;
        }

        .action-buttons { display: flex; flex-wrap: wrap; gap: 6px; }

        @media (max-width: 768px) {
            .filter-card .card-body { padding: 1rem; }
            .btn-add-company { width: 100%; margin-bottom: 10px; }
            .summary-stats h3 { font-size: 1.2rem; }
            .action-buttons .btn { width: 100%; }
            .pagination-wrapper { flex-direction: column; gap: 1rem; text-align: center; }
        }
    </style>
@endpush

@section('content')
    <div class="card filter-card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="card-title mb-1"><i class="fas fa-calendar-day me-2"></i>Daily Sales Report</h4>
                <p class="small mb-0 text-white-50">Ringkasan harian dengan tampilan lebih rapi seperti weekly & monthly.</p>
            </div>
            @if ($access['Create'])
            <a href="{{ route('pos.report.create') }}" class="btn btn-success btn-add-company">
                <i class="fas fa-plus me-1"></i> Add New Report
            </a>
            @endif
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="summary-stats">
                        <p><i class="fas fa-file-alt me-1"></i> Total Reports</p>
                        <h3>{{ $reports->total() }}</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-stats">
                        <p><i class="fas fa-coins me-1"></i> Grand Total</p>
                        <h3>{{ \Carbon\Carbon::rupiah($reports->sum('total_amount'), 0, ',', '.') }}</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-stats">
                        <p><i class="fas fa-chart-line me-1"></i> Avg / Report</p>
                        <h3>{{ \Carbon\Carbon::rupiah($reports->total() > 0 ? $reports->sum('total_amount') / $reports->total() : 0, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($access['Read'] == 1)
    <div class="card report-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i> Daily Report List</h5>
            <span class="badge bg-light text-dark">{{ $reports->count() }} item</span>
        </div>
        <div class="card-body p-0">
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
                                <td class="text-center"><span class="badge bg-secondary rounded-pill">{{ $reports->firstItem() + $loop->index }}</span></td>
                                <td>
                                    <span class="badge-day"><i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($report->report_date ?? $report->arrived_date ?? now())->format('d M Y') }}</span>
                                    <br>
                                    <small class="text-muted">Arrived: {{ \Carbon\Carbon::parse($report->arrived_date ?? $report->report_date ?? now())->format('d M Y') }}</small>
                                </td>
                                <td>
                                    <strong>{{ $report->company->company_name ?? 'N/A' }}</strong>
                                    <br>
                                    <small class="text-muted">ID: {{ $report->company_id ?? '-' }}</small>
                                </td>
                                <td class="text-end"><span class="amount-text text-success">{{ \Carbon\Carbon::rupiah($report->accessories_amount ?? 0, 0, ',', '.') }}</span></td>
                                <td class="text-end"><span class="amount-text text-info">{{ \Carbon\Carbon::rupiah($report->service_amount ?? 0, 0, ',', '.') }}</span></td>
                                <td class="text-end"><span class="amount-text text-warning">{{ \Carbon\Carbon::rupiah($report->pulsa_amount ?? 0, 0, ',', '.') }}</span></td>
                                <td class="text-end"><strong class="amount-text text-primary">{{ \Carbon\Carbon::rupiah($report->total_amount ?? 0, 0, ',', '.') }}</strong></td>
                                <td class="text-center">
                                    <div class="action-buttons justify-content-center">
                                        <a href="{{ route('pos.report.show', $report) }}" class="btn btn-sm btn-subtle-primary">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        @if ($access['Update'] == 1)
                                        <a href="{{ route('pos.report.edit', $report) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        @endif
                                        @if ($access['Delete'] == 1)
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteReportModal{{ $report->id }}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                        @endif
                                    </div>

                                    @if ($access['Delete'] == 1)
                                    <div class="modal fade" id="deleteReportModal{{ $report->id }}" tabindex="-1" data-bs-backdrop="static" aria-labelledby="deleteReportModalLabel{{ $report->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-danger">
                                                    <h5 class="modal-title text-white" id="deleteReportModalLabel{{ $report->id }}">
                                                        <i class="fas fa-triangle-exclamation me-2"></i> Delete Report
                                                    </h5>
                                                    <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close">
                                                        <i class="fas fa-times text-white"></i>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-center py-4">
                                                    <div class="mb-3"><i class="fas fa-trash-alt text-danger" style="font-size: 4rem;"></i></div>
                                                    <h5 class="mb-3">Are you sure?</h5>
                                                    <p class="text-muted mb-0">You are about to delete the daily report for <strong>{{ \Carbon\Carbon::parse($report->report_date ?? $report->arrived_date ?? now())->format('d M Y') }}</strong>.</p>
                                                    <p class="text-danger small mt-2 mb-0">This action cannot be undone.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('pos.report.destroy', $report) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-2"></i> Delete report</button>
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
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pagination-wrapper">
                <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Showing {{ $reports->count() }} of {{ $reports->total() }} records</small>
                {{ $reports->links() }}
            </div>
        </div>
    </div>
    @endif
@endsection
