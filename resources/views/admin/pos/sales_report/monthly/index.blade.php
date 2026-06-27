@extends('admin.layouts.app')

@push('css')
    <style>
        /* ===== FILTER CARD ===== */
        .filter-card {
            background: linear-gradient(135deg, #ccf0f2 0%, #a7f3d0 100%) !important;
            border: none;
            border-radius: 2rem;
            margin-bottom: 2rem;
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
            margin: 0;
        }

        .filter-card .card-body {
            padding: 1.5rem;
        }

        .filter-card .form-label {
            color: #065f46;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .filter-card .form-control,
        .filter-card .form-select {
            border-radius: 10px;
            border: 1px solid #d1fae5;
            padding: 0.6rem 1rem;
            background: #ffffff;
            color: #1e293b;
        }

        .filter-card .form-control:focus,
        .filter-card .form-select:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        .btn-primary {
            background: #065f46 !important;
            border: none !important;
            border-radius: 30px !important;
            padding: 0.6rem 2rem !important;
            font-weight: 600 !important;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background: #047857 !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(6, 95, 70, 0.25);
        }

        .btn-secondary {
            background: rgba(6, 95, 70, 0.12) !important;
            border: 1px solid #d1fae5 !important;
            border-radius: 30px !important;
            padding: 0.6rem 1.5rem !important;
            color: #065f46 !important;
            font-weight: 600 !important;
        }

        .btn-secondary:hover {
            background: rgba(6, 95, 70, 0.2) !important;
            color: #065f46 !important;
        }

        .quick-filter .btn-filter {
            border-radius: 30px;
            padding: 0.2rem 1rem;
            font-size: 0.75rem;
            border: 1px solid #d1fae5;
            color: #065f46;
            background: white;
            transition: 0.2s;
        }

        .quick-filter .btn-filter:hover {
            background: #ecfdf5;
            border-color: #6ee7b7;
        }

        .quick-filter .btn-filter.btn-outline-primary {
            border-color: #10b981;
            color: #065f46;
        }

        .quick-filter .btn-filter.btn-outline-secondary {
            border-color: #d1fae5;
            color: #6b7280;
        }

        .quick-filter {
            background: #f8fafc;
            border-radius: 12px;
            padding: 0.6rem 1rem;
            margin-top: 1rem;
            border: 1px solid #d1fae5;
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
            margin-bottom: 4px;
            font-weight: 500;
        }

        .summary-stats h3 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #065f46;
            margin: 0;
        }

        .summary-stats i {
            color: #10b981;
            margin-right: 4px;
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

        .report-card .card-footer {
            background: #f8fafc;
            border-top: 1px solid #d1fae5;
        }

        /* ===== TABLE ===== */
        .table-monthly thead th {
            background: #ecfdf5;
            color: #065f46;
            font-weight: 600;
            border-bottom: 2px solid #d1fae5;
            padding: 0.9rem 1rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .table-monthly tbody td {
            padding: 0.9rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-monthly tbody tr:hover {
            background: #f0fdf4;
        }

        .table-monthly tbody tr:last-child td {
            border-bottom: none;
        }

        /* ===== BADGE MONTH ===== */
        .badge-month {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 0.3rem 1rem;
            border-radius: 30px;
            font-weight: 600;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.85rem;
        }

        .badge-month i {
            font-size: 1rem;
        }

        /* ===== AMOUNT TEXT ===== */
        .amount-text {
            font-family: 'Courier New', monospace;
            font-weight: 700;
        }

        .amount-text.text-success { color: #059669; }
        .amount-text.text-info { color: #0ea5e9; }
        .amount-text.text-warning { color: #d97706; }
        .amount-text.text-primary { color: #065f46; }

        /* ===== TOTAL ROW ===== */
        .total-row {
            background: #ecfdf5 !important;
            font-weight: 700;
        }

        .total-row td {
            border-top: 2px solid #10b981;
        }

        /* ===== TREND ===== */
        .trend-up {
            color: #059669;
        }
        .trend-down {
            color: #ef4444;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 3rem;
            background: #f8fafc;
            border-radius: 1.5rem;
            border: 1px solid #d1fae5;
        }
        .empty-state i {
            font-size: 4rem;
            color: #a7f3d0;
            margin-bottom: 1rem;
        }
        .empty-state h5 {
            color: #065f46;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .filter-card .card-header h4 {
                font-size: 1rem;
            }
            .filter-card .card-body {
                padding: 1rem;
            }
            .summary-stats h3 {
                font-size: 1.2rem;
            }
            .table-monthly thead th {
                font-size: 0.7rem;
                padding: 0.5rem;
            }
            .table-monthly tbody td {
                font-size: 0.75rem;
                padding: 0.5rem;
            }
            .amount-text {
                font-size: 0.75rem;
            }
            .badge-month {
                font-size: 0.7rem;
                padding: 0.2rem 0.7rem;
            }
            .quick-filter .btn-filter {
                font-size: 0.65rem;
                padding: 0.15rem 0.6rem;
            }
        }
    </style>
@endpush

@section('content')
    <!-- ===== FILTER CARD ===== -->
    <div class="card filter-card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4><i class="fas fa-chart-line me-2"></i> Monthly Sales Report</h4>
                <p class="small text-muted mb-0" style="color: #047857 !important;">
                    <i class="fas fa-filter me-1"></i> Filter by company and date range
                </p>
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

            <form method="GET" action="{{ route('pos.report.monthly') }}" id="filterForm">
                <div class="row align-items-end g-3">
                    <div class="col-md-3">
                        <label class="form-label" for="company_id"><i class="fas fa-building me-1"></i> Company</label>
                        <select name="company_id" id="company_id" class="form-select">
                            <option value="">All Companies</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}"
                                    {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" for="start_date"><i class="fas fa-calendar-alt me-1"></i> Start Month</label>
                        <input type="month" name="start_date" id="start_date" class="form-control"
                            value="{{ request('start_date', $startDate ?? date('Y-m')) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" for="end_date"><i class="fas fa-calendar-check me-1"></i> End Month</label>
                        <input type="month" name="end_date" id="end_date" class="form-control"
                            value="{{ request('end_date', $endDate ?? date('Y-m')) }}">
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i> Generate
                            </button>
                            <a href="{{ route('pos.report.monthly') }}" class="btn btn-secondary">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Filter -->
                <div class="quick-filter mt-3">
                    <small class="text-muted me-2">Quick Navigation:</small>
                    @php
                        $currentYear = date('Y');
                        $currentMonth = date('m');
                    @endphp
                    @for ($i = $currentYear; $i >= $currentYear - 2; $i--)
                        <a href="?{{ http_build_query(array_merge(request()->all(), ['start_date' => $i . '-01', 'end_date' => $i . '-12'])) }}"
                            class="btn btn-filter btn-outline-primary">
                            {{ $i }}
                        </a>
                    @endfor
                    <a href="?{{ http_build_query(array_merge(request()->all(), ['start_date' => date('Y-m'), 'end_date' => date('Y-m')])) }}"
                        class="btn btn-filter btn-outline-secondary">
                        Current Month
                    </a>
                    <a href="?{{ http_build_query(array_merge(request()->all(), ['start_date' => date('Y-m', strtotime('-1 month')), 'end_date' => date('Y-m', strtotime('-1 month'))])) }}"
                        class="btn btn-filter btn-outline-secondary">
                        Previous Month
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== REPORT RESULT ===== -->
    @if (isset($monthlyReports) && $monthlyReports->count() > 0)
        <!-- Summary Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="summary-stats">
                    <p><i class="fas fa-calendar-alt"></i> Total Months</p>
                    <h3>{{ $monthlyReports->count() }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-stats">
                    <p><i class="fas fa-chart-bar"></i> Total Transactions</p>
                    <h3>{{ number_format($monthlyReports->sum('total_transactions')) }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-stats">
                    <p><i class="fas fa-coins"></i> Average per Month</p>
                    <h3>Rp {{ number_format($monthlyReports->avg('total_amount'), 0, ',', '.') }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-stats">
                    <p><i class="fas fa-chart-line"></i> Grand Total</p>
                    <h3>Rp {{ number_format($monthlyReports->sum('total_amount'), 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <!-- Monthly Report Table -->
        <div class="card report-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-chart-simple me-2"></i> Monthly Sales Report
                    @if (request('company_id'))
                        <small class="ms-2">
                            - {{ $companies->where('id', request('company_id'))->first()->company_name ?? 'Selected Company' }}
                        </small>
                    @endif
                </h5>
                <span class="badge bg-light text-dark">{{ $monthlyReports->count() }} months</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-monthly align-middle">
                        <thead>
                            <tr>
                                <th class="text-center" width="50">#</th>
                                <th>Month Period</th>
                                <th class="text-end">Accessories</th>
                                <th class="text-end">Service</th>
                                <th class="text-end">Pulsa</th>
                                <th class="text-end">Total Amount</th>
                                <th class="text-center">Transactions</th>
                                <th class="text-center">Trend</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalAccessories = 0;
                                $totalService = 0;
                                $totalPulsa = 0;
                                $totalAmount = 0;
                                $totalTransactions = 0;
                                $previousAmount = null;
                            @endphp

                            @foreach ($monthlyReports as $index => $report)
                                @php
                                    $totalAccessories += $report->accessories_amount;
                                    $totalService += $report->service_amount;
                                    $totalPulsa += $report->pulsa_amount;
                                    $totalAmount += $report->total_amount;
                                    $totalTransactions += $report->total_transactions;

                                    $trendClass = '';
                                    $trendIcon = '';
                                    $trendText = '';
                                    if ($previousAmount && $previousAmount > 0) {
                                        $trendPercent = (($report->total_amount - $previousAmount) / $previousAmount) * 100;
                                        if ($trendPercent > 0) {
                                            $trendClass = 'trend-up';
                                            $trendIcon = 'fa-arrow-up';
                                            $trendText = '+' . number_format($trendPercent, 1) . '%';
                                        } elseif ($trendPercent < 0) {
                                            $trendClass = 'trend-down';
                                            $trendIcon = 'fa-arrow-down';
                                            $trendText = number_format($trendPercent, 1) . '%';
                                        } else {
                                            $trendIcon = 'fa-minus';
                                            $trendText = '0%';
                                        }
                                    }
                                    $previousAmount = $report->total_amount;
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge-month">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $report->month_display }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $report->month_year }}</small>
                                    </td>
                                    <td class="text-end">
                                        <span class="amount-text text-success">
                                            Rp {{ number_format($report->accessories_amount, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="amount-text text-info">
                                            Rp {{ number_format($report->service_amount, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="amount-text text-warning">
                                            Rp {{ number_format($report->pulsa_amount, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <strong class="amount-text text-primary" style="font-size: 1.05rem;">
                                            Rp {{ number_format($report->total_amount, 0, ',', '.') }}
                                        </strong>
                                        @if ($report->total_amount > 0)
                                            <br>
                                            <small class="text-muted">
                                                Avg: Rp
                                                {{ number_format($report->total_amount / ($report->total_transactions ?: 1), 0, ',', '.') }}
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info rounded-pill">
                                            <i class="fas fa-receipt me-1"></i>
                                            {{ $report->total_transactions }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if ($trendIcon)
                                            <i class="fas {{ $trendIcon }} {{ $trendClass }}"></i>
                                            <small class="{{ $trendClass }} d-block">{{ $trendText }}</small>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('pos.report.monthly.detail', [
                                            'start_date' => \Carbon\Carbon::createFromFormat('Y-m', $report->month_year)->startOfMonth()->toDateString(),
                                            'end_date' => \Carbon\Carbon::createFromFormat('Y-m', $report->month_year)->endOfMonth()->toDateString(),
                                            'company_id' => request('company_id'),
                                        ]) }}"
                                            class="btn btn-sm btn-edit-green">
                                            <i class="fas fa-eye me-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="2" class="text-end"><strong>GRAND TOTAL</strong></td>
                                <td class="text-end">
                                    <strong class="text-success">Rp {{ number_format($totalAccessories, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-end">
                                    <strong class="text-info">Rp {{ number_format($totalService, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-end">
                                    <strong class="text-warning">Rp {{ number_format($totalPulsa, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-end">
                                    <strong class="text-primary">Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-center">
                                    <strong class="bg-white px-2 py-1 rounded">{{ $totalTransactions }}</strong>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Report generated on: {{ now()->format('d/m/Y H:i:s') }}
                        </small>
                    </div>
                    <div class="col-md-6 text-end">
                        <small class="text-muted">
                            <i class="fas fa-chart-line"></i>
                            Showing {{ $monthlyReports->count() }} month(s)
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== CHART ===== -->
        <div class="card report-card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-chart-line me-2"></i> Monthly Trend Chart</h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" style="height: 300px;"></canvas>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-chart-line"></i>
            <h5>No Data Available</h5>
            <p class="text-muted">Please select month range and company to generate monthly report</p>
            <button type="button" class="btn btn-primary" onclick="document.getElementById('filterForm').submit();">
                <i class="fas fa-chart-simple me-2"></i> Generate Report
            </button>
        </div>
    @endif
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Auto submit form when company changes
        document.getElementById('company_id')?.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        // Set default date range if not set
        document.addEventListener('DOMContentLoaded', function() {
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');

            if (!startDate.value) {
                startDate.value = new Date().toISOString().slice(0, 7);
            }
            if (!endDate.value) {
                endDate.value = new Date().toISOString().slice(0, 7);
            }
        });

        // Monthly Chart
        @if (isset($monthlyReports) && $monthlyReports->count() > 0)
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('monthlyChart');
                if (!ctx) return;

                const chartLabels = @json($monthlyReports->pluck('month_display')->values()->all());
                const chartTotals = @json($monthlyReports->map(fn($item) => (float) ($item->total_amount ?? 0))->values()->all());
                const chartTransactions = @json($monthlyReports->map(fn($item) => (int) ($item->total_transactions ?? 0))->values()->all());

                if (!chartLabels.length || !ctx.getContext) return;

                new Chart(ctx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: 'Total Amount (Rp)',
                            data: chartTotals,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#059669',
                            pointBorderColor: '#fff',
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }, {
                            label: 'Transactions Count',
                            data: chartTransactions,
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#d97706',
                            pointBorderColor: '#fff',
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            yAxisID: 'y1'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.dataset.label || '';
                                        const value = context.raw;
                                        if (context.dataset.label.includes('Amount')) {
                                            return label + ': Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                        }
                                        return label + ': ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            },
                            y1: {
                                position: 'right',
                                beginAtZero: true,
                                grid: { drawOnChartArea: false }
                            }
                        }
                    }
                });
            });
        @endif
    </script>
@endpush