@extends('admin.layouts.app')

@push('css')
<style>
    .page-shell { padding-top: 1rem; }
    .page-hero { border-radius: 28px; padding: 24px; background: linear-gradient(135deg, #111827 0%, #2563eb 45%, #22d3ee 100%); color:#fff; box-shadow: 0 18px 40px rgba(15,23,42,.18); }
    .page-hero .eyebrow { text-transform: uppercase; letter-spacing: .28em; font-size: .72rem; color: rgba(255,255,255,.82); }
    .soft-card { border-radius: 24px; border: 1px solid rgba(148,163,184,.18); box-shadow: 0 18px 40px rgba(15,23,42,.08); overflow: hidden; }
    .soft-card .card-header { border-bottom: 1px solid rgba(148,163,184,.18); background: linear-gradient(180deg, #fff 0%, #f8fbff 100%); }
    .form-label { font-weight: 600; color: #334155; }
    .form-control, .form-select { border-radius: 14px; border-color: #cbd5e1; padding: .72rem .9rem; }
    .form-control:focus, .form-select:focus { border-color: #2563eb; box-shadow: 0 0 0 .18rem rgba(37,99,235,.15); }
    .action-bar { border-top: 1px solid rgba(148,163,184,.18); margin-top: 1rem; padding-top: 1rem; }
    @media (max-width: 768px) { .page-hero { padding: 18px; } .action-bar .btn { width:100%; } .action-bar { flex-direction:column-reverse; } }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 page-shell">
    <section class="page-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <p class="eyebrow mb-2">Inventory</p>
            <h2 class="mb-1">Update Stock</h2>
        </div>
        <span class="badge bg-white text-primary fs-6 px-3 py-2"><i class="fas fa-pen me-1"></i> Edit stock</span>
    </section>

    <div class="card soft-card mt-4">
        <div class="card-header">
            <h4 class="mb-1">Stock Details</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('inventory.stock.update', $stock) }}" method="POST">
                @method('PUT')
                @csrf
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="product_variant_id">Product Variant</label>
                        <select id="product_variant_id" name="product_variant_id" class="form-select select2 @error('product_variant_id') is-invalid @enderror">
                            <option value="">Select Variant</option>
                            @foreach ($productVariants as $variant)
                                <option value="{{ $variant->id }}" {{ old('product_variant_id', $stock->product_variant_id) == $variant->id ? 'selected' : '' }}>
                                    {{ $variant->product->product_name ?? '-' }} - {{ $variant->variant_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_variant_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="current_stock">Current Stock</label>
                        <input type="number" step="0.01" id="current_stock" name="current_stock" value="{{ old('current_stock', $stock->current_stock) }}" class="form-control @error('current_stock') is-invalid @enderror" placeholder="0.00">
                        @error('current_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 action-bar">
                    <a href="{{ route('inventory.stock.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection