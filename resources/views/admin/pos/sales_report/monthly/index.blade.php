@extends('admin.layouts.app')

@push('css')
    <style>
        .company-logo {
            max-width: 80px;
            max-height: 80px;
            object-fit: contain;
        }

        .filter-card {
            background: linear-gradient(135deg, #0f172a 0%, #2563eb 45%, #22d3ee 100%);
            border: none;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .filter-card .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 20px 25px;
        }

        .filter-card .card-header h4 {
            color: white;
            margin: 0;
            font-weight: 600;
        }

        .filter-card .card-body {
            padding: 25px;
        }

        .filter-card .form-label {
            color: white;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .filter-card .form-control,
        .filter-card .form-select {
            border-radius: 10px;
            border: none;
            padding: 10px 15px;
            background: rgba(255, 255, 255, 0.95);
        }

        .filter-card .form-control:focus,
        .filter-card .form-select:focus {
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.5);
        }

        .filter-card .btn-primary {
            background: #ff6b6b;
            border: none;
            border-radius: 10px;
            padding: 10px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .filter-card .btn-primary:hover {
            background: #ff5252;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .filter-card .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 10px;
            padding: 10px 30px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .filter-card .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .report-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            border: none;
        }

        .report-card .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
        }

        .report-card .card-header h5 {
            margin: 0;
            font-weight: 600;
        }

        .summary-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border: none;
        }

        .summary-stats {
            background: white;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .summary-stats:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .summary-stats h3 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: bold;
            color: #667eea;
        }

        .summary-stats p {
            margin: 0;
            color: #666;
            font-size: 0.85rem;
        }

        .table-monthly {
            margin-bottom: 0;
        }

        .table-monthly thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
            padding: 15px;
        }

        .table-monthly tbody td {
            padding: 15px;
            vertical-align: middle;
        }

        .table-monthly tbody tr:hover {
            background-color: #f8f9fa;
            transition: all 0.2s ease;
        }

        .badge-month {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 500;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .month-icon {
            font-size: 18px;
        }

        .amount-text {
            font-family: monospace;
            font-size: 14px;
            font-weight: 600;
        }

        .total-row {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-weight: bold;
        }

        .total-row td {
            font-weight: bold;
            padding: 15px;
        }

        .trend-up {
            color: #28a745;
            font-size: 12px;
        }

        .trend-down {
            color: #dc3545;
            font-size: 12px;
        }

        .empty-state {
            text-align: center;
            padding: 50px;
            background: #f8f9fa;
            border-radius: 15px;
        }

        .empty-state i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 15px;
        }

        .quick-filter {
            background: white;
            border-radius: 10px;
            padding: 8px 12px;
            margin-bottom: 15px;
        }

        .quick-filter .btn-filter {
            padding: 4px 12px;
            font-size: 12px;
            border-radius: 20px;
            margin: 0 3px;
        }

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
                font-size: 0.75rem;
                padding: 8px;
            }

            .table-monthly tbody td {
                font-size: 0.7rem;
                padding: 8px;
            }

            .amount-text {
                font-size: 0.7rem;
            }

            .badge-month {
                font-size: 0.65rem;
                padding: 4px 8px;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Filter Section -->
    <div class="card filter-card">
        <div class="card-header">
            <h4><i class="fas fa-chart-line me-2"></i> Monthly Sales Report Filter</h4>
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
                <div class="row align-items-end">
                    <div class="col-md-3 mb-3 mb-md-0">
                        <label class="form-label" for="company_id">
                            <i class="fas fa-building me-1"></i> Company
                        </label>
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

                    <div class="col-md-3 mb-3 mb-md-0">
                        <label class="form-label" for="start_date">
                            <i class="fas fa-calendar-alt me-1"></i> Start Date (Month)
                        </label>
                        <input type="month" name="start_date" id="start_date" class="form-control"
                            value="{{ request('start_date', $startDate ?? date('Y-m')) }}">
                    </div>

                    <div class="col-md-3 mb-3 mb-md-0">
                        <label class="form-label" for="end_date">
                            <i class="fas fa-calendar-check me-1"></i> End Date (Month)
                        </label>
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
            </form>

            <!-- Quick Filter for Current/Previous Year -->
            <div class="quick-filter mt-3">
                <small class="text-muted me-2">Quick Navigation:</small>
                @php
                    $currentYear = date('Y');
                    $currentMonth = date('m');
                @endphp
                @for ($i = $currentYear; $i >= $currentYear - 2; $i--)
                    <a href="?{{ http_build_query(array_merge(request()->all(), ['start_date' => $i . '-01', 'end_date' => $i . '-12'])) }}"
                        class="btn btn-sm btn-outline-primary btn-filter">
                        {{ $i }}
                    </a>
                @endfor
                <a href="?{{ http_build_query(array_merge(request()->all(), ['start_date' => date('Y-m'), 'end_date' => date('Y-m')])) }}"
                    class="btn btn-sm btn-outline-secondary btn-filter">
                    Current Month
                </a>
                <a href="?{{ http_build_query(array_merge(request()->all(), ['start_date' => date('Y-m', strtotime('-1 month')), 'end_date' => date('Y-m', strtotime('-1 month'))])) }}"
                    class="btn btn-sm btn-outline-secondary btn-filter">
                    Previous Month
                </a>
            </div>
        </div>
    </div>

    <!-- Report Result Section -->
    @if (isset($monthlyReports) && $monthlyReports->count() > 0)
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="summary-stats">
                    <p><i class="fas fa-calendar-alt"></i> Total Months</p>
                    <h3>{{ $monthlyReports->count() }}</h3>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="summary-stats">
                    <p><i class="fas fa-chart-bar"></i> Total Transactions</p>
                    <h3>{{ number_format($monthlyReports->sum('total_transactions')) }}</h3>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="summary-stats">
                    <p><i class="fas fa-coins"></i> Average per Month</p>
                    <h3>Rp {{ number_format($monthlyReports->avg('total_amount'), 0, ',', '.') }}</h3>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="summary-stats">
                    <p><i class="fas fa-chart-line"></i> Grand Total</p>
                    <h3>Rp {{ number_format($monthlyReports->sum('total_amount'), 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <!-- Monthly Report Table -->
        <div class="card report-card">
            <div class="card-header">
                <h5>
                    <i class="fas fa-chart-simple me-2"></i>
                    Monthly Sales Report
                    @if (request('company_id'))
                        <small class="ms-2">
                            -
                            {{ $companies->where('id', request('company_id'))->first()->company_name ?? 'Selected Company' }}
                        </small>
                    @endif
                </h5>
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

                                    // Calculate trend percentage
                                    $trendClass = '';
                                    $trendIcon = '';
                                    $trendText = '';
                                    if ($previousAmount && $previousAmount > 0) {
                                        $trendPercent =
                                            (($report->total_amount - $previousAmount) / $previousAmount) * 100;
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
                                            <i class="fas fa-calendar-alt month-icon"></i>
                                            {{ $report->month_display }}
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            {{ $report->month_year }}
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <span class="amount-text text-success">
                                            {{ $report->formatted_accessories ?? 'Rp ' . number_format($report->accessories_amount, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="amount-text text-info">
                                            {{ $report->formatted_service ?? 'Rp ' . number_format($report->service_amount, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="amount-text text-warning">
                                            {{ $report->formatted_pulsa ?? 'Rp ' . number_format($report->pulsa_amount, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <strong class="amount-text text-primary" style="font-size: 1rem;">
                                            {{ $report->formatted_total ?? 'Rp ' . number_format($report->total_amount, 0, ',', '.') }}
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
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="2" class="text-end">
                                    <strong>GRAND TOTAL</strong>
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">{{ \Carbon\Carbon::rupiah($totalAccessories) }}</strong>
                                </td>
                                <td class="text-end">
                                    <strong class="text-info">{{ \Carbon\Carbon::rupiah($totalService) }}</strong>
                                </td>
                                <td class="text-end">
                                    <strong class="text-warning">{{ \Carbon\Carbon::rupiah($totalPulsa) }}</strong>
                                </td>
                                <td class="text-end">
                                    <strong class="text-primary">{{ \Carbon\Carbon::rupiah($totalAmount) }}</strong>
                                </td>
                                <td class="text-center">
                                    <strong class="bg-white px-2 py-1 rounded">
                                        {{ $totalTransactions }}
                                    </strong>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light">
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

        <!-- Additional Chart Section (Optional) -->
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
        // Auto submit form when company changes (optional)
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

                if (!ctx) {
                    return;
                }

                const chartLabels = @json($monthlyReports->pluck('month_display')->values()->all());
                const chartTotals = @json($monthlyReports->map(fn($item) => (float) ($item->total_amount ?? 0))->values()->all());
                const chartTransactions = @json($monthlyReports->map(fn($item) => (int) ($item->total_transactions ?? 0))->values()->all());

                if (!chartLabels.length || !ctx.getContext) {
                    return;
                }

                const chartContext = ctx.getContext('2d');

                if (!window.Chart) {
                    console.error('Chart.js is not loaded.');
                    return;
                }

                new Chart(chartContext, {
                    type: 'line',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                                label: 'Total Amount (Rp)',
                                data: chartTotals,
                                borderColor: '#667eea',
                                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: '#764ba2',
                                pointBorderColor: '#fff',
                                pointRadius: 5,
                                pointHoverRadius: 7
                            },
                            {
                                label: 'Transactions Count',
                                data: chartTransactions,
                                borderColor: '#ff6b6b',
                                backgroundColor: 'rgba(255, 107, 107, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: '#ff5252',
                                pointBorderColor: '#fff',
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                yAxisID: 'y1'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.dataset.label || '';
                                        const value = context.raw;

                                        if (context.dataset.label && context.dataset.label.includes(
                                                'Amount')) {
                                            return label + ': Rp ' + new Intl.NumberFormat('id-ID')
                                                .format(value);
                                        }

                                        return label + ': ' + new Intl.NumberFormat('id-ID').format(
                                            value);
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
                                grid: {
                                    drawOnChartArea: false
                                }
                            }
                        }
                    }
                });
            });
        @endif
    </script>
@endpush
