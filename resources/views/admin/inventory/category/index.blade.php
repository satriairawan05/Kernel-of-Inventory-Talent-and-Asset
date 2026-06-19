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

@section('content')
    <div class="container-fluid py-4 page-shell">
        <section class="page-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <p class="eyebrow mb-2">Inventory</p>
                <h2 class="mb-1">Category Management</h2>
            </div>
            @if ($access['Create'] == 1)
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="fas fa-plus me-1"></i> Add New
                </button>
            @endif
        </section>

        @if ($access['Read'] == 1)
            <div class="card soft-card mt-4">
                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div>
                        <h4 class="mb-1">List of Category</h4>
                    </div>
                    <span class="stat-chip bg-primary-subtle text-primary"><i class="fas fa-tags me-1"></i>
                        {{ $categories->count() }} categories</span>
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
                                    <th scope="col">Company</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Handle</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <th scope="row">{{ $categories->firstItem() + $loop->index }}</th>
                                        <td>{{ $category->company->company_name ?? '-' }}</td>
                                        <td>{{ $category->category_name }}</td>
                                        <td>{{ Str::limit($category->description, 30) ?? '-' }}</td>
                                        <td class="action-buttons d-md-flex flex-md-row align-items-md-center gap-2">
                                            @if ($access['Update'] == 1)
                                                <button type="button" class="btn btn-sm btn-primary btn-edit"
                                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                                    data-id="{{ $category->id }}" data-name="{{ $category->category_name }}"
                                                    data-company-id="{{ $category->company_id }}"
                                                    data-description="{{ $category->description }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                            @endif
                                            @if ($access['Delete'] == 1)
                                                <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                    data-id="{{ $category->id }}"
                                                    data-name="{{ $category->category_name }}">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No Data Found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3 pagination-wrapper">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if ($access['Create'] == 1)
        <!-- ==================== MODAL CREATE ==================== -->
        <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add New Category</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('inventory.category.store') }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="create_company_id">Company</label>
                                    <select id="create_company_id" name="company_id"
                                        class="form-select select2 @error('company_id') is-invalid @enderror">
                                        <option value="">Select Company</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}"
                                                {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                                {{ $company->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="form-label" for="create_category_name">Category Name</label>
                                    <input type="text" id="create_category_name" name="category_name"
                                        value="{{ old('category_name') }}"
                                        class="form-control @error('category_name') is-invalid @enderror"
                                        placeholder="Example: Electronics, Furniture, etc.">
                                    @error('category_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="create_description">Description</label>
                                <textarea id="create_description" name="description" rows="4"
                                    class="form-control @error('description') is-invalid @enderror"
                                    placeholder="Example: Category for IT equipment and assets.">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Add
                                Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    @if ($access['Update'] == 1)
        <!-- ==================== MODAL EDIT ==================== -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="edit_company_id">Company</label>
                                    <select id="edit_company_id" name="company_id"
                                        class="form-select select2 @error('company_id') is-invalid @enderror">
                                        <option value="">Select Company</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="form-label" for="edit_category_name">Category Name</label>
                                    <input type="text" id="edit_category_name" name="category_name" value=""
                                        class="form-control @error('category_name') is-invalid @enderror"
                                        placeholder="Example: Electronics, Furniture, etc.">
                                    @error('category_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="edit_description">Description</label>
                                <textarea id="edit_description" name="description" rows="4"
                                    class="form-control @error('description') is-invalid @enderror"
                                    placeholder="Example: Category for IT equipment and assets."></textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning text-dark"><i class="fas fa-save me-1"></i>
                                Update Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    @if ($access['Delete'] == 1)
        <!-- ==================== MODAL DELETE ==================== -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Delete Category</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-body text-center py-4">
                            <div class="mb-3"><i class="fas fa-trash-alt text-danger" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="mb-3">Are you sure?</h5>
                            <p class="text-muted mb-0">You are about to delete <strong id="deleteCategoryName"></strong>.
                            </p>
                            <p class="text-danger small mt-2 mb-0">This action cannot be undone.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-2"></i> Delete
                                Category</button>
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
            // ===== EDIT MODAL =====
            const editModal = document.getElementById('editModal');
            const editForm = document.getElementById('editForm');
            const editCompany = document.getElementById('edit_company_id');
            const editName = document.getElementById('edit_category_name');
            const editDesc = document.getElementById('edit_description');

            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const companyId = button.getAttribute('data-company-id');
                const description = button.getAttribute('data-description');

                // Set action URL
                const url = "{{ route('inventory.category.update', ':id') }}".replace(':id', id);
                editForm.action = url;

                // Set values
                editCompany.value = companyId;
                editName.value = name;
                editDesc.value = description;
            });

            // ===== DELETE MODAL =====
            const deleteModal = document.getElementById('deleteModal');
            const deleteForm = document.getElementById('deleteForm');
            const deleteName = document.getElementById('deleteCategoryName');

            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');

                // Set action URL
                const url = "{{ route('inventory.category.destroy', ':id') }}".replace(':id', id);
                deleteForm.action = url;
                deleteName.textContent = name;
            });
        });
    </script>
@endpush
