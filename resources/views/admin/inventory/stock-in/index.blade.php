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

        .stat-chip {
            border-radius: 999px;
            padding: 6px 10px;
            font-weight: 600;
            font-size: .82rem;
        }

        .table thead th {
            background: #eef4ff;
            color: #334155;
            font-weight: 700;
        }

        .table tbody tr:hover {
            background: #f8fbff;
        }

        .action-buttons .btn-sm {
            font-size: 0.75rem;
            padding: 0.2rem 0.5rem;
        }

        @media (max-width: 768px) {
            .page-hero {
                padding: 18px;
            }

            .page-hero .btn {
                width: 100%;
            }

            .action-buttons {
                display: flex;
                flex-direction: column;
                gap: .45rem;
                min-width: 120px;
            }

            .action-buttons .btn {
                width: 100%;
            }

            .pagination-wrapper {
                flex-direction: column;
                gap: .75rem;
                align-items: stretch;
            }

            .pagination-wrapper>div {
                width: 100%;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ========== EDIT MODAL ==========
            const editModal = document.getElementById('editModal');
            const editForm = document.getElementById('editForm');
            const editVariant = document.getElementById('edit_product_variant_id');
            const editQty = document.getElementById('edit_qty');
            const editReceiver = document.getElementById('edit_receiver_sender');
            const editMovementType = document.getElementById('edit_movement_type');
            const editNotes = document.getElementById('edit_notes');

            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const variantId = button.getAttribute('data-variant-id');
                const qty = button.getAttribute('data-qty');
                const receiver = button.getAttribute('data-receiver');
                const movementType = button.getAttribute('data-movement-type');
                const notes = button.getAttribute('data-notes') || '';

                const url = "{{ route('inventory.stock-in.update', ':id') }}".replace(':id', id);
                editForm.action = url;

                editVariant.value = variantId;
                editQty.value = qty;
                editReceiver.value = receiver;
                editMovementType.value = movementType;
                editNotes.value = notes;
            });

            // ========== DELETE MODAL ==========
            const deleteModal = document.getElementById('deleteModal');
            const deleteForm = document.getElementById('deleteForm');
            const deleteName = document.getElementById('delete_stock_in_name');

            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');

                const url = "{{ route('inventory.stock-in.destroy', ':id') }}".replace(':id', id);
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
                <h2 class="mb-1">Stock In</h2>
                <p class="mb-0">List of incoming stock transactions (purchase & opening).</p>
            </div>
            @if ($access['Create'] == 1)
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="fas fa-plus me-1"></i> Add Stock In
                </button>
            @endif
        </section>

        <div class="card soft-card mt-4">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <h4 class="mb-1">Stock In List</h4>
                    <p class="text-muted mb-0">All stock addition transactions.</p>
                </div>
                <span class="stat-chip bg-primary-subtle text-primary"><i class="fas fa-boxes me-1"></i>
                    {{ $stockIns->total() }} records</span>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive mb-3">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Variant</th>
                                <th>Qty In</th>
                                <th>Type</th>
                                <th>Stock Before</th>
                                <th>Stock After</th>
                                <th>PIC</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stockIns as $item)
                                <tr>
                                    <td>{{ $stockIns->firstItem() + $loop->index }}</td>
                                    <td>
                                        <strong>{{ $item->productVariant->product->product_name ?? '-' }}</strong>
                                        <div class="small text-muted">
                                            {{ $item->productVariant->variant_name ?? 'No variant' }}</div>
                                    </td>
                                    <td class="text-success fw-bold">+{{ number_format($item->qty, 2, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $badgeClass = 'badge-info';
                                            if ($item->movement_type == 'opening') {
                                                $badgeClass = 'badge-success';
                                            } elseif ($item->movement_type == 'purcashe') {
                                                $badgeClass = 'badge-danger';
                                            } else {
                                                $badgeClass = 'badge-secondary';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $item->movement_type }}</span>
                                    </td>
                                    <td>{{ number_format($item->stock_before, 2, ',', '.') }}</td>
                                    <td>{{ number_format($item->stock_after, 2, ',', '.') }}</td>
                                    <td>{{ $item->user->name ?? '-' }}</td>
                                    <td class="text-nowrap">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="action-buttons d-md-flex flex-md-row align-items-md-center gap-2">
                                        @if ($access['Update'] == 1)
                                            <button type="button" class="btn btn-sm btn-warning btn-edit"
                                                data-bs-toggle="modal" data-bs-target="#editModal"
                                                data-id="{{ $item->id }}"
                                                data-variant-id="{{ $item->product_variant_id }}"
                                                data-qty="{{ $item->qty }}"
                                                data-receiver="{{ $item->receiver_sender ?? '' }}"
                                                data-movement-type="{{ $item->movement_type }}"
                                                data-notes="{{ $item->notes ?? '' }}">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                        @endif
                                        @if ($access['Delete'] == 1)
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal" data-id="{{ $item->id }}"
                                                data-name="{{ $item->productVariant->product->product_name ?? 'Product' }} - {{ $item->productVariant->variant_name ?? 'Variant' }}">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No stock in transactions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 pagination-wrapper">
                    {{ $stockIns->links() }}
                </div>
            </div>
        </div>
    </div>

    @if ($access['Create'] == 1)
        <!-- ============================================================== -->
        <!-- MODAL CREATE -->
        <!-- ============================================================== -->
        <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Stock In</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('inventory.stock-in.store') }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="create_product_variant_id">Product Variant</label>
                                    <select id="create_product_variant_id" name="product_variant_id"
                                        class="form-select select2 @error('product_variant_id') is-invalid @enderror">
                                        <option value="">Select Variant</option>
                                        @foreach ($productVariants as $variant)
                                            <option value="{{ $variant->id }}"
                                                {{ old('product_variant_id') == $variant->id ? 'selected' : '' }}>
                                                {{ $variant->product->product_name ?? '-' }} -
                                                {{ $variant->variant_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_variant_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="create_qty">Quantity In</label>
                                    <input type="number" step="0.01" id="create_qty" name="qty"
                                        value="{{ old('qty', 0) }}" class="form-control @error('qty') is-invalid @enderror"
                                        placeholder="0.00">
                                    @error('qty')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="create_receiver_sender">Receiver</label>
                                    <input type="text" id="create_receiver_sender" name="receiver_sender"
                                        value="{{ old('receiver_sender') }}"
                                        class="form-control @error('receiver_sender') is-invalid @enderror"
                                        placeholder="Receiver name">
                                    @error('receiver_sender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Select Movement Type -->
                            <div class="mb-3">
                                <label class="form-label" for="create_movement_type">Transaction Type</label>
                                <select id="create_movement_type" name="movement_type"
                                    class="form-select select2 @error('movement_type') is-invalid @enderror">
                                    <option value="">Select Type</option>
                                    @foreach ($movementTypes as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ old('movement_type') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('movement_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="create_notes">Notes</label>
                                <textarea id="create_notes" name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                                    placeholder="Additional notes (optional)">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Stock
                                In</button>
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
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Stock In</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="edit_product_variant_id">Product Variant</label>
                                    <select id="edit_product_variant_id" name="product_variant_id"
                                        class="form-select select2 @error('product_variant_id') is-invalid @enderror">
                                        <option value="">Select Variant</option>
                                        @foreach ($productVariants as $variant)
                                            <option value="{{ $variant->id }}">
                                                {{ $variant->product->product_name ?? '-' }} -
                                                {{ $variant->variant_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_variant_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="edit_qty">Quantity In</label>
                                    <input type="number" step="0.01" id="edit_qty" name="qty"
                                        class="form-control @error('qty') is-invalid @enderror" placeholder="0.00">
                                    @error('qty')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="edit_receiver_sender">Receiver</label>
                                    <input type="text" id="edit_receiver_sender" name="receiver_sender"
                                        class="form-control @error('receiver_sender') is-invalid @enderror"
                                        placeholder="Receiver name">
                                    @error('receiver_sender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Select Movement Type -->
                            <div class="mb-3">
                                <label class="form-label" for="edit_movement_type">Transaction Type</label>
                                <select id="edit_movement_type" name="movement_type"
                                    class="form-select select2 @error('movement_type') is-invalid @enderror">
                                    <option value="">Select Type</option>
                                    @foreach ($movementTypes as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('movement_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="edit_notes">Notes</label>
                                <textarea id="edit_notes" name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                                    placeholder="Additional notes (optional)"></textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning text-dark"><i class="fas fa-save me-1"></i>
                                Update Stock In</button>
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
                        <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Delete Transaction</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-body text-center py-4">
                            <div class="mb-3"><i class="fas fa-trash-alt text-danger" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="mb-3">Are you sure?</h5>
                            <p class="text-muted mb-0">You are about to delete the stock in transaction for <strong
                                    id="delete_stock_in_name"></strong>.</p>
                            <p class="text-danger small mt-2 mb-0">This action cannot be undone.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-2"></i>
                                Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
