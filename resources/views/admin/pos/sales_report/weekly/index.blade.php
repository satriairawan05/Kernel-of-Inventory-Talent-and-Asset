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
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .filter-card .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(255,255,255,0.2);
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
            background: rgba(255,255,255,0.95);
        }

        .filter-card .form-control:focus,
        .filter-card .form-select:focus {
            box-shadow: 0 0 0 3px rgba(255,255,255,0.5);
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
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .filter-card .btn-secondary {
            background: rgba(255,255,255,0.2);
            border: none;
            border-radius: 10px;
            padding: 10px 30px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .filter-card .btn-secondary:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }

        .report-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
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
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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

        .table-weekly {
            margin-bottom: 0;
        }

        .table-weekly thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
            padding: 15px;
        }

        .table-weekly tbody td {
            padding: 15px;
            vertical-align: middle;
        }

        .table-weekly tbody tr:hover {
            background-color: #f8f9fa;
            transition: all 0.2s ease;
        }

        .badge-week {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 500;
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
                font-size: 0.75rem;
                padding: 8px;
            }

            .table-weekly tbody td {
                font-size: 0.7rem;
                padding: 8px;
            }

            .amount-text {
                font-size: 0.7rem;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Filter Section -->
    <div class="card filter-card">
        <div class="card-header">
            <h4><i class="fas fa-chart-line me-2"></i>Weekly Sales Report Filter</h4>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('pos.report.weekly') }}" id="filterForm">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-3 mb-md-0">
                        <label class="form-label" for="company_id">
                            <i class="fas fa-building me-1"></i> Company
                        </label>
                        <select name="company_id" id="company_id" class="form-select">
                            <option value="">All Companies</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3 mb-md-0">
                        <label class="form-label" for="start_date">
                            <i class="fas fa-calendar-alt me-1"></i> Start Date
                        </label>
                        <input type="date" name="start_date" id="start_date" 
                            class="form-control" 
                            value="{{ request('start_date', $startDate ?? date('Y-m-01')) }}">
                    </div>
                    
                    <div class="col-md-3 mb-3 mb-md-0">
                        <label class="form-label" for="end_date">
                            <i class="fas fa-calendar-check me-1"></i> End Date
                        </label>
                        <input type="date" name="end_date" id="end_date" 
                            class="form-control" 
                            value="{{ request('end_date', $endDate ?? date('Y-m-d')) }}">
                    </div>
                    
                    <div class="col-md-3">
                        <div class="d-flex gap-1">
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

    <!-- Report Result Section -->
    @if(isset($weeklyReports) && $weeklyReports->count() > 0)
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="summary-stats">
                    <p><i class="fas fa-calendar-week"></i> Total Weeks</p>
                    <h3>{{ $weeklyReports->count() }}</h3>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="summary-stats">
                    <p><i class="fas fa-chart-bar"></i> Total Transactions</p>
                    <h3>{{ $weeklyReports->sum('total_transactions') }}</h3>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="summary-stats">
                    <p><i class="fas fa-coins"></i> Average per Week</p>
                    <h3>{{ Carbon\Carbon::rupiah($weeklyReports->avg('total_amount'), 0, ',', '.') }}</h3>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="summary-stats">
                    <p><i class="fas fa-chart-line"></i> Grand Total</p>
                    <h3>{{ Carbon\Carbon::rupiah($weeklyReports->sum('total_amount'), 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <!-- Weekly Report Table -->
        <div class="card report-card">
            <div class="card-header">
                <h5>
                    <i class="fas fa-chart-simple me-2"></i>
                    Weekly Sales Report
                    @if(request('company_id'))
                        <small class="ms-2">
                            - {{ $companies->where('id', request('company_id'))->first()->company_name ?? 'Selected Company' }}
                        </small>
                    @endif
                </h5>
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
                            
                            @foreach($weeklyReports as $index => $report)
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
                                        <i class="fas fa-calendar-week me-1"></i>
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
                                    <strong class="amount-text text-primary" style="font-size: 1rem;">
                                        {{ $report->formatted_total ?? Carbon\Carbon::rupiah($report->total_amount, 0, ',', '.') }}
                                    </strong>
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
                                    ]) }}" class="btn btn-sm btn-outline-primary">
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
    // Auto submit form when company changes (optional)
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