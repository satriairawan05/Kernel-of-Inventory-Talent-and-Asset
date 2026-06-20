@extends('admin.layouts.app')

@push('css')
<style>
    .page-shell { padding-top: 1rem; }
    .page-hero { border-radius: 28px; padding: 24px; background: linear-gradient(135deg, #111827 0%, #2563eb 45%, #22d3ee 100%); color:#fff; }
    .soft-card { border-radius: 24px; border: 1px solid rgba(148,163,184,.18); box-shadow: 0 18px 40px rgba(15,23,42,.08); }
    .badge-active { background: #d4edda; color: #155724; }
    .badge-closed { background: #e2e3e5; color: #383d41; }
    .table thead th { background: #eef4ff; }
    .modal-lg-custom { max-width: 90%; }
    .product-list-scroll { max-height: 400px; overflow-y: auto; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 page-shell">
    <section class="page-hero d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1">Stock Opname Period Report</h2>
            <p class="mb-0">List of opname periods and their status.</p>
        </div>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="fas fa-plus me-1"></i> Create Period
        </button>
    </section>

    <div class="card soft-card mt-4">
        <div class="card-body">
            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Period</th>
                            <th>Total Items</th>
                            <th>Matched Items</th>
                            <th>Difference Items</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($periods as $period)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($period->period_start)->format('d M Y') }} - {{ \Carbon\Carbon::parse($period->period_end)->format('d M Y') }}</td>
                                <td>{{ $period->total_products }}</td>
                                <td>{{ $period->matched_products }}</td>
                                <td>{{ $period->difference_products }}</td>
                                <td>
                                    <span class="badge {{ $period->status == 'active' ? 'badge-active' : 'badge-closed' }}">
                                        {{ $period->status == 'active' ? 'Active' : 'Closed' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('inventory.stock-opname.show', $period) }}" class="btn btn-sm btn-primary">Detail</a>
                                    @if($period->status == 'active')
                                        <form action="{{ route('inventory.stock-opname.close', $period) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-warning">Close Period</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">No opname periods found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $periods->links() }}
        </div>
    </div>
</div>

<!-- ==================== MODAL CREATE ==================== -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-lg-custom">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Create Opname Period</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('inventory.stock-opname.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Start Date</label>
                            <input type="date" value="{{ \Carbon\Carbon::now()->startOfMonth()->toDateString() }}" name="period_start" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date</label>
                            <input type="date" value="{{ \Carbon\Carbon::now()->endOfMonth()->toDateString() }}" name="period_end" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Period Notes</label>
                            <input type="text" name="notes" class="form-control" placeholder="Optional notes">
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i> Fill in the physical stock for each product.
                    </div>

                    <div class="product-list-scroll">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th style="width:5%">#</th>
                                    <th style="width:35%">Product / Variant</th>
                                    <th style="width:15%">System Stock</th>
                                    <th style="width:25%">Physical Stock</th>
                                    <th style="width:20%">Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productVariants as $variant)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $variant->product->product_name ?? '-' }} - {{ $variant->variant_name ?? '-' }}</td>
                                        <td>{{ number_format($variant->stock->current_stock ?? 0, 2, ',', '.') }}</td>
                                        <td>
                                            <input type="number" step="0.01" name="details[{{ $variant->id }}]"
                                                class="form-control" placeholder="Physical stock"
                                                value="{{ old('details.' . $variant->id) }}">
                                        </td>
                                        <td>
                                            <input type="text" name="detail_notes[{{ $variant->id }}]"
                                                class="form-control" placeholder="Notes per product">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Period</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection