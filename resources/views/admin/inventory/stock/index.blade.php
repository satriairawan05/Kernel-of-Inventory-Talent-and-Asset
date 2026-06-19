@extends('admin.layouts.app')

@push('css')
    <style>
        .page-shell { padding-top: 1rem; }
        .page-hero {
            border-radius: 28px; padding: 24px;
            background: linear-gradient(135deg, #111827 0%, #2563eb 45%, #22d3ee 100%);
            color: #fff; box-shadow: 0 18px 40px rgba(15, 23, 42, .18);
        }
        .page-hero .eyebrow { text-transform: uppercase; letter-spacing: .28em; font-size: .72rem; color: rgba(255,255,255,.82); }
        .soft-card {
            border-radius: 24px; border: 1px solid rgba(148,163,184,.18);
            box-shadow: 0 18px 40px rgba(15,23,42,.08);
            overflow: hidden;
        }
        .soft-card .card-header {
            border-bottom: 1px solid rgba(148,163,184,.18);
            background: linear-gradient(180deg, #fff 0%, #f8fbff 100%);
        }
        .stat-chip {
            border-radius: 999px; padding: 6px 10px; font-weight: 600; font-size: .82rem;
        }
        .table thead th { background: #eef4ff; color: #334155; font-weight: 700; }
        .table tbody tr:hover { background: #f8fbff; }
        .action-buttons .btn-sm { font-size: 0.75rem; padding: 0.2rem 0.5rem; }
        @media (max-width: 768px) {
            .page-hero { padding: 18px; }
            .page-hero .btn { width: 100%; }
            .action-buttons { display:flex; flex-direction:column; gap:.45rem; min-width:120px; }
            .action-buttons .btn { width:100%; }
            .pagination-wrapper { flex-direction:column; gap:.75rem; align-items:stretch; }
            .pagination-wrapper > div { width:100%; }
        }
    </style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ==================== EDIT MODAL ====================
            const editModal = document.getElementById('editModal');
            const editForm = document.getElementById('editForm');
            const editVariant = document.getElementById('edit_product_variant_id');
            const editStock = document.getElementById('edit_current_stock');

            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const variantId = button.getAttribute('data-variant-id');
                const currentStock = button.getAttribute('data-current-stock');

                // Set action URL
                const url = "{{ route('inventory.stock.update', ':id') }}".replace(':id', id);
                editForm.action = url;

                // Set values
                editVariant.value = variantId;
                editStock.value = currentStock;
            });

            // ==================== DELETE MODAL ====================
            const deleteModal = document.getElementById('deleteModal');
            const deleteForm = document.getElementById('deleteForm');
            const deleteName = document.getElementById('delete_stock_name');

            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');

                const url = "{{ route('inventory.stock.destroy', ':id') }}".replace(':id', id);
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
            <p class="eyebrow mb-2">Inventory</p>
            <h2 class="mb-1">Stock Management</h2>
            <p class="mb-0">Kelola stok produk berdasarkan varian produk.</p>
        </div>
        @if ($access['Create'] == 1)
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="fas fa-plus me-1"></i> Add New Stock
            </button>
        @endif
    </section>

    @if ($access['Read'] == 1)
    <div class="card soft-card mt-4">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h4 class="mb-1">List of Stock</h4>
                <p class="text-muted mb-0">Data stok produk varian.</p>
            </div>
            <span class="stat-chip bg-primary-subtle text-primary"><i class="fas fa-boxes me-1"></i>
                {{ $stocks->total() }} total records</span>
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

            <div class="table-responsive mb-3">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Product</th>
                            <th scope="col">Variant</th>
                            <th scope="col">Current Stock</th>
                            <th scope="col">Last Updated</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stocks as $stock)
                            <tr>
                                <th scope="row">{{ $stocks->firstItem() + $loop->index }}</th>
                                <td>{{ $stock->productVariant->product->product_name ?? '-' }}</td>
                                <td>{{ $stock->productVariant->variant_name ?? '-' }}</td>
                                <td class="fw-bold {{ $stock->current_stock <= 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($stock->current_stock, 2, ',', '.') }}
                                </td>
                                <td class="text-nowrap">{{ $stock->updated_at->format('d/m/Y H:i') }}</td>
                                <td class="action-buttons d-md-flex flex-md-row align-items-md-center gap-2">
                                    @if ($access['Update'] == 1)
                                        <button type="button" class="btn btn-sm btn-warning btn-edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal"
                                            data-id="{{ $stock->id }}"
                                            data-variant-id="{{ $stock->product_variant_id }}"
                                            data-current-stock="{{ $stock->current_stock }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    @endif
                                    @if ($access['Delete'] == 1)
                                        <button type="button" class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            data-id="{{ $stock->id }}"
                                            data-name="{{ ($stock->productVariant->product->product_name ?? '') . ' - ' . ($stock->productVariant->variant_name ?? '') }}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No Stock Data Found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3 pagination-wrapper">
                {{ $stocks->links() }}
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
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add New Stock</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('inventory.stock.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="create_product_variant_id">Product Variant</label>
                            <select id="create_product_variant_id" name="product_variant_id"
                                class="form-select select2 @error('product_variant_id') is-invalid @enderror">
                                <option value="">Select Variant</option>
                                @foreach ($productVariants as $variant)
                                    <option value="{{ $variant->id }}" {{ old('product_variant_id') == $variant->id ? 'selected' : '' }}>
                                        {{ $variant->product->product_name ?? '-' }} - {{ $variant->variant_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_variant_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="create_current_stock">Current Stock</label>
                            <input type="number" step="0.01" id="create_current_stock" name="current_stock"
                                value="{{ old('current_stock', 0) }}"
                                class="form-control @error('current_stock') is-invalid @enderror"
                                placeholder="0.00">
                            @error('current_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Stock</button>
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
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="edit_product_variant_id">Product Variant</label>
                            <select id="edit_product_variant_id" name="product_variant_id"
                                class="form-select select2 @error('product_variant_id') is-invalid @enderror">
                                <option value="">Select Variant</option>
                                @foreach ($productVariants as $variant)
                                    <option value="{{ $variant->id }}">
                                        {{ $variant->product->product_name ?? '-' }} - {{ $variant->variant_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_variant_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="edit_current_stock">Current Stock</label>
                            <input type="number" step="0.01" id="edit_current_stock" name="current_stock"
                                class="form-control @error('current_stock') is-invalid @enderror"
                                placeholder="0.00">
                            @error('current_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-dark"><i class="fas fa-save me-1"></i> Update Stock</button>
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
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Delete Stock</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center py-4">
                    <div class="mb-3"><i class="fas fa-trash-alt text-danger" style="font-size: 4rem;"></i></div>
                    <h5 class="mb-3">Are you sure?</h5>
                    <p class="text-muted mb-0">You are about to delete stock for <strong id="delete_stock_name"></strong>.</p>
                    <p class="text-danger small mt-2 mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-2"></i> Delete Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif


@endsection