@extends('admin.layouts.app')

@push('css')
<style>
    .page-shell { padding-top: 1rem; }
    .page-hero {
        border-radius: 28px;
        padding: 24px;
        background: linear-gradient(135deg, #0f172a 0%, #2563eb 45%, #22d3ee 100%);
        color: #fff;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
    }
    .page-hero .eyebrow { text-transform: uppercase; letter-spacing: .28em; font-size: .72rem; color: rgba(255,255,255,.85); }
    .soft-panel { border-radius: 24px; border: 1px solid rgba(148,163,184,.18); box-shadow: 0 18px 40px rgba(15,23,42,.08); overflow: hidden; }
    .soft-panel .card-header { border-bottom: 1px solid rgba(148,163,184,.18); background: linear-gradient(180deg, #fff 0%, #f8fbff 100%); }
    .soft-panel .table thead th { background: #eef4ff; color: #334155; font-weight: 700; }
    .soft-panel .table tbody tr:hover { background: #f8fbff; }
    .pill-chip { display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 6px 10px; font-size: .82rem; font-weight: 600; }
    .badge-movement { border-radius: 20px; padding: 4px 10px; font-size: 0.75rem; font-weight: 500; }
    .table td { vertical-align: middle; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 page-shell">
    <section class="page-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <h2 class="mb-1">Stock Movement Log</h2>
            <p class="mb-0">Complete history of stock changes</p>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <a href="{{ route('inventory.home') }}" class="btn btn-outline-light"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
        </div>
    </section>

    @if ($access['Read'] == 1)
    <div class="card soft-panel shadow-sm border-0 rounded-4 overflow-hidden mt-4">
        <div class="card-header bg-white border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h4 class="mb-1">Stock Movements</h4>
                <p class="text-muted mb-0">All transactions inbound, outbound, adjustments, stocktakes, etc.</p>
            </div>
            <span class="pill-chip bg-primary-subtle text-primary"><i class="fas fa-history"></i> {{ $movements->count() }} records</span>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Produk / Varian</th>
                            <th>PIC</th>
                            <th>Type</th>
                            <th>Qty</th>
                            <th>Stok Before</th>
                            <th>Stok After</th>
                            <th>Notes</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($movements as $movement)
                        <tr>
                            <td>{{ $movements->firstItem() + $loop->index }}</td>
                            <td>
                                <div><strong>{{ $movement->productVariant->product->product_name ?? '-' }}</strong></div>
                                <div class="small text-muted">{{ $movement->productVariant->variant_name ?? 'Tanpa varian' }}</div>
                            </td>
                            <td>{{ $movement->user->name ?? '-' }}</td>
                            <td>
                                @php
                                    $type = $movement->movement_type;
                                    $badgeClass = match($type) {
                                        'opening' => 'bg-secondary',
                                        'purchase' => 'bg-success',
                                        'sale' => 'bg-danger',
                                        'adjustment' => 'bg-warning text-dark',
                                        'opname' => 'bg-info',
                                        default => 'bg-light text-dark'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} badge-movement">
                                    {{ ucfirst($type) }}
                                </span>
                            </td>
                            <td>
                                <span class="{{ $movement->qty > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $movement->qty > 0 ? '+' : '' }}{{ number_format($movement->qty, 2, ',', '.') }}
                                </span>
                            </td>
                            <td>{{ number_format($movement->stock_before, 2, ',', '.') }}</td>
                            <td>{{ number_format($movement->stock_after, 2, ',', '.') }}</td>
                            <td>{{ Str::limit($movement->notes, 50) ?? '-' }}</td>
                            <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">There is no stock movement data yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 d-flex justify-content-center">
                {{ $movements->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection