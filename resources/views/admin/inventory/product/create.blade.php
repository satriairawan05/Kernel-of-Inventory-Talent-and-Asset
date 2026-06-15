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
    .preview-box {
        width: 150px;
        height: 150px;
        border-radius: 18px;
        object-fit: cover;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
    }
    .action-bar { border-top: 1px solid rgba(148,163,184,.18); margin-top: 1rem; padding-top: 1rem; }
    @media (max-width: 768px) { .page-hero { padding: 18px; } .action-bar .btn { width:100%; } .action-bar { flex-direction:column-reverse; } }
</style>
@endpush

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('product_image');
        const preview = document.getElementById('imagePreview');
        const defaultPreview = document.getElementById('defaultPreview');
        
        if (!input || !preview) return;
        
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) {
                // Jika tidak ada file, tampilkan default preview dan sembunyikan preview baru
                preview.classList.add('d-none');
                defaultPreview.classList.remove('d-none');
                return;
            }
            
            // Validasi tipe file
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('Invalid file type. Please upload JPG, JPEG, PNG, or WEBP file.');
                input.value = ''; // Reset input
                preview.classList.add('d-none');
                defaultPreview.classList.remove('d-none');
                return;
            }
            
            // Validasi ukuran file (max 5MB)
            const maxSize = 5 * 1024 * 1024; // 5MB in bytes
            if (file.size > maxSize) {
                alert('File size must not exceed 5MB.');
                input.value = ''; // Reset input
                preview.classList.add('d-none');
                defaultPreview.classList.remove('d-none');
                return;
            }
            
            // Sembunyikan default preview
            defaultPreview.classList.add('d-none');
            
            // Tampilkan preview gambar baru
            const reader = new FileReader();
            reader.onload = function(event) {
                preview.src = event.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        });
    });
</script>
@endpush

@section('content')
<div class="container-fluid py-4 page-shell">
    <section class="page-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <p class="eyebrow mb-2">Inventory</p>
            <h2 class="mb-1">Create Product</h2>
        </div>
        <a href="{{ route('inventory.product.index') }}" class="btn btn-outline-light"><i class="fas fa-arrow-left me-1"></i> Back</a>
    </section>

    <div class="card soft-card mt-4">
        <div class="card-header">
            <h4 class="mb-1">Product Details</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('inventory.product.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label class="form-label">Outlet</label>
                    <select name="company_id" class="form-select @error('company_id') is-invalid @enderror" required>
                        <option value="">Select Outlet</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->company_name }}</option>
                        @endforeach
                    </select>
                    @error('company_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Unit</label>
                    <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required>
                        <option value="">Select Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->unit_code }}</option>
                        @endforeach
                    </select>
                    @error('unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="product_name" placeholder="Ayam Kecil" value="{{ old('product_name') }}" class="form-control @error('product_name') is-invalid @enderror" required>
                    @error('product_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Product Code</label>
                    <input type="text" name="product_code" placeholder="AK-001" value="{{ old('product_code') }}" class="form-control @error('product_code') is-invalid @enderror" required>
                    @error('product_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                        <option value="1" {{ old('is_active', 1) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-12">
                    <label class="form-label">Has Variant</label>
                    <select name="has_variant" class="form-select @error('has_variant') is-invalid @enderror" required>
                        <option value="1" {{ old('has_variant') == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('has_variant', 0) == '0' ? 'selected' : '' }}>No</option>
                    </select>
                    @error('has_variant')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" placeholder="lorem ipsum" rows="4" class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-12">
                    <label class="form-label">Product Image</label>
                    <input type="file" id="product_image" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpg,image/jpeg,image/png,image/webp">
                    <small class="text-muted">Allowed formats: JPG, JPEG, PNG, WEBP (Max 5MB)</small>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="d-flex justify-content-center mt-3">
                        <img id="imagePreview" class="preview-box d-none" src="#" alt="Product Image Preview">
                        <img id="defaultPreview" class="preview-box" src="https://placehold.co/150x150?text=Product+Image" alt="No Image">
                    </div>
                </div>
                
                <div class="col-12 d-flex justify-content-end gap-2 action-bar">
                    <a href="{{ route('inventory.product.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection