@extends('admin.layouts.app')

@push('css')
<style>
    .page-shell { padding-top: 1rem; }
    .page-hero { border-radius: 28px; padding: 24px; background: linear-gradient(135deg, #111827 0%, #2563eb 45%, #22d3ee 100%); color:#fff; box-shadow: 0 18px 40px rgba(15,23,42,.18); }
    .page-hero .eyebrow { text-transform: uppercase; letter-spacing: .28em; font-size: .72rem; color: rgba(255,255,255,.82); }
    .soft-card { border-radius: 24px; border: 1px solid rgba(148,163,184,.18); box-shadow: 0 18px 40px rgba(15,23,42,.08); overflow: hidden; }
    .soft-card .card-header { border-bottom: 1px solid rgba(148,163,184,.18); background: linear-gradient(180deg, #fff 0%, #f8fbff 100%); }
    .info-card { border-radius: 18px; background: linear-gradient(180deg, #fff 0%, #f8fbff 100%); border: 1px solid #e5eefb; }
    .table thead th { background: #eef4ff; color: #334155; font-weight: 700; }
    .table tbody tr:hover { background: #f8fbff; }
    @media (max-width: 768px) { .page-hero { padding: 18px; } .page-hero .btn { width:100%; } .page-hero .d-flex { width:100%; flex-direction:column; align-items:stretch; } }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 page-shell">
    <section class="page-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <p class="eyebrow mb-2">Inventory</p>
            <h2 class="mb-1">Product Detail</h2>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('inventory.product.index') }}" class="btn btn-outline-light"><i class="fas fa-arrow-left me-1"></i> Back</a>
            <a href="{{ route('inventory.product.edit', $product) }}" class="btn btn-warning text-dark"><i class="fas fa-edit me-1"></i> Edit</a>
        </div>
    </section>

    <div class="card soft-card mt-4">
        <div class="card-header">
            <h4 class="mb-1">Product Overview</h4>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card info-card h-100">
                        <div class="card-body">
                            <h5 class="mb-3">{{ $product->product_name }}</h5>
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Code</dt><dd class="col-sm-8">{{ $product->product_code }}</dd>
                                <dt class="col-sm-4">Company</dt><dd class="col-sm-8">{{ $product->company->company_name ?? '-' }}</dd>
                                <dt class="col-sm-4">Category</dt><dd class="col-sm-8">{{ $product->category->category_name ?? '-' }}</dd>
                                <dt class="col-sm-4">Unit</dt><dd class="col-sm-8">{{ $product->unit->unit_name ?? '-' }}</dd>
                                <dt class="col-sm-4">Has Variant</dt><dd class="col-sm-8">{{ $product->has_variant ? 'Yes' : 'No' }}</dd>
                                <dt class="col-sm-4">Status</dt><dd class="col-sm-8">{{ $product->is_active ? 'Active' : 'Inactive' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card info-card h-100">
                        <div class="card-body">
                            <h5 class="mb-3">Description</h5>
                            <p class="text-muted mb-0">{{ $product->description ?: 'No description provided.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <h5 class="mb-3">Variants</h5>
                @if($product->variants->count())
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead>
                                <tr><th>#</th><th>Variant Name</th><th>Variant Code</th><th>Purchase Price</th><th>Selling Price</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                            @foreach($product->variants as $variant)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $variant->variant_name ?? '-' }}</td>
                                    <td>{{ $variant->variant_code ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::rupiah($variant->purchase_price ?? 0, 0, ',', '.') }}</td>
                                    <td>{{ \Carbon\Carbon::rupiah($variant->selling_price ?? 0, 0, ',', '.') }}</td>
                                    <td>@if($variant->is_active)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-secondary mb-0">No variant data found for this product.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
