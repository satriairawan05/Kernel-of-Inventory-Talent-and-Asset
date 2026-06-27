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
        .table-weekly thead th {
            background: #ecfdf5;
            color: #065f46;
            font-weight: 600;
            border-bottom: 2px solid #d1fae5;
            padding: 0.9rem 1rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .table-weekly tbody td {
            padding: 0.9rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-weekly tbody tr:hover {
            background: #f0fdf4;
        }

        .table-weekly tbody tr:last-child td {
            border-bottom: none;
        }

        /* ===== BADGE WEEK ===== */
        .badge-week {
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

        .badge-week i {
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

            .table-weekly thead th {
                font-size: 0.7rem;
                padding: 0.5rem;
            }

            .table-weekly tbody td {
                font-size: 0.75rem;
                padding: 0.5rem;
            }

            .amount-text {
                font-size: 0.75rem;
            }

            .badge-week {
                font-size: 0.7rem;
                padding: 0.2rem 0.7rem;
            }
        }
    </style>
@endpush

@section('content')
    <!-- ===== FILTER CARD ===== -->
    <div class="card filter-card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4><i class="fas fa-chart-line me-2"></i> Weekly Sales Report</h4>
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

            <form method="GET" action="{{ route('pos.report.weekly') }}" id="filterForm">
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
                        <label class="form-label" for="start_date"><i class="fas fa-calendar-alt me-1"></i> Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                            value="{{ request('start_date', $startDate ?? date('Y-m-01')) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" for="end_date"><i class="fas fa-calendar-check me-1"></i> End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control"
                            value="{{ request('end_date', $endDate ?? date('Y-m-d')) }}">
                    </div>

                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i> Generate
                            </button>
                            <a href="{{ route('pos.report.weekly') }}" class="btn btn-secondary">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== REPORT RESULT ===== -->
    @if (isset($weeklyReports) && $weeklyReports->count() > 0)
        <!-- Summary Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="summary-stats">
                    <p><i class="fas fa-calendar-week"></i> Total Weeks</p>
                    <h3>{{ $weeklyReports->count() }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-stats">
                    <p><i class="fas fa-chart-bar"></i> Total Transactions</p>
                    <h3>{{ $weeklyReports->sum('total_transactions') }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-stats">
                    <p><i class="fas fa-coins"></i> Average per Week</p>
                    <h3>{{ Carbon\Carbon::rupiah($weeklyReports->avg('total_amount'), 0, ',', '.') }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-stats">
                    <p><i class="fas fa-chart-line"></i> Grand Total</p>
                    <h3>{{ Carbon\Carbon::rupiah($weeklyReports->sum('total_amount'), 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <!-- Weekly Report Table -->
        <div class="card report-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-chart-simple me-2"></i> Weekly Sales Report
                    @if (request('company_id'))
                        <small class="ms-2">
                            - {{ $companies->where('id', request('company_id'))->first()->company_name ?? 'Selected Company' }}
                        </small>
                    @endif
                </h5>
                <span class="badge bg-light text-dark">{{ $weeklyReports->count() }} weeks</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-weekly align-middle">
                        <thead>
                            <tr>
                                <th class="text-center" width="50">#</th>
                                <th>Week Period</th>
                                <th class="text-end">Accessories</th>
                                <th class="text-end">Service</th>
                                <th class="text-end">Pulsa</th>
                                <th class="text-end">Total Amount</th>
                                <th class="text-center">Transactions</th>
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
                            @endphp

                            @foreach ($weeklyReports as $index => $report)
                                @php
                                    $totalAccessories += $report->accessories_amount;
                                    $totalService += $report->service_amount;
                                    $totalPulsa += $report->pulsa_amount;
                                    $totalAmount += $report->total_amount;
                                    $totalTransactions += $report->total_transactions;
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge-week">
                                            <i class="fas fa-calendar-week"></i>
                                            {{ $report->week_display }}
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            Year {{ $report->year }} - Week {{ $report->week_number }}
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <span class="amount-text text-success">
                                            {{ $report->formatted_accessories ?? Carbon\Carbon::rupiah($report->accessories_amount, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="amount-text text-info">
                                            {{ $report->formatted_service ?? Carbon\Carbon::rupiah($report->service_amount, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="amount-text text-warning">
                                            {{ $report->formatted_pulsa ?? Carbon\Carbon::rupiah($report->pulsa_amount, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <strong class="amount-text text-primary" style="font-size: 1.05rem;">
                                            {{ $report->formatted_total ?? Carbon\Carbon::rupiah($report->total_amount, 0, ',', '.') }}
                                        </strong>
                                        @if ($report->total_amount > 0 && $report->total_transactions > 0)
                                            <br>
                                            <small class="text-muted">
                                                Avg: Rp
                                                {{ number_format($report->total_amount / $report->total_transactions, 0, ',', '.') }}
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
                                        <a href="{{ route('pos.report.weekly.detail', [
                                            'start_date' => $report->week_start,
                                            'end_date' => $report->week_end,
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
                                    <strong class="text-success">{{ Carbon\Carbon::rupiah($totalAccessories, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-end">
                                    <strong class="text-info">{{ Carbon\Carbon::rupiah($totalService, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-end">
                                    <strong class="text-warning">{{ Carbon\Carbon::rupiah($totalPulsa, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-end">
                                    <strong class="text-primary">{{ Carbon\Carbon::rupiah($totalAmount, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-center">
                                    <strong class="bg-white px-2 py-1 rounded">{{ $totalTransactions }}</strong>
                                </td>
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
                            Showing {{ $weeklyReports->count() }} week(s)
                        </small>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-chart-line"></i>
            <h5>No Data Available</h5>
            <p class="text-muted">Please select date range and company to generate weekly report</p>
            <button type="button" class="btn btn-primary" onclick="document.getElementById('filterForm').submit();">
                <i class="fas fa-chart-simple me-2"></i> Generate Report
            </button>
        </div>
    @endif
@endsection

@push('js')
    <script>
        // Auto submit form when company changes
        document.getElementById('company_id')?.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        // Set default date range for current month if not set
        document.addEventListener('DOMContentLoaded', function() {
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');

            if (!startDate.value) {
                const firstDay = new Date();
                firstDay.setDate(1);
                startDate.value = firstDay.toISOString().split('T')[0];
            }

            if (!endDate.value) {
                endDate.value = new Date().toISOString().split('T')[0];
            }
        });
    </script>
@endpush