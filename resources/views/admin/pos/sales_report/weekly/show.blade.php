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
        .badge-pill { border-radius: 999px; padding: 6px 10px; }
        .amount-text { font-family: monospace; font-weight: 700; }
        .chip { display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 6px 10px; background: #eef2ff; color: #4338ca; font-size: 0.85rem; }
    </style>
@endpush

@section('content')
    <div class="page-shell">
        <div class="card hero-card mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <span class="chip mb-2"><i class="fas fa-calendar-week"></i> Weekly Detail Report</span>
                        <h3 class="mb-1">Weekly Sales Overview</h3>
                        <p class="mb-0 text-white-50">Period: {{ $startDate }} to {{ $endDate }} @if($companyId) • Company ID {{ $companyId }} @endif</p>
                    </div>
                    <a href="{{ route('pos.report.weekly') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-1"></i> Back to Weekly List
                    </a>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-3 col-sm-6">
                        <div class="kpi-card">
                            <small>Total Weeks</small>
                            <h3>{{ $report['summary']['total_weeks'] ?? 0 }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="kpi-card">
                            <small>Transactions</small>
                            <h3>{{ number_format($report['summary']['total_transactions'] ?? 0) }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="kpi-card">
                            <small>Grand Total</small>
                            <h3>{{ \Carbon\Carbon::rupiah($report['summary']['grand_total'] ?? 0, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="kpi-card">
                            <small>Average / Week</small>
                            <h3>{{ \Carbon\Carbon::rupiah(($report['summary']['total_weeks'] ?? 0) > 0 ? ($report['summary']['grand_total'] ?? 0) / ($report['summary']['total_weeks'] ?? 1) : 0, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card report-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0"><i class="fas fa-table me-2"></i> Weekly Period Breakdown</h5>
                    <small class="text-muted">Each row represents one week period from the selected range.</small>
                </div>
                <span class="badge bg-primary badge-pill">{{ count($report['data'] ?? []) }} item</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-report align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Week Period</th>
                                <th class="text-end">Accessories</th>
                                <th class="text-end">Service</th>
                                <th class="text-end">Pulsa</th>
                                <th class="text-end">Total Amount</th>
                                <th class="text-center">Transactions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report['data'] ?? [] as $index => $item)
                                <tr>
                                    <td class="text-center"><span class="badge bg-secondary rounded-pill">{{ $index + 1 }}</span></td>
                                    <td>
                                        <strong>{{ $item['week_display'] ?? 'Week ' . ($item['week_number'] ?? '-') }}</strong>
                                        <br>
                                        <small class="text-muted">Year {{ $item['year'] ?? '-' }} • Week {{ $item['week_number'] ?? '-' }}</small>
                                    </td>
                                    <td class="text-end"><span class="amount-text text-success">{{ \Carbon\Carbon::rupiah($item['accessories_amount'] ?? 0, 0, ',', '.') }}</span></td>
                                    <td class="text-end"><span class="amount-text text-info">{{ \Carbon\Carbon::rupiah($item['service_amount'] ?? 0, 0, ',', '.') }}</span></td>
                                    <td class="text-end"><span class="amount-text text-warning">{{ \Carbon\Carbon::rupiah($item['pulsa_amount'] ?? 0, 0, ',', '.') }}</span></td>
                                    <td class="text-end"><strong class="amount-text text-primary">{{ \Carbon\Carbon::rupiah($item['total_amount'] ?? 0, 0, ',', '.') }}</strong></td>
                                    <td class="text-center"><span class="badge bg-info rounded-pill">{{ $item['total_transactions'] ?? 0 }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">No weekly detail data found for the selected period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
