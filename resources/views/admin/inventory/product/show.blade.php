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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Klik gambar untuk memperbesar
            const productImage = document.querySelector('.product-image');
            if (productImage) {
                productImage.style.cursor = 'pointer';
                productImage.addEventListener('click', function() {
                    // Buat modal untuk preview gambar besar
                    const modalHtml = `
                    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">Product Image</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center p-4">
                                    <img src="${this.src}" alt="Product Image" class="img-fluid rounded" style="max-height: 70vh;">
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                    // Hapus modal yang sudah ada jika ada
                    const existingModal = document.getElementById('imageModal');
                    if (existingModal) {
                        existingModal.remove();
                    }

                    // Tambahkan modal ke body
                    document.body.insertAdjacentHTML('beforeend', modalHtml);

                    // Tampilkan modal
                    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
                    modal.show();

                    // Hapus modal setelah ditutup
                    document.getElementById('imageModal').addEventListener('hidden.bs.modal', function() {
                        this.remove();
                    });
                });
            }
        });
    </script>
@endpush

@section('content')
    <div class="container-fluid py-4 page-shell">
        <section class="page-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <p class="eyebrow mb-2">Inventory</p>
                <h2 class="mb-1">Product Detail</h2>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('inventory.product.index') }}" class="btn btn-outline-light"><i
                        class="fas fa-arrow-left me-1"></i> Back</a>
                <a href="{{ route('inventory.product.edit', $product) }}" class="btn btn-warning text-dark"><i
                        class="fas fa-edit me-1"></i> Edit</a>
            </div>
        </section>

        <div class="card soft-card mt-4">
            <div class="card-header">
                <h4 class="mb-1">Product Overview</h4>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <!-- Kolom Kiri: Informasi Produk -->
                    <div class="col-lg-7">
                        <div class="card info-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="mb-0">{{ $product->product_name }}</h5>
                                    @if ($product->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </div>
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Product Code</dt>
                                    <dd class="col-sm-8"><code>{{ $product->product_code }}</code></dd>

                                    <dt class="col-sm-4">Company / Outlet</dt>
                                    <dd class="col-sm-8">{{ $product->company->company_name ?? '-' }}</dd>

                                    <dt class="col-sm-4">Category</dt>
                                    <dd class="col-sm-8">{{ $product->category->category_name ?? '-' }}</dd>

                                    <dt class="col-sm-4">Unit</dt>
                                    <dd class="col-sm-8">{{ $product->unit->unit_name ?? '-' }}</dd>

                                    <dt class="col-sm-4">Has Variant</dt>
                                    <dd class="col-sm-8">
                                        @if ($product->has_variant)
                                            <span class="badge bg-primary">
                                                <i class="fas fa-tag me-1"></i> Yes ({{ $product->variants->count() }}
                                                variants)
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-ban me-1"></i> No
                                            </span>
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Gambar Produk -->
                    <div class="col-lg-5">
                        <div class="card info-card h-100">
                            <div class="card-body text-center">
                                <h5 class="mb-3">Product Image</h5>
                                <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->product_name }}"
                                    class="product-image"
                                    onerror="this.src='{{ asset('assets/img/icons/av color.png') }}'">
                                @if (!$product->image)
                                    <p class="text-muted mt-3 mb-0"><small>No image uploaded</small></p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Deskripsi Produk -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card info-card">
                            <div class="card-body">
                                <h5 class="mb-3">Description</h5>
                                <p class="text-muted mb-0">{{ $product->description ?: 'No description provided.' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Add Product Variant (DITAMBAHKAN DI SINI) -->
                <div class="mt-4 d-flex justify-content-end">
                    <a href="#" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Add Product Variant
                    </a>
                </div>

                <!-- Daftar Variant -->
                <div class="mt-4">
                    <h5 class="mb-3">Product Variants</h5>
                    @if ($product->variants && $product->variants->count())
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
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
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $variant->variant_name ?? '-' }}</td>
                                            <td><code>{{ $variant->variant_code ?? '-' }}</code></td>
                                            <td class="text-end">
                                                {{ number_format($variant->purchase_price ?? 0, 0, ',', '.') }}</td>
                                            <td class="text-end">
                                                {{ number_format($variant->selling_price ?? 0, 0, ',', '.') }}</td>
                                            <td>
                                                @if ($variant->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="{{ route('inventory.product-variant.edit', [$product, $variant]) }}" 
                                                       class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteVariantModal{{ $variant->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>

                                                <!-- Modal Delete Variant -->
                                                <div class="modal fade" id="deleteVariantModal{{ $variant->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content border-0 shadow">
                                                            <div class="modal-header bg-danger text-white">
                                                                <h5 class="modal-title">Delete Variant</h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body text-center py-4">
                                                                <p class="mb-0">Yakin ingin menghapus variant <strong>{{ $variant->variant_name }}</strong>?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <form action="{{ route('inventory.product-variant.destroy', [$product, $variant]) }}" method="POST">
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
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-secondary mb-0">
                            <i class="fas fa-info-circle me-2"></i> No variant data found for this product.
                            <a href="#" class="alert-link ms-2">
                                Click here to add first variant →
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection