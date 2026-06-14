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
    .page-hero p { color: rgba(255,255,255,.88); }
    .soft-panel { border-radius: 24px; border: 1px solid rgba(148,163,184,.18); box-shadow: 0 18px 40px rgba(15,23,42,.08); overflow: hidden; }
    .soft-panel .card-header { border-bottom: 1px solid rgba(148,163,184,.18); background: linear-gradient(180deg, #fff 0%, #f8fbff 100%); }
    .soft-panel .table thead th { background: #eef4ff; color: #334155; font-weight: 700; letter-spacing: .02em; }
    .soft-panel .table tbody tr:hover { background: #f8fbff; }
    .search-shell .form-control, .search-shell .btn { border-radius: 12px; }
    .search-shell .btn { padding-inline: .9rem; }
    .pill-chip { display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 6px 10px; font-size: .82rem; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 page-shell">
    <section class="page-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <h2 class="mb-1">Product Inventory</h2>
            <p class="mb-0">Daftar produk, pencarian nama atau kode</p>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center search-shell">
            <form method="GET" action="{{ route('inventory.product.index') }}" class="d-flex gap-2 align-items-center flex-wrap">
                <input type="search" name="search" value="{{ old('search', $search) }}" class="form-control" placeholder="Cari nama atau kode produk..." style="min-width: 280px;">
                <button class="btn btn-light text-primary" type="submit"><i class="fas fa-search me-1"></i> Cari</button>
                @if($search)
                    <a href="{{ route('inventory.product.index') }}" class="btn btn-outline-light">Reset</a>
                @endif
            </form>
            <a href="{{ route('inventory.product.create') }}" class="btn btn-success"><i class="fas fa-plus me-1"></i> Add Product</a>
        </div>
    </section>

    <div class="card soft-panel shadow-sm border-0 rounded-4 overflow-hidden mt-4">
        <div class="card-header bg-white border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h4 class="mb-1">Latest Product List</h4>
                <p class="text-muted mb-0">Data tetap sama, tampilannya dibuat lebih modern dan mudah dibaca.</p>
            </div>
            <span class="pill-chip bg-primary-subtle text-primary"><i class="fas fa-boxes-stacked"></i> {{ $products->count() }} items</span>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Code</th>
                            <th>Category</th>
                            <th>Unit</th>
                            <th>Company</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $product->product_name }}</strong>
                                <div class="small text-muted">{{ Str::limit($product->description, 50) }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark">{{ $product->product_code }}</span></td>
                            <td>{{ $product->category->category_name ?? '-' }}</td>
                            <td>{{ $product->unit->unit_name ?? '-' }}</td>
                            <td>{{ $product->company->company_name ?? '-' }}</td>
                            <td>
                                @if($product->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('inventory.product.show', $product) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('inventory.product.edit', $product) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteProductModal{{ $product->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <div class="modal fade" id="deleteProductModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Delete Product</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center py-4">
                                                <p class="mb-0">Yakin ingin menghapus <strong>{{ $product->product_name }}</strong>?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('inventory.product.destroy', $product) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">No products found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
