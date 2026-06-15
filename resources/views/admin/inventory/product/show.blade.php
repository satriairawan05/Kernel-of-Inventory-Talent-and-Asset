@extends('admin.layouts.app')

@push('css')
    <style>
        .page-shell {
            padding-top: 1rem;
        }

        .page-hero {
            border-radius: 28px;
            padding: 24px;
            background: linear-gradient(135deg, #111827 0%, #2563eb 45%, #22d3ee 100%);
            color: #fff;
            box-shadow: 0 18px 40px rgba(15, 23, 42, .18);
        }

        .page-hero .eyebrow {
            text-transform: uppercase;
            letter-spacing: .28em;
            font-size: .72rem;
            color: rgba(255, 255, 255, .82);
        }

        .soft-card {
            border-radius: 24px;
            border: 1px solid rgba(148, 163, 184, .18);
            box-shadow: 0 18px 40px rgba(15, 23, 42, .08);
            overflow: hidden;
        }

        .soft-card .card-header {
            border-bottom: 1px solid rgba(148, 163, 184, .18);
            background: linear-gradient(180deg, #fff 0%, #f8fbff 100%);
        }

        .info-card {
            border-radius: 18px;
            background: linear-gradient(180deg, #fff 0%, #f8fbff 100%);
            border: 1px solid #e5eefb;
        }

        .product-image {
            width: 100%;
            max-width: 280px;
            height: auto;
            border-radius: 18px;
            object-fit: cover;
            border: 1px solid #e5e7eb;
            background: #f8fafc;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
        }

        .variant-thumb {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #e5e7eb;
        }

        .image-preview-container {
            text-align: center;
            margin-top: 10px;
        }

        .image-preview-container img {
            max-height: 150px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            object-fit: cover;
        }

        .table thead th {
            background: #eef4ff;
            color: #334155;
            font-weight: 700;
        }

        .table tbody tr:hover {
            background: #f8fbff;
        }

        @media (max-width: 768px) {
            .page-hero {
                padding: 18px;
            }

            .page-hero .btn {
                width: 100%;
            }

            .page-hero .d-flex {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
            }

            .product-image {
                max-width: 100%;
            }
        }
    </style>
@endpush

@push('js')
    <script type="text/javascript">
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (!preview) return;

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'inline-block';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                const defaultSrc = preview.getAttribute('data-default-src');
                if (defaultSrc) {
                    preview.src = defaultSrc;
                    preview.style.display = 'inline-block';
                } else {
                    preview.style.display = 'none';
                }
            }
        }

        function parsePrice(value) {
            if (value === null || value === undefined) return '';
            const raw = value.toString().trim();
            if (!raw) return '';
            const normalized = raw.replace(/\./g, '').replace(/,/g, '.').replace(/[^0-9.]/g, '');
            const num = parseFloat(normalized);
            if (isNaN(num)) return '';
            return Math.min(num, 9999999999999.99).toFixed(2);
        }

        function formatRupiah(value) {
            if (value === null || value === undefined || value === '') return '';
            const num = parseFloat(value);
            if (isNaN(num)) return '';
            const fixed = Math.min(num, 9999999999999.99).toFixed(2);
            let [intPart, decPart] = fixed.split('.');
            intPart = parseInt(intPart, 10).toLocaleString('id-ID');
            return decPart === '00' ? intPart : `${intPart},${decPart}`;
        }

        function syncPriceInput(input) {
            if (!input || !input.classList.contains('price-format')) return;
            const raw = parsePrice(input.value);
            if (raw === '') {
                input.setAttribute('data-raw', '');
                input.value = '';
                return;
            }
            input.setAttribute('data-raw', raw);
            input.value = formatRupiah(raw);
        }

        function setInitialPriceState(container) {
            container.querySelectorAll('.price-format').forEach(input => {
                const raw = input.getAttribute('data-raw');

                if (raw !== null && raw !== '') {
                    input.value = formatRupiah(raw);
                    return;
                }

                const parsed = parsePrice(input.value);
                if (parsed === '') {
                    input.value = '';
                    input.setAttribute('data-raw', '');
                    return;
                }

                input.setAttribute('data-raw', parsed);
                input.value = formatRupiah(parsed);
            });
        }

        function preparePriceForSubmit(form) {
            form.querySelectorAll('.price-format').forEach(input => {
                input.value = input.getAttribute('data-raw') || '';
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('input', function(e) {
                if (!e.target.classList.contains('price-format')) return;
                syncPriceInput(e.target);
            });

            const addModal = document.getElementById('addVariantModal');
            if (addModal) {
                setInitialPriceState(addModal);
                addModal.addEventListener('show.bs.modal', function() {
                    setInitialPriceState(addModal);
                });
            }

            document.querySelectorAll('[id^="editVariantModal"]').forEach(modal => {
                setInitialPriceState(modal);
                modal.addEventListener('show.bs.modal', function() {
                    setInitialPriceState(modal);
                });
            });

            const addForm = document.querySelector('#addVariantModal form');
            if (addForm) {
                addForm.addEventListener('submit', function() {
                    preparePriceForSubmit(addForm);
                });
            }

            document.querySelectorAll('[id^="editVariantModal"] form').forEach(form => {
                form.addEventListener('submit', function() {
                    preparePriceForSubmit(form);
                });
            });
        });
    </script>
@endpush

@section('content')
    <div class="container-fluid py-4 page-shell">
        @if (session('variant_success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('variant_success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <section class="page-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <p class="eyebrow mb-2">Inventory</p>
                <h2 class="mb-1">Product Detail</h2>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('inventory.product.index') }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
                <a href="{{ route('inventory.product.edit', $product) }}" class="btn btn-warning text-dark">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
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
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="mb-0">{{ $product->product_name }}</h5>
                                    <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Product Code</dt>
                                    <dd class="col-sm-8"><code>{{ $product->product_code }}</code></dd>
                                    <dt class="col-sm-4">Outlet</dt>
                                    <dd class="col-sm-8">{{ $product->company->company_name ?? '-' }}</dd>
                                    <dt class="col-sm-4">Category</dt>
                                    <dd class="col-sm-8">{{ $product->category->category_name ?? '-' }}</dd>
                                    <dt class="col-sm-4">Unit</dt>
                                    <dd class="col-sm-8">{{ $product->unit->unit_name ?? '-' }}</dd>
                                    <dt class="col-sm-4">Has Variant</dt>
                                    <dd class="col-sm-8">
                                        <span class="badge {{ $product->has_variant ? 'bg-primary' : 'bg-secondary' }}">
                                            <i class="fas {{ $product->has_variant ? 'fa-tag' : 'fa-ban' }} me-1"></i>
                                            {{ $product->has_variant ? "Yes ({$product->variants->count()} variants)" : 'No' }}
                                        </span>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="card info-card h-100">
                            <div class="card-body text-center">
                                <h5 class="mb-3">Product Image</h5>
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->product_name }}"
                                    class="product-image"
                                    onerror="this.src='{{ asset('assets/img/icons/av color.png') }}'">
                                @if (!$product->image)
                                    <p class="text-muted mt-3 mb-0"><small>No image uploaded</small></p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVariantModal">
                        <i class="fas fa-plus me-2"></i> Add Product Variant
                    </button>
                </div>

                <div class="mt-4">
                    <h5 class="mb-3">Product Variants</h5>
                    @if ($product->variants && $product->variants->count())
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Variant Name</th>
                                        <th>Variant Code</th>
                                        <th class="text-end">Purchase Price</th>
                                        <th class="text-end">Selling Price</th>
                                        <th>Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($product->variants as $variant)
                                        <tr>
                                            <td class="align-middle">{{ $loop->iteration }}</td>
                                            <td class="align-middle">
                                                <img src="{{ asset('storage/' . $variant->image) }}" class="variant-thumb"
                                                    onerror="this.src='{{ asset('assets/img/icons/av color.png') }}'">
                                            </td>
                                            <td class="align-middle">{{ $variant->variant_name ?? '-' }}</td>
                                            <td class="align-middle"><code>{{ $variant->variant_code ?? '-' }}</code></td>
                                            <td class="text-end align-middle">
                                                {{ \Carbon\Carbon::rupiah($variant->purchase_price) }}</td>
                                            <td class="text-end align-middle">
                                                {{ \Carbon\Carbon::rupiah($variant->selling_price) }}</td>
                                            <td class="align-middle">
                                                <span
                                                    class="badge {{ $variant->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $variant->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="text-end align-middle">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="button" class="btn btn-sm btn-warning"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editVariantModal{{ $variant->id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteVariantModal{{ $variant->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-secondary mb-0">
                            <i class="fas fa-info-circle me-2"></i> No variant data found for this product.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add Variant -->
    <div class="modal fade" id="addVariantModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Product Variant</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('inventory.product.product-variant.store', $product) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Variant Name</label>
                            <input type="text" name="variant_name"
                                class="form-control @error('variant_name') is-invalid @enderror"
                                value="{{ old('variant_name') }}" placeholder="e.g., XL, Red, 256GB">
                            @error('variant_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Variant Code</label>
                            <input type="text" name="variant_code"
                                class="form-control @error('variant_code') is-invalid @enderror"
                                value="{{ old('variant_code') }}" placeholder="e.g., VNT-001">
                            @error('variant_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Variant Image</label>
                            <input type="file" name="image"
                                class="form-control @error('image') is-invalid @enderror" accept="image/*"
                                onchange="previewImage(this, 'add_image_preview')">
                            <div class="image-preview-container text-start mt-3">
                                <img id="add_image_preview" src="" alt="Preview Image" style="display:none;">
                            </div>
                            @error('image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Purchase Price</label>
                            <input type="text" name="purchase_price"
                                class="form-control price-format @error('purchase_price') is-invalid @enderror"
                                value="{{ old('purchase_price', '') }}" data-raw="{{ old('purchase_price', '') }}">
                            @error('purchase_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Selling Price</label>
                            <input type="text" name="selling_price"
                                class="form-control price-format @error('selling_price') is-invalid @enderror"
                                value="{{ old('selling_price', '') }}" data-raw="{{ old('selling_price', '') }}">
                            @error('selling_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="add_is_active"
                                value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="add_is_active">Active Status</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Variant</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @includeWhen(
        $product->variants && $product->variants->count(),
        'admin.inventory.product.partials.variant-modals',
        ['product' => $product]
    )
@endsection
