@extends('admin.layouts.app')

@push('css')
<style>
    .page-shell { padding-top: 1rem; }
    .page-hero { border-radius: 28px; padding: 24px; background: linear-gradient(135deg, #111827 0%, #2563eb 45%, #22d3ee 100%); color: #fff; box-shadow: 0 18px 40px rgba(15,23,42,.18); }
    .page-hero .eyebrow { text-transform: uppercase; letter-spacing: .28em; font-size: .72rem; color: rgba(255,255,255,.82); }
    .soft-card { border-radius: 24px; border: 1px solid rgba(148,163,184,.18); box-shadow: 0 18px 40px rgba(15,23,42,.08); overflow: hidden; }
    .soft-card .card-header { border-bottom: 1px solid rgba(148,163,184,.18); background: linear-gradient(180deg, #fff 0%, #f8fbff 100%); }
    .stat-chip { border-radius: 999px; padding: 6px 10px; font-weight: 600; font-size: .82rem; }
    .table thead th { background: #eef4ff; color: #334155; font-weight: 700; }
    .table tbody tr:hover { background: #f8fbff; }
    @media (max-width: 768px) {
        .page-hero { padding: 18px; }
        .page-hero .btn { width: 100%; }
        .action-buttons { display:flex; flex-direction:column; gap:.45rem; min-width:120px; }
        .action-buttons .btn { width:100%; }
        .pagination-wrapper { flex-direction: column; gap: .75rem; align-items: stretch; }
        .pagination-wrapper > div { width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 page-shell">
    <section class="page-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <p class="eyebrow mb-2">Settings</p>
            <h2 class="mb-1">Unit Management</h2>
        </div>
        <a href="{{ route('setting.unit.create') }}" class="btn btn-success"><i class="fas fa-plus me-1"></i> Add New</a>
    </section>

    <div class="card soft-card mt-4">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h4 class="mb-1">Unit List</h4>
            </div>
            <span class="stat-chip bg-primary-subtle text-primary"><i class="fas fa-box me-1"></i> {{ $units->count() }} units</span>
        </div>
        <div class="card-body">
            <div class="table-responsive mb-3">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Unit Name</th>
                            <th scope="col">Unit Code</th>
                            <th scope="col">Description</th>
                            <th scope="col">Handle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($units as $unit)
                            <tr>
                                <th scope="row">{{ $units->firstItem() + $loop->index }}</th>
                                <td>{{ $unit->unit_name }}</td>
                                <td>{{ $unit->unit_code }}</td>
                                <td>{{ $unit->description }}</td>
                                <td class="action-buttons d-md-flex flex-md-row align-items-md-center gap-2">
                                    <a href="{{ route('setting.unit.edit', $unit) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Edit</a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUnitModal{{ $unit->id }}"><i class="fas fa-trash"></i> Delete</button>
                                    <div class="modal fade" id="deleteUnitModal{{ $unit->id }}" tabindex="-1" data-bs-backdrop="static" aria-labelledby="deleteUnitModalLabel{{ $unit->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-danger"><h5 class="modal-title text-white" id="deleteUnitModalLabel{{ $unit->id }}"><i class="fas fa-triangle-exclamation me-2"></i> Delete Unit</h5><button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times text-white"></i></button></div>
                                                <div class="modal-body text-center py-4">
                                                    <div class="mb-3"><i class="fas fa-trash-alt text-danger" style="font-size: 4rem;"></i></div>
                                                    <h5 class="mb-3">Are you sure?</h5>
                                                    <p class="text-muted mb-0">You are about to delete <strong>{{ $unit->unit_name }}</strong>.</p>
                                                    <p class="text-danger small mt-2 mb-0">This action cannot be undone.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('setting.unit.destroy', $unit) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-2"></i> Delete Unit</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
                {{ $units->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
