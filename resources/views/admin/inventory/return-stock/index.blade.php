@extends('admin.layouts.app')

@push('css')
<style>
    .page-shell { padding-top: 1rem; }
    .page-hero { border-radius: 28px; padding: 24px; background: linear-gradient(135deg, #111827 0%, #2563eb 45%, #22d3ee 100%); color:#fff; box-shadow: 0 18px 40px rgba(15,23,42,.18); }
    .page-hero .eyebrow { text-transform: uppercase; letter-spacing: .28em; font-size: .72rem; color: rgba(255,255,255,.82); }
    .soft-card { border-radius: 24px; border: 1px solid rgba(148,163,184,.18); box-shadow: 0 18px 40px rgba(15,23,42,.08); overflow: hidden; }
    .soft-card .card-header { border-bottom: 1px solid rgba(148,163,184,.18); background: linear-gradient(180deg, #fff 0%, #f8fbff 100%); }
    .stat-chip { border-radius: 999px; padding: 6px 10px; font-weight: 600; font-size: .82rem; }
    .table thead th { background: #eef4ff; color: #334155; font-weight: 700; }
    .table tbody tr:hover { background: #f8fbff; }
    .action-buttons { display:flex; gap:.5rem; flex-wrap:wrap; }
    @media (max-width: 768px) {
        .page-hero { padding: 18px; }
        .page-hero .btn { width: 100%; }
        .action-buttons { flex-direction:column; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 page-shell">
    <section class="page-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <p class="eyebrow mb-2">Inventory</p>
            <h2 class="mb-1">Return Barang</h2>
            <p class="mb-0">Riwayat retur / pengembalian barang.</p>
        </div>
        <a href="{{ route('inventory.return-stock.create') }}" class="btn btn-success"><i class="fas fa-plus me-1"></i> Tambah Retur</a>
    </section>

    <div class="card soft-card mt-4">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h4 class="mb-1">List Return</h4>
                <p class="text-muted mb-0">Semua transaksi retur barang.</p>
            </div>
            <span class="stat-chip bg-primary-subtle text-primary"><i class="fas fa-boxes me-1"></i> {{ $movements->total() }} records</span>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('failed'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('failed') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Produk / Varian</th>
                            <th>Qty Return</th>
                            <th>Stok Sebelum</th>
                            <th>Stok Sesudah</th>
                            <th>PIC</th>
                            <th>Catatan</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $item->productVariant->product->product_name ?? '-' }}</strong>
                                <div class="small text-muted">{{ $item->productVariant->variant_name ?? '-' }}</div>
                            </td>
                            <td class="text-success fw-bold">+{{ number_format($item->qty, 2, ',', '.') }}</td>
                            <td>{{ number_format($item->stock_before, 2, ',', '.') }}</td>
                            <td>{{ number_format($item->stock_after, 2, ',', '.') }}</td>
                            <td>{{ $item->user->name ?? '-' }}</td>
                            <td>{{ Str::limit($item->notes, 30) ?? '-' }}</td>
                            <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('inventory.return-stock.edit', $item) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id }}"><i class="fas fa-trash"></i></button>
                                </div>
                                <!-- Modal Delete -->
                                <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Hapus Retur</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center py-4">
                                                <p>Yakin ingin menghapus transaksi retur ini?</p>
                                                <p class="text-muted small">Stok akan dikembalikan ke sebelumnya.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <form action="{{ route('inventory.return-stock.destroy', $item) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center py-4 text-muted">Belum ada data retur.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-center">
                {{ $movements->links() }}
            </div>
        </div>
    </div>
</div>
@endsection