@extends('admin.layouts.app')

@push('css')
    <style>
        .page-shell { padding-top: 1rem; }
        .page-hero {
            border-radius: 28px; padding: 24px;
            background: linear-gradient(135deg, #0f172a 0%, #2563eb 45%, #22d3ee 100%);
            color: #fff; box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
        }
        .page-hero .eyebrow { text-transform: uppercase; letter-spacing: .28em; font-size: .72rem; color: rgba(255,255,255,.85); }
        .page-hero p { color: rgba(255,255,255,.88); }
        .soft-panel {
            border-radius: 24px;
            border: 1px solid rgba(148, 163, 184, .18);
            box-shadow: 0 18px 40px rgba(15, 23, 42, .08);
            overflow: hidden;
        }
        .soft-panel .card-header {
            border-bottom: 1px solid rgba(148, 163, 184, .18);
            background: linear-gradient(180deg, #fff 0%, #f8fbff 100%);
        }
        .soft-panel .table thead th {
            background: #eef4ff;
            color: #334155;
            font-weight: 700;
            letter-spacing: .02em;
        }
        .soft-panel .table tbody tr:hover { background: #f8fbff; }
        .search-shell .form-control, .search-shell .btn { border-radius: 12px; }
        .search-shell .btn { padding-inline: .9rem; }
        .pill-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: .82rem;
            font-weight: 600;
        }
        .product-thumb {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            object-fit: cover;
            border: 1px solid #e5e7eb;
            background: #f8fafc;
        }
        .preview-box {
            width: 150px;
            height: 150px;
            border-radius: 18px;
            object-fit: cover;
            border: 1px solid #e5e7eb;
            background: #f8fafc;
        }
        .modal-body .preview-box { width: 120px; height: 120px; }
        .action-buttons .btn-sm { font-size: 0.75rem; padding: 0.2rem 0.5rem; }
        @media (max-width: 768px) {
            .page-hero { padding: 18px; }
            .search-shell .d-flex { flex-direction: column; }
            .search-shell .form-control { min-width: 100% !important; }
        }
    </style>
@endpush

@push('js')
    <script>
        // ==================== PREVIEW GAMBAR ====================
        function setupImagePreview(inputId, previewId, defaultId = null) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const defaultPreview = defaultId ? document.getElementById(defaultId) : null;
            if (!input || !preview) return;
            const defaultSrc = preview.src;

            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) {
                    preview.src = defaultSrc;
                    preview.classList.remove('d-none');
                    if (defaultPreview) defaultPreview.classList.remove('d-none');
                    return;
                }
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Format file tidak didukung. Gunakan JPG, JPEG, PNG, atau WEBP.');
                    input.value = '';
                    preview.src = defaultSrc;
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file maksimal 5MB.');
                    input.value = '';
                    preview.src = defaultSrc;
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(ev) {
                    preview.src = ev.target.result;
                    preview.classList.remove('d-none');
                    if (defaultPreview) defaultPreview.classList.add('d-none');
                };
                reader.readAsDataURL(file);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Preview for Create modal
            setupImagePreview('create_product_image', 'create_image_preview', 'create_default_preview');
            // Preview for Edit modal
            setupImagePreview('edit_product_image', 'edit_image_preview', 'edit_default_preview');

            // ==================== EDIT MODAL ====================
            const editModal = document.getElementById('editModal');
            const editForm = document.getElementById('editForm');
            const editCompany = document.getElementById('edit_company_id');
            const editCategory = document.getElementById('edit_category_id');
            const editUnit = document.getElementById('edit_unit_id');
            const editName = document.getElementById('edit_product_name');
            const editCode = document.getElementById('edit_product_code');
            const editActive = document.getElementById('edit_is_active');
            const editHasVariant = document.getElementById('edit_has_variant');
            const editDesc = document.getElementById('edit_description');
            const editImagePreview = document.getElementById('edit_image_preview');

            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const companyId = button.getAttribute('data-company-id');
                const categoryId = button.getAttribute('data-category-id');
                const unitId = button.getAttribute('data-unit-id');
                const name = button.getAttribute('data-name');
                const code = button.getAttribute('data-code');
                const isActive = button.getAttribute('data-is-active') === '1';
                const hasVariant = button.getAttribute('data-has-variant') === '1';
                const description = button.getAttribute('data-description');
                const image = button.getAttribute('data-image');

                // Set action URL
                const url = "{{ route('inventory.product.update', ':id') }}".replace(':id', id);
                editForm.action = url;

                // Set values
                editCompany.value = companyId;
                editCategory.value = categoryId;
                editUnit.value = unitId;
                editName.value = name;
                editCode.value = code;
                editActive.value = isActive ? '1' : '0';
                editHasVariant.value = hasVariant ? '1' : '0';
                editDesc.value = description || '';
                // Set image preview (default gambar lama)
                if (image) {
                    editImagePreview.src = image;
                    editImagePreview.classList.remove('d-none');
                    document.getElementById('edit_default_preview').classList.add('d-none');
                } else {
                    editImagePreview.src = 'https://placehold.co/120x120?text=No+Image';
                    editImagePreview.classList.add('d-none');
                    document.getElementById('edit_default_preview').classList.remove('d-none');
                }
            });

            // ==================== DELETE MODAL ====================
            const deleteModal = document.getElementById('deleteModal');
            const deleteForm = document.getElementById('deleteForm');
            const deleteName = document.getElementById('delete_product_name');

            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const url = "{{ route('inventory.product.destroy', ':id') }}".replace(':id', id);
                deleteForm.action = url;
                deleteName.textContent = name;
            });
        });
    </script>
@endpush

@section('content')
<div class="container-fluid py-4 page-shell">
    <section class="page-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <h2 class="mb-1">Product Inventory</h2>
            <p class="mb-0">Daftar produk, pencarian nama atau kode</p>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center search-shell">
            <form method="GET" action="{{ route('inventory.product.index') }}"
                class="d-flex gap-2 align-items-center flex-wrap">
                <input type="search" name="search" value="{{ old('search', $search) }}" class="form-control"
                    placeholder="Cari nama" style="min-width: 280px;">
                <button class="btn btn-light text-primary" type="submit"><i class="fas fa-search me-1"></i> Cari</button>
                @if ($search)
                    <a href="{{ route('inventory.product.index') }}" class="btn btn-outline-light">Reset</a>
                @endif
            </form>
            @if ($access['Create'] == 1)
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="fas fa-plus me-1"></i> Add Product
                </button>
            @endif
        </div>
    </section>

    @if ($access['Read'] == 1)
    <div class="card soft-panel shadow-sm border-0 rounded-4 overflow-hidden mt-4">
        <div class="card-header bg-white border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div><h4 class="mb-1">Latest Product List</h4></div>
            <span class="pill-chip bg-primary-subtle text-primary"><i class="fas fa-boxes-stacked"></i> {{ $products->count() }} items</span>
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
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Product</th>
                            {{-- <th>Code</th> --}}
                            {{-- <th>Category</th> --}}
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
                                <img src="{{ $product->image_url }}" alt="{{ $product->product_name }}" class="product-thumb">
                            </td>
                            <td>
                                <strong>{{ $product->product_name }}</strong>
                                <div class="small text-muted">{{ Str::limit($product->description, 25) }}</div>
                            </td>
                            {{-- <td><span class="badge bg-light text-dark">{{ $product->product_code }}</span></td> --}}
                            {{-- <td>{{ $product->category->category_name ?? '-' }}</td> --}}
                            <td>{{ $product->unit->unit_name ?? '-' }}</td>
                            <td>{{ $product->company->company_name ?? '-' }}</td>
                            <td>
                                @if ($product->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    @if ($access['Update'] == 1)
                                        <button type="button" class="btn btn-sm btn-warning btn-edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal"
                                            data-id="{{ $product->id }}"
                                            data-company-id="{{ $product->company_id }}"
                                            data-category-id="{{ $product->category_id }}"
                                            data-unit-id="{{ $product->unit_id }}"
                                            data-name="{{ $product->product_name }}"
                                            data-code="{{ $product->product_code }}"
                                            data-is-active="{{ $product->is_active ? '1' : '0' }}"
                                            data-has-variant="{{ $product->has_variant ? '1' : '0' }}"
                                            data-description="{{ $product->description }}"
                                            data-image="{{ $product->image ? asset('storage/' . $product->image) : '' }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endif
                                    @if ($access['Delete'] == 1)
                                        <button type="button" class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            data-id="{{ $product->id }}"
                                            data-name="{{ $product->product_name }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">No products found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-center">
                {{ $products->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

@if ($access['Create'] == 1)
<!-- ============================================================== -->
<!-- MODAL CREATE -->
<!-- ============================================================== -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add New Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('inventory.product.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Outlet</label>
                            <select name="company_id" class="form-select select2 @error('company_id') is-invalid @enderror" required>
                                <option value="">Select Outlet</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->company_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('company_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        {{-- <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select select2 @error('category_id') is-invalid @enderror" required>
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div> --}}
                        <div class="col-md-6">
                            <label class="form-label">Unit</label>
                            <select name="unit_id" class="form-select select2 @error('unit_id') is-invalid @enderror" required>
                                <option value="">Select Unit</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->unit_code }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="product_name" value="{{ old('product_name') }}"
                                class="form-control @error('product_name') is-invalid @enderror" required placeholder="Ayam Kecil">
                            @error('product_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        {{-- <div class="col-md-6">
                            <label class="form-label">Product Code</label>
                            <input type="text" name="product_code" value="{{ old('product_code') }}"
                                class="form-control @error('product_code') is-invalid @enderror" required placeholder="AK-001">
                            @error('product_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div> --}}
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="is_active" class="form-select select2 @error('is_active') is-invalid @enderror" required>
                                <option value="1" {{ old('is_active', 1) == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Has Variant</label>
                            <select name="has_variant" class="form-select select2 @error('has_variant') is-invalid @enderror" required>
                                <option value="1" {{ old('has_variant') == '1' ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ old('has_variant', 0) == '0' ? 'selected' : '' }}>No</option>
                            </select>
                            @error('has_variant') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Product Image</label>
                            <input type="file" id="create_product_image" name="image"
                                class="form-control @error('image') is-invalid @enderror"
                                accept="image/jpg,image/jpeg,image/png,image/webp">
                            <small class="text-muted">Allowed: JPG, JPEG, PNG, WEBP (Max 5MB)</small>
                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="d-flex justify-content-center mt-3">
                                <img id="create_image_preview" class="preview-box d-none" src="#" alt="Preview">
                                <img id="create_default_preview" class="preview-box" src="https://placehold.co/120x120?text=Product+Image" alt="No Image">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@if ($access['Update'] == 1)
<!-- ============================================================== -->
<!-- MODAL EDIT -->
<!-- ============================================================== -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Outlet</label>
                            <select id="edit_company_id" name="company_id" class="form-select select2 @error('company_id') is-invalid @enderror" required>
                                <option value="">Select Outlet</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                @endforeach
                            </select>
                            @error('company_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        {{-- <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select id="edit_category_id" name="category_id" class="form-select select2 @error('category_id') is-invalid @enderror" required>
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div> --}}
                        <div class="col-md-6">
                            <label class="form-label">Unit</label>
                            <select id="edit_unit_id" name="unit_id" class="form-select select2 @error('unit_id') is-invalid @enderror" required>
                                <option value="">Select Unit</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->unit_code }}</option>
                                @endforeach
                            </select>
                            @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Product Name</label>
                            <input type="text" id="edit_product_name" name="product_name"
                                class="form-control @error('product_name') is-invalid @enderror" required>
                            @error('product_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        {{-- <div class="col-md-6">
                            <label class="form-label">Product Code</label>
                            <input type="text" id="edit_product_code" name="product_code"
                                class="form-control @error('product_code') is-invalid @enderror" required>
                            @error('product_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div> --}}
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select id="edit_is_active" name="is_active" class="form-select select2 @error('is_active') is-invalid @enderror" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Has Variant</label>
                            <select id="edit_has_variant" name="has_variant" class="form-select select2 @error('has_variant') is-invalid @enderror" required>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                            @error('has_variant') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea id="edit_description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror" required></textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Product Image</label>
                            <input type="file" id="edit_product_image" name="image"
                                class="form-control @error('image') is-invalid @enderror"
                                accept="image/jpg,image/jpeg,image/png,image/webp">
                            <small class="text-muted">Allowed: JPG, JPEG, PNG, WEBP (Max 5MB) – kosongkan jika tidak ingin mengubah</small>
                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="d-flex justify-content-center mt-3">
                                <img id="edit_image_preview" class="preview-box d-none" src="#" alt="Preview">
                                <img id="edit_default_preview" class="preview-box" src="https://placehold.co/120x120?text=Product+Image" alt="No Image">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-dark"><i class="fas fa-save me-1"></i> Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@if ($access['Delete'] == 1)
<!-- ============================================================== -->
<!-- MODAL DELETE -->
<!-- ============================================================== -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Delete Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center py-4">
                    <div class="mb-3"><i class="fas fa-trash-alt text-danger" style="font-size: 4rem;"></i></div>
                    <h5 class="mb-3">Are you sure?</h5>
                    <p class="text-muted mb-0">You are about to delete <strong id="delete_product_name"></strong>.</p>
                    <p class="text-danger small mt-2 mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-2"></i> Delete Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection