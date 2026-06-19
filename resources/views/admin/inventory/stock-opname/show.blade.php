@extends('admin.layouts.app')

@push('css')
<style>
    .page-shell { padding-top: 1rem; }
    .page-hero { border-radius: 28px; padding: 24px; background: linear-gradient(135deg, #111827 0%, #2563eb 45%, #22d3ee 100%); color:#fff; }
    .soft-card { border-radius: 24px; border: 1px solid rgba(148,163,184,.18); box-shadow: 0 18px 40px rgba(15,23,42,.08); }
    .badge-status { padding: 4px 12px; border-radius: 20px; }
    .badge-active { background: #d4edda; color: #155724; }
    .badge-closed { background: #e2e3e5; color: #383d41; }
    .table thead th { background: #eef4ff; }
    .diff-positive { color: #28a745; font-weight: bold; }
    .diff-negative { color: #dc3545; font-weight: bold; }
    .diff-zero { color: #6c757d; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 page-shell">
    <section class="page-hero d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1">Detail Periode Opname</h2>
            <p class="mb-0">
                {{ \Carbon\Carbon::parse($period->period_start)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($period->period_end)->format('d M Y') }}
                <span class="badge {{ $period->status == 'active' ? 'badge-active' : 'badge-closed' }} ms-2">
                    {{ $period->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                </span>
            </p>
        </div>
        <a href="{{ route('inventory.stock-opname.index') }}" class="btn btn-outline-light"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
    </section>

    <div class="card soft-card mt-4">
        <div class="card-body">
            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Produk / Varian</th>
                            <th>Stok Sistem</th>
                            <th>Stok Fisik</th>
                            <th>Selisih</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($period->details as $detail)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $detail->productVariant->product->product_name ?? '-' }} - {{ $detail->productVariant->variant_name ?? '-' }}</td>
                                <td>{{ number_format($detail->system_stock, 2, ',', '.') }}</td>
                                <td>
                                    @if($detail->physical_stock !== null)
                                        {{ number_format($detail->physical_stock, 2, ',', '.') }}
                                    @else
                                        <span class="text-muted">Belum dilaporkan</span>
                                    @endif
                                </td>
                                <td>
                                    @if($detail->difference !== null)
                                        @php
                                            $diff = $detail->difference;
                                            $class = $diff > 0 ? 'diff-positive' : ($diff < 0 ? 'diff-negative' : 'diff-zero');
                                        @endphp
                                        <span class="{{ $class }}">{{ ($diff > 0 ? '+' : '') . number_format($diff, 2, ',', '.') }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($detail->physical_stock !== null)
                                        <span class="badge bg-success">Sudah</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Belum</span>
                                    @endif
                                </td>
                                <td>{{ $detail->notes ?? '-' }}</td>
                                <td>
                                    @if($period->status == 'active')
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateModal{{ $detail->id }}">
                                            <i class="fas fa-edit"></i> Laporkan
                                        </button>
                                    @else
                                        <span class="text-muted">Terkunci</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ========== MODAL UPDATE PER DETAIL ========== -->
@foreach($period->details as $detail)
<div class="modal fade" id="updateModal{{ $detail->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Laporkan Stok Fisik</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('inventory.stock-opname.update-detail', $detail) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p><strong>Produk:</strong> {{ $detail->productVariant->product->product_name ?? '-' }} - {{ $detail->productVariant->variant_name ?? '-' }}</p>
                    <p><strong>Stok Sistem:</strong> {{ number_format($detail->system_stock, 2, ',', '.') }}</p>
                    <div class="mb-3">
                        <label class="form-label">Stok Fisik</label>
                        <input type="number" step="0.01" name="physical_stock" class="form-control" 
                               value="{{ old('physical_stock', $detail->physical_stock ?? '') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <input type="text" name="notes" class="form-control" value="{{ old('notes', $detail->notes) }}" placeholder="Catatan (opsional)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection