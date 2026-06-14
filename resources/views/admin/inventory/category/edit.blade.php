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
            <h2 class="mb-1">Update Category</h2>
        </div>
        <span class="badge bg-white text-primary fs-6 px-3 py-2"><i class="fas fa-pen me-1"></i> Edit category</span>
    </section>

    <div class="card soft-card mt-4">
        <div class="card-header">
            <h4 class="mb-1">Category Details</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('inventory.category.update', $category) }}" method="POST">
                @method('PUT')
                @csrf
                <div class="row g-3">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="company_id">Company</label>
                        <select id="company_id" name="company_id" class="form-select @error('company_id') is-invalid @enderror">
                            <option value="">Select Company</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id', $category->company_id) == $company->id ? 'selected' : '' }}>{{ $company->company_name }}</option>
                            @endforeach
                        </select>
                        @error('company_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label" for="category_name">Category Name</label>
                        <input type="text" id="category_name" name="category_name" value="{{ old('category_name', $category->category_name) }}" class="form-control @error('category_name') is-invalid @enderror" placeholder="Example: Electronics, Furniture, etc.">
                        @error('category_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="description">Description</label>
                    <textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="Example: Category for IT equipment and assets.">{{ old('description', $category->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="d-flex justify-content-end gap-2 action-bar">
                    <a href="{{ route('inventory.category.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection