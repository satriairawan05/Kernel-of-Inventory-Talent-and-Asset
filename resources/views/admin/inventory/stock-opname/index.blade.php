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
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
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

        .action-buttons {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .badge-diff-plus {
            background: #d4edda;
            color: #155724;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        .badge-diff-minus {
            background: #f8d7da;
            color: #721c24;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        .badge-diff-zero {
            background: #e2e3e5;
            color: #383d41;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .page-hero {
                padding: 18px;
            }

            .page-hero .btn {
                width: 100%;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4 page-shell">
        <!-- Hero -->
        <section class="page-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <p class="eyebrow mb-2">Inventory</p>
                <h2 class="mb-1">Stock Opname</h2>
                <p class="mb-0">Pencocokan stok fisik dengan sistem.</p>
            </div>
            @if ($access['Create'] == 1)
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="fas fa-plus me-1"></i> Tambah Opname
                </button>
            @endif
        </section>

        <!-- Card Tabel -->
        <div class="card soft-card mt-4">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <h4 class="mb-1">List Opname</h4>
                    <p class="text-muted mb-0">Riwayat opname stok.</p>
                </div>
                <span class="stat-chip bg-primary-subtle text-primary">
                    <i class="fas fa-boxes me-1"></i> {{ $opnames->total() }} records
                </span>
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
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Produk / Varian</th>
                                <th>System Stock</th>
                                <th>Physical Stock</th>
                                <th>Difference</th>
                                <th>Catatan</th>
                                <th>Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($opnames as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $item->productVariant->product->product_name ?? '-' }}</strong>
                                        <div class="small text-muted">{{ $item->productVariant->variant_name ?? '-' }}</div>
                                    </td>
                                    <td>{{ number_format($item->system_stock, 2, ',', '.') }}</td>
                                    <td>{{ number_format($item->physical_stock, 2, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $diff = $item->difference;
                                            $badgeClass =
                                                $diff > 0
                                                    ? 'badge-diff-plus'
                                                    : ($diff < 0
                                                        ? 'badge-diff-minus'
                                                        : 'badge-diff-zero');
                                            $sign = $diff > 0 ? '+' : '';
                                        @endphp
                                        <span class="badge {{ $badgeClass }} px-3 py-2">
                                            {{ $sign }}{{ number_format($diff, 2, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>{{ Str::limit($item->notes, 50) ?? '-' }}</td>
                                    <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            @if ($access['Update'] == 1)
                                                <!-- Tombol Edit -->
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                    data-bs-target="#editModal" data-id="{{ $item->id }}"
                                                    data-product-variant-id="{{ $item->product_variant_id }}"
                                                    data-physical-stock="{{ $item->physical_stock }}"
                                                    data-notes="{{ $item->notes ?? '' }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @endif
                                            @if ($access['Delete'] == 1)
                                                <!-- Tombol Delete -->
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal" data-id="{{ $item->id }}"
                                                    data-name="{{ ($item->productVariant->product->product_name ?? 'produk') . ' - ' . ($item->productVariant->variant_name ?? 'varian') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Belum ada data opname.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex justify-content-center">
                    {{ $opnames->links() }}
                </div>
            </div>
        </div>
    </div>

    @if ($access['Create'] == 1)
        <!-- ======================== MODAL CREATE ======================== -->
        <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Tambah Opname</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('inventory.stock-opname.store') }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold" for="create_product_variant_id">Product Variant <span
                                            class="text-danger">*</span></label>
                                    <select id="create_product_variant_id" name="product_variant_id" class="form-select select2"
                                        required>
                                        <option value="">-- Pilih Varian --</option>
                                        @foreach ($productVariants as $variant)
                                            <option value="{{ $variant->id }}"
                                                data-system-stock="{{ $variant->stock->current_stock ?? 0 }}">
                                                {{ $variant->product->product_name ?? '-' }} -
                                                {{ $variant->variant_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">System Stock</label>
                                    <input type="text" id="create_system_stock" class="form-control" readonly>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold" for="create_physical_stock">Physical Stock <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.01" id="create_physical_stock" name="physical_stock"
                                        class="form-control" required placeholder="0.00">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold" for="create_notes">Catatan</label>
                                    <textarea id="create_notes" name="notes" rows="3" class="form-control" placeholder="Catatan opname..."></textarea>
                                </div>
                                <div class="col-12">
                                    <div class="alert alert-info" id="create_diff_info">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Selisih: <strong id="create_diff_display">0.00</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan
                                Opname</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if ($access['Update'] == 1)
        <!-- ======================== MODAL EDIT ======================== -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Opname</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold" for="edit_product_variant_id">Product Variant <span
                                            class="text-danger">*</span></label>
                                    <select id="edit_product_variant_id" name="product_variant_id" class="form-select select2"
                                        required>
                                        <option value="">-- Pilih Varian --</option>
                                        @foreach ($productVariants as $variant)
                                            <option value="{{ $variant->id }}"
                                                data-system-stock="{{ $variant->stock->current_stock ?? 0 }}">
                                                {{ $variant->product->product_name ?? '-' }} -
                                                {{ $variant->variant_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">System Stock</label>
                                    <input type="text" id="edit_system_stock" class="form-control" readonly>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold" for="edit_physical_stock">Physical Stock <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.01" id="edit_physical_stock" name="physical_stock"
                                        class="form-control" required placeholder="0.00">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold" for="edit_notes">Catatan</label>
                                    <textarea id="edit_notes" name="notes" rows="3" class="form-control" placeholder="Catatan opname..."></textarea>
                                </div>
                                <div class="col-12">
                                    <div class="alert alert-info" id="edit_diff_info">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Selisih: <strong id="edit_diff_display">0.00</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning text-dark"><i class="fas fa-save me-1"></i>
                                Update Opname</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if ($access['Delete'] == 1)
        <!-- ======================== MODAL DELETE ======================== -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Hapus Opname</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-body text-center py-4">
                            <p>Yakin ingin menghapus opname untuk <strong id="delete_item_name"></strong>?</p>
                            <p class="text-muted small">Stok akan dikembalikan ke sebelum opname.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ===== CREATE MODAL =====
            const createVariant = document.getElementById('create_product_variant_id');
            const createSystem = document.getElementById('create_system_stock');
            const createPhysical = document.getElementById('create_physical_stock');
            const createDiff = document.getElementById('create_diff_display');

            function updateCreateDiff() {
                const system = parseFloat(createSystem.value) || 0;
                const physical = parseFloat(createPhysical.value) || 0;
                const diff = physical - system;
                createDiff.textContent = (diff > 0 ? '+' : '') + diff.toFixed(2);
            }

            createVariant.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const system = selected.dataset.systemStock || 0;
                createSystem.value = parseFloat(system).toFixed(2);
                updateCreateDiff();
            });

            createPhysical.addEventListener('input', updateCreateDiff);

            // Trigger initial
            if (createVariant.value) {
                createVariant.dispatchEvent(new Event('change'));
            }

            // ===== EDIT MODAL =====
            const editModal = document.getElementById('editModal');
            const editVariant = document.getElementById('edit_product_variant_id');
            const editSystem = document.getElementById('edit_system_stock');
            const editPhysical = document.getElementById('edit_physical_stock');
            const editNotes = document.getElementById('edit_notes');
            const editDiff = document.getElementById('edit_diff_display');
            const editForm = document.getElementById('editForm');

            function updateEditDiff() {
                const system = parseFloat(editSystem.value) || 0;
                const physical = parseFloat(editPhysical.value) || 0;
                const diff = physical - system;
                editDiff.textContent = (diff > 0 ? '+' : '') + diff.toFixed(2);
            }

            editVariant.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const system = selected.dataset.systemStock || 0;
                editSystem.value = parseFloat(system).toFixed(2);
                updateEditDiff();
            });

            editPhysical.addEventListener('input', updateEditDiff);

            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const variantId = button.getAttribute('data-product-variant-id');
                const physicalStock = button.getAttribute('data-physical-stock');
                const notes = button.getAttribute('data-notes') || '';

                // Set action URL
                const baseUrl = "{{ route('inventory.stock-opname.update', ':id') }}".replace(':id', id);
                editForm.action = baseUrl;

                // Set values
                editVariant.value = variantId;
                editVariant.dispatchEvent(new Event('change'));

                editPhysical.value = physicalStock;
                editNotes.value = notes;
                updateEditDiff();
            });

            // ===== DELETE MODAL =====
            const deleteModal = document.getElementById('deleteModal');
            const deleteForm = document.getElementById('deleteForm');
            const deleteName = document.getElementById('delete_item_name');

            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');

                // Set action URL
                const baseUrl = "{{ route('inventory.stock-opname.destroy', ':id') }}".replace(':id', id);
                deleteForm.action = baseUrl;
                deleteName.textContent = name;
            });
        });
    </script>
@endpush
