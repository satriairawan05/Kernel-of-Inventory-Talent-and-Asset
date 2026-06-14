@extends('admin.layouts.app')

@push('css')
    <style>
        .page-shell { padding: 8px 0 24px; }
        .hero-card {
            border: none;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
        }
        .hero-card .card-body { padding: 24px; }
        .kpi-card {
            background: rgba(255,255,255,0.16);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 14px;
            padding: 14px;
            height: 100%;
        }
        .kpi-card small { color: rgba(255,255,255,0.82); }
        .kpi-card h3 { font-size: 1.35rem; font-weight: 700; color: #fff; margin-top: 6px; margin-bottom: 0; }
        .report-card {
            border: none;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        }
        .report-card .card-header { background: #fff; border-bottom: 1px solid #edf2f7; }
        .table-report th { background: #f8fafc; color: #475569; }
        .amount-text { font-family: monospace; font-weight: 700; }
        .chip { display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 6px 10px; background: #eef2ff; color: #4338ca; font-size: 0.85rem; }

        @media (max-width: 768px) {

            .card-header h4 {
                font-size: 1rem;
                margin-bottom: 0;
            }

            .card-body {
                padding: 1rem;
            }

            .btn-add-company {
                width: 100%;
                margin-bottom: 1rem;
            }

            .action-buttons {
                display: flex;
                flex-direction: column;
                gap: .5rem;
                min-width: 100px;
            }

            .action-buttons .btn {
                width: 100%;
            }

            .company-logo {
                max-width: 50px;
                max-height: 50px;
            }

            .pagination-wrapper {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .pagination-wrapper>div {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="page-shell">
        <div class="card hero-card mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <span class="chip mb-2"><i class="fas fa-calendar-day"></i> Daily Detail Report</span>
                        <h3 class="mb-1">Daily Sales Detail</h3>
                        <p class="mb-0 text-white-50">Report date {{ \Carbon\Carbon::parse($salesReport['report_date'] ?? now())->format('d M Y') }} • Arrived {{ \Carbon\Carbon::parse($salesReport['arrived_date'] ?? now())->format('d M Y') }}</p>
                    </div>
                    <a href="{{ route('pos.report.daily') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-1"></i> Back to Daily List
                    </a>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-3 col-sm-6">
                        <div class="kpi-card">
                            <small>Outets</small>
                            <h3>{{ $salesReport['summary']['total_companies'] ?? 0 }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="kpi-card">
                            <small>Accessories</small>
                            <h3>{{ \Carbon\Carbon::rupiah($salesReport['summary']['total_accessories'] ?? 0, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="kpi-card">
                            <small>Service</small>
                            <h3>{{ \Carbon\Carbon::rupiah($salesReport['summary']['total_service'] ?? 0, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="kpi-card">
                            <small>Grand Total</small>
                            <h3>{{ \Carbon\Carbon::rupiah($salesReport['summary']['grand_total'] ?? 0, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card report-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0"><i class="fas fa-table me-2"></i> Company Breakdown</h5>
                    <small class="text-muted">Transaction details for the selected daily report.</small>
                </div>
                <span class="badge bg-primary rounded-pill">{{ count($salesReport['data'] ?? []) }} record</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-report align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Outlet</th>
                                <th class="text-end">Accessories</th>
                                <th class="text-end">Service</th>
                                <th class="text-end">Pulsa</th>
                                <th class="text-end">Total</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salesReport['data'] as $index => $report)
                                <tr>
                                    <td class="text-center"><span class="badge bg-secondary rounded-pill">{{ $index + 1 }}</span></td>
                                    <td>
                                        <strong>{{ $salesReport['company_name'] ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">ID {{ $report['company_id'] ?? '-' }}</small>
                                    </td>
                                    <td class="text-end"><span class="amount-text text-success">{{ \Carbon\Carbon::rupiah($report['accessories_amount'] ?? 0, 0, ',', '.') }}</span></td>
                                    <td class="text-end"><span class="amount-text text-info">{{ \Carbon\Carbon::rupiah($report['service_amount'] ?? 0, 0, ',', '.') }}</span></td>
                                    <td class="text-end"><span class="amount-text text-warning">{{ \Carbon\Carbon::rupiah($report['pulsa_amount'] ?? 0, 0, ',', '.') }}</span></td>
                                    <td class="text-end"><strong class="amount-text text-primary">{{ \Carbon\Carbon::rupiah($report['total_amount'] ?? 0, 0, ',', '.') }}</strong></td>
                                    <td>{{ $report['notes'] ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">No daily detail data found for this report.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
