@extends('admin.layouts.app')

@push('css')
    <style>
        .page-shell { padding-top: 1rem; }
        .page-hero {
            border-radius: 2rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, #ccf0f2 0%, #a7f3d0 100%) !important;
            color: #065f46;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
            border: 1px solid rgba(16,185,129,0.2);
        }
        .page-hero .eyebrow {
            text-transform: uppercase;
            letter-spacing: .28em;
            font-size: .72rem;
            color: #047857;
        }
        .page-hero h2 { color: #065f46; font-weight: 700; }
        .page-hero p { color: #047857; }
        .page-hero .btn-outline-secondary {
            border-color: #065f46 !important;
            color: #065f46 !important;
            border-radius: 30px !important;
        }
        .page-hero .btn-outline-secondary:hover {
            background: #065f46 !important;
            color: #fff !important;
        }

        .soft-card {
            border-radius: 1.5rem;
            border: 1px solid #d1fae5;
            box-shadow: 0 8px 24px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .soft-card .card-header {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-bottom: 1px solid #d1fae5;
            padding: 1rem 1.5rem;
        }
        .soft-card .card-header h4 { color: #065f46; font-weight: 700; }

        .summary-stats {
            background: #ffffff;
            border-radius: 1.5rem;
            padding: 1.25rem 1rem;
            text-align: center;
            border: 1px solid #d1fae5;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            transition: 0.2s;
            height: 100%;
        }
        .summary-stats p {
            font-size: 0.85rem;
            color: #6b7280;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        .summary-stats h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        .text-success { color: #059669 !important; }
        .text-danger { color: #dc2626 !important; }
        .text-primary { color: #065f46 !important; }

        .table thead th {
            background: #ecfdf5;
            color: #065f46;
            font-weight: 600;
            border-bottom: 2px solid #d1fae5;
            padding: 0.9rem 1rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .table tbody tr:hover { background: #f0fdf4; }
        .table tbody td { padding: 0.9rem 1rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }

        .badge-success { background: #059669 !important; color: #fff !important; }
        .badge-danger { background: #dc2626 !important; color: #fff !important; }

        .stat-chip {
            border-radius: 999px;
            padding: 0.4rem 1rem;
            font-weight: 600;
            font-size: .82rem;
            background: #ecfdf5 !important;
            color: #065f46 !important;
            border: 1px solid #d1fae5;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4 page-shell">
        <!-- Hero -->
        <section class="page-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <p class="eyebrow mb-2"><i class="fas fa-calendar-day me-1"></i> Cash Detail</p>
                <h2 class="mb-1">Detail Cash Summary</h2>
                <p class="mb-0">Detail cash in/out records for <strong>{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</strong>.</p>
            </div>
            <a href="{{ route('pos.cash_summary.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Summary
            </a>
        </section>

        <!-- Summary Stats for this date -->
        <div class="row g-3 mb-4 mt-2">
            <div class="col-md-3 col-6">
                <div class="summary-stats">
                    <p><i class="fas fa-arrow-down text-success"></i> Cash In</p>
                    <h3 class="text-success">{{ $summary['formatted_in'] ?? 'Rp 0' }}</h3>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="summary-stats">
                    <p><i class="fas fa-arrow-up text-danger"></i> Cash Out</p>
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
                    <h3>{{ $records->count() }}</h3>
                </div>
            </div>
        </div>

        <!-- Table Records -->
        <div class="card soft-card mt-0">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <h4 class="mb-1"><i class="fas fa-list me-2"></i> All Records</h4>
                    <p class="text-muted mb-0">List of cash in/out transactions on this date.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="stat-chip"><i class="fas fa-cash-register me-1"></i>
                        {{ $records->count() }} record{{ $records->count() > 1 ? 's' : '' }}
                    </span>
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

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Type</th>
                                <th scope="col" class="text-end">Amount</th>
                                <th scope="col">Description</th>
                                <th scope="col">Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $index => $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @php
                                            $isCashIn = $item->type === \App\Enums\CashSummaryTypeEnum::CASH_IN->value;
                                            $badgeClass = $isCashIn ? 'badge-success' : 'badge-danger';
                                            $iconClass = $isCashIn ? 'fa-arrow-down' : 'fa-arrow-up';
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            <i class="fas {{ $iconClass }}"></i>
                                            {{ $item->type_label }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold {{ $isCashIn ? 'text-success' : 'text-danger' }}">
                                        {{ $item->formatted_amount }}
                                    </td>
                                    <td>{{ $item->description ?? '-' }}</td>
                                    <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No records found for this date.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection