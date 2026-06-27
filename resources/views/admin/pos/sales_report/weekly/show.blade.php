@extends('admin.layouts.app')

@push('css')
    <style>
        /* ===== HERO CARD ===== */
        .page-shell { padding: 8px 0 24px; }

        .hero-card {
            border: none;
            border-radius: 2rem;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            background: linear-gradient(135deg, #ccf0f2 0%, #a7f3d0 100%) !important;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .hero-card .card-body {
            padding: 1.5rem;
        }

        /* ===== CHIP ===== */
        .chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            border-radius: 999px;
            padding: 0.25rem 0.9rem;
            background: #ecfdf5 !important;
            color: #065f46 !important;
            font-size: 0.85rem;
            font-weight: 600;
            border: 1px solid #d1fae5;
        }

        .chip i {
            color: #10b981;
        }

        /* ===== HERO TEXT ===== */
        .hero-card h3 {
            color: #065f46 !important;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .hero-card .text-white-50 {
            color: #047857 !important;
        }

        /* ===== KPI CARDS ===== */
        .kpi-card {
            background: #ffffff;
            border: 1px solid #d1fae5;
            border-radius: 1.5rem;
            padding: 1.25rem 1rem;
            height: 100%;
            transition: 0.2s;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        .kpi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
            border-color: #6ee7b7;
        }

        .kpi-card small {
            color: #6b7280;
            font-weight: 500;
            font-size: 0.85rem;
        }

        .kpi-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #065f46;
            margin-top: 0.25rem;
            margin-bottom: 0;
        }

        /* ===== BUTTON BACK ===== */
        .btn-light {
            background: #ffffff !important;
            border: 1px solid #d1fae5 !important;
            border-radius: 30px !important;
            color: #065f46 !important;
            font-weight: 600 !important;
            padding: 0.5rem 1.5rem !important;
            transition: 0.2s;
        }

        .btn-light:hover {
            background: #ecfdf5 !important;
            border-color: #6ee7b7 !important;
            color: #047857 !important;
        }

        /* ===== REPORT CARD ===== */
        .report-card {
            border: none;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
            border: 1px solid #d1fae5;
        }

        .report-card .card-header {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-bottom: 1px solid #d1fae5;
            padding: 1rem 1.5rem;
        }

        .report-card .card-header h5 {
            color: #065f46;
            font-weight: 700;
        }

        .report-card .card-header .badge {
            background: #065f46 !important;
            color: #fff;
        }

        .report-card .card-body {
            padding: 0;
        }

        /* ===== TABLE ===== */
        .table-report thead th {
            background: #ecfdf5;
            color: #065f46;
            font-weight: 600;
            border-bottom: 2px solid #d1fae5;
            padding: 0.9rem 1rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .table-report tbody td {
            padding: 0.9rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-report tbody tr:hover {
            background: #f0fdf4;
        }

        .table-report tbody tr:last-child td {
            border-bottom: none;
        }

        .amount-text {
            font-family: 'Courier New', monospace;
            font-weight: 700;
        }

        .amount-text.text-success { color: #059669; }
        .amount-text.text-info { color: #0ea5e9; }
        .amount-text.text-warning { color: #d97706; }
        .amount-text.text-primary { color: #065f46; }

        /* ===== BADGE ===== */
        .badge.bg-secondary {
            background: #d1d5db !important;
            color: #1e293b;
        }

        .badge.bg-primary {
            background: #065f46 !important;
            color: #fff;
        }

        .badge.bg-info {
            background: #0ea5e9 !important;
            color: #fff;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .hero-card .card-body {
                padding: 1rem;
            }

            .kpi-card h3 {
                font-size: 1.2rem;
            }

            .table-report thead th,
            .table-report tbody td {
                padding: 0.5rem;
                font-size: 0.75rem;
            }

            .amount-text {
                font-size: 0.75rem;
            }

            .chip {
                font-size: 0.7rem;
                padding: 0.15rem 0.6rem;
            }

            .btn-light {
                padding: 0.4rem 1rem;
                font-size: 0.85rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="page-shell">
        <!-- ===== HERO CARD ===== -->
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

                <!-- KPI Cards -->
                <div class="row g-3 mt-3">
                    <div class="col-md-3 col-sm-6">
                        <div class="kpi-card">
                            <small><i class="fas fa-calendar-alt me-1"></i> Total Weeks</small>
                            <h3>{{ $report['summary']['total_weeks'] ?? 0 }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="kpi-card">
                            <small><i class="fas fa-receipt me-1"></i> Transactions</small>
                            <h3>{{ number_format($report['summary']['total_transactions'] ?? 0) }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="kpi-card">
                            <small><i class="fas fa-cash-stack me-1"></i> Grand Total</small>
                            <h3>{{ \Carbon\Carbon::rupiah($report['summary']['grand_total'] ?? 0, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="kpi-card">
                            <small><i class="fas fa-chart-line me-1"></i> Average / Week</small>
                            <h3>{{ \Carbon\Carbon::rupiah(($report['summary']['total_weeks'] ?? 0) > 0 ? ($report['summary']['grand_total'] ?? 0) / ($report['summary']['total_weeks'] ?? 1) : 0, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TABLE CARD ===== -->
        <div class="card report-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0"><i class="fas fa-table me-2"></i> Weekly Period Breakdown</h5>
                    <small class="text-muted" style="color: #047857 !important;">Each row represents one week period from the selected range.</small>
                </div>
                <span class="badge bg-primary rounded-pill">{{ count($report['data'] ?? []) }} item</span>
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