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
        .menu-image-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }
        .menu-initials {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: #e5e7eb;
            color: #6b7280;
            font-weight: 700;
            font-size: 1rem;
        }
        .image-preview-container {
            position: relative;
            display: inline-block;
        }
        .image-preview-container img {
            max-height: 120px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        .image-preview-container .remove-image {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #ef4444;
            color: #fff;
            border: none;
            font-size: 14px;
            line-height: 24px;
            text-align: center;
            cursor: pointer;
        }
        .input-group-rupiah .input-group-text {
            background: #eef2f6;
            font-weight: 600;
            color: #1e293b;
        }
        .input-group-rupiah .form-control {
            border-left: none;
        }
        .form-check-label {
            font-weight: 500;
        }
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
    @if(!isset($jquery_loaded))
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @endif

    <script type="text/javascript">
        $(document).ready(function() {

            function formatRupiah(angka) {
                if (!angka) return '';
                var clean = angka.replace(/\D/g, '');
                if (clean === '') return '';
                var number = parseInt(clean, 10);
                if (isNaN(number)) return '';
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function parseRupiah(value) {
                if (!value) return 0;
                var clean = value.replace(/\D/g, '');
                return parseInt(clean, 10) || 0;
            }

            $(document).on('input', '.price-rupiah', function() {
                var val = $(this).val();
                var formatted = formatRupiah(val);
                $(this).val(formatted);
            });

            function initSelect2(modalId) {
                $(modalId + ' .select2').select2({
                    theme: 'default',
                    width: '100%',
                    dropdownParent: $(modalId),
                    placeholder: 'Select an option',
                    allowClear: false
                });
            }

            $('#createModal').on('shown.bs.modal', function() {
                initSelect2('#createModal');
            });

            // CREATE modal - image preview
            $('#create_image').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        $('#create_image_preview').attr('src', event.target.result).show();
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#create_image_preview').hide();
                }
            });

            // EDIT modal
            $('#editModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');
                const name = button.data('name');
                const price = button.data('price');
                const category = button.data('category');
                const status = button.data('status');
                const image = button.data('image');
                const variantId = button.data('product-variant-id') || '';

                const url = "{{ route('pos.menu.update', ':id') }}".replace(':id', id);
                $('#editForm').attr('action', url);

                $('#edit_id').val(id);
                $('#edit_name').val(name);
                $('#edit_price').val(formatRupiah(price.toString()));
                $('#edit_category').val(category || 'food').trigger('change');
                $('#edit_status').val(status || 'available').trigger('change');

                // Set selected variant
                if (variantId) {
                    $('#edit_product_variant_id').val(variantId).trigger('change');
                } else {
                    $('#edit_product_variant_id').val('').trigger('change');
                }

                if (image) {
                    $('#edit_current_image').attr('src', image).show();
                } else {
                    $('#edit_current_image').hide();
                }

                $('#edit_image_preview').hide();
                $('#edit_image').val('');
            });

            $('#editModal').on('shown.bs.modal', function() {
                initSelect2('#editModal');
            });

            $('#edit_image').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        $('#edit_image_preview').attr('src', event.target.result).show();
                        $('#edit_current_image').hide();
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#edit_image_preview').hide();
                    $('#edit_current_image').show();
                }
            });

            // DELETE modal
            $('#deleteModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');
                const name = button.data('name');

                const url = "{{ route('pos.menu.destroy', ':id') }}".replace(':id', id);
                $('#deleteForm').attr('action', url);
                $('#delete_menu_name').text(name);
            });

            // Submit - bersihkan format rupiah
            $('#createForm').on('submit', function(e) {
                var priceVal = $('#create_price').val();
                if (priceVal) {
                    var cleanPrice = priceVal.replace(/\D/g, '');
                    $('#create_price').val(cleanPrice);
                }
            });

            $('#editForm').on('submit', function(e) {
                var priceVal = $('#edit_price').val();
                if (priceVal) {
                    var cleanPrice = priceVal.replace(/\D/g, '');
                    $('#edit_price').val(cleanPrice);
                }
            });

            console.log('✅ jQuery & Select2 siap digunakan!');
        });
    </script>
@endpush

@section('content')
<div class="container-fluid py-4 page-shell">
    <section class="page-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <p class="eyebrow mb-2">Menu</p>
            <h2 class="mb-1">Menu Items Management</h2>
            <p class="mb-0">Manage all menu items for your outlet.</p>
        </div>
        @if ($access['Create'] == 1)
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="fas fa-plus me-1"></i> Add New Menu
            </button>
        @endif
    </section>

    @if ($access['Read'] == 1)
    <div class="card soft-card mt-4">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h4 class="mb-1">Menu List</h4>
                <p class="text-muted mb-0">All menu items for your outlet.</p>
            </div>
            <span class="stat-chip bg-primary-subtle text-primary"><i class="fas fa-utensils me-1"></i>
                {{ $menuItems->total() }} total menu items</span>
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
                            <th scope="col">Image</th>
                            <th scope="col">Name</th>
                            <th scope="col">Variant</th>
                            <th scope="col">Price</th>
                            <th scope="col">Category</th>
                            <th scope="col">Status</th>
                            <th scope="col">Stock</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menuItems as $item)
                            <tr>
                                <th scope="row">{{ $menuItems->firstItem() + $loop->index }}</th>
                                <td>
                                    @if($item->image_url)
                                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="menu-image-thumb">
                                    @else
                                        <div class="menu-initials">{{ $item->initials }}</div>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $item->name }}</td>
                                <td>
                                    @if($item->productVariant)
                                        <span class="badge bg-info">{{ $item->productVariant->variant_name }}</span>
                                    @else
                                        <span class="badge bg-secondary">No variant</span>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td><span class="badge bg-secondary">{{ $item->category_label }}</span></td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'available' => 'success',
                                            'low' => 'warning',
                                            'out' => 'danger',
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$item->status] ?? 'secondary' }}">
                                        {{ $item->status_label }}
                                    </span>
                                </td>
                                <td class="{{ $item->current_stock <= 0 ? 'text-danger fw-bold' : ($item->current_stock <= 5 ? 'text-warning fw-bold' : '') }}">
                                    {{ number_format($item->current_stock, 0, ',', '.') }}
                                </td>
                                <td class="action-buttons d-md-flex flex-md-row align-items-md-center gap-2">
                                    @if ($access['Update'] == 1)
                                        <button type="button" class="btn btn-sm btn-warning btn-edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal"
                                            data-id="{{ $item->id }}"
                                            data-name="{{ $item->name }}"
                                            data-price="{{ $item->price }}"
                                            data-category="{{ $item->category }}"
                                            data-status="{{ $item->status }}"
                                            data-image="{{ $item->image_url }}"
                                            data-product-variant-id="{{ $item->product_variant_id }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    @endif
                                    @if ($access['Delete'] == 1)
                                        <button type="button" class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            data-id="{{ $item->id }}"
                                            data-name="{{ $item->name }}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">No menu items found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3 pagination-wrapper">
                {{ $menuItems->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

@if ($access['Create'] == 1)
<!-- MODAL CREATE -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add New Menu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="createForm" action="{{ route('pos.menu.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8 mb-3">
                            <label class="form-label" for="create_name">Menu Name <span class="text-danger">*</span></label>
                            <input type="text" id="create_name" name="name"
                                value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Ayam Geprek">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="create_price">Price (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-rupiah">
                                <span class="input-group-text">Rp</span>
                                <input type="text" id="create_price" name="price"
                                    value="{{ old('price') ? number_format(old('price'), 0, ',', '.') : '' }}"
                                    class="form-control price-rupiah @error('price') is-invalid @enderror"
                                    placeholder="Contoh: 25.000">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="create_category">Category</label>
                            <select id="create_category" name="category"
                                class="form-select select2 @error('category') is-invalid @enderror">
                                <option value="">Select Category</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="create_status">Status</label>
                            <select id="create_status" name="status"
                                class="form-select select2 @error('status') is-invalid @enderror">
                                <option value="">Select Status</option>
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}" {{ old('status') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="create_product_variant_id">Product Variant (Stok)</label>
                            <select id="create_product_variant_id" name="product_variant_id"
                                class="form-select select2 @error('product_variant_id') is-invalid @enderror">
                                <option value="">No variant (no stock)</option>
                                @foreach($productVariants as $variant)
                                    <option value="{{ $variant['id'] }}" {{ old('product_variant_id') == $variant['id'] ? 'selected' : '' }}>
                                        {{ $variant['name'] }} (Stok: {{ number_format($variant['stock'], 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('product_variant_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="create_image">Image</label>
                        <input type="file" id="create_image" name="image"
                            class="form-control @error('image') is-invalid @enderror"
                            accept="image/*">
                        <div class="mt-2 image-preview-container">
                            <img id="create_image_preview" style="display:none; max-height:120px; border-radius:8px; border:1px solid #e5e7eb;">
                        </div>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Menu</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if ($access['Update'] == 1)
<!-- MODAL EDIT -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8 mb-3">
                            <label class="form-label" for="edit_name">Menu Name <span class="text-danger">*</span></label>
                            <input type="text" id="edit_name" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Ayam Geprek">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="edit_price">Price (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-rupiah">
                                <span class="input-group-text">Rp</span>
                                <input type="text" id="edit_price" name="price"
                                    class="form-control price-rupiah @error('price') is-invalid @enderror"
                                    placeholder="Contoh: 25.000">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="edit_category">Category</label>
                            <select id="edit_category" name="category"
                                class="form-select select2 @error('category') is-invalid @enderror">
                                <option value="">Select Category</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="edit_status">Status</label>
                            <select id="edit_status" name="status"
                                class="form-select select2 @error('status') is-invalid @enderror">
                                <option value="">Select Status</option>
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="edit_product_variant_id">Product Variant (Stok)</label>
                            <select id="edit_product_variant_id" name="product_variant_id"
                                class="form-select select2 @error('product_variant_id') is-invalid @enderror">
                                <option value="">No variant (no stock)</option>
                                @foreach($productVariants as $variant)
                                    <option value="{{ $variant['id'] }}">
                                        {{ $variant['name'] }} (Stok: {{ number_format($variant['stock'], 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('product_variant_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="edit_image">Image</label>
                        <input type="file" id="edit_image" name="image"
                            class="form-control @error('image') is-invalid @enderror"
                            accept="image/*">
                        <div class="mt-2 image-preview-container">
                            <img id="edit_current_image" style="display:none; max-height:120px; border-radius:8px; border:1px solid #e5e7eb;">
                            <img id="edit_image_preview" style="display:none; max-height:120px; border-radius:8px; border:1px solid #e5e7eb;">
                        </div>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-dark"><i class="fas fa-save me-1"></i> Update Menu</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if ($access['Delete'] == 1)
<!-- MODAL DELETE -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Delete Menu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center py-4">
                    <div class="mb-3"><i class="fas fa-trash-alt text-danger" style="font-size: 4rem;"></i></div>
                    <h5 class="mb-3">Are you sure?</h5>
                    <p class="text-muted mb-0">You are about to delete menu <strong id="delete_menu_name"></strong>.</p>
                    <p class="text-danger small mt-2 mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-2"></i> Delete Menu</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection