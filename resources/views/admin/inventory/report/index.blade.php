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
        .filter-form .form-control, .filter-form .form-select {
            border-radius: 999px;
            background: rgba(255,255,255,0.9);
            border: 1px solid rgba(255,255,255,0.3);
            color: #1f2937;
        }
        .filter-form .btn {
            border-radius: 999px;
        }
        @media (max-width: 768px) {
            .page-hero { padding: 18px; }
            .page-hero .btn { width: 100%; }
            .action-buttons { display:flex; flex-direction:column; gap:.45rem; min-width:120px; }
            .action-buttons .btn { width:100%; }
            .filter-form .d-flex { flex-direction: column; }
        }
    </style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto submit filter on change
            document.getElementById('filterType').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
            document.getElementById('filterDate').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            // Delete modal
            const deleteModal = document.getElementById('deleteModal');
            const deleteForm = document.getElementById('deleteForm');
            const deleteName = document.getElementById('delete_report_name');

            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');

                const url = "{{ route('inventory.report.destroy', ':id') }}".replace(':id', id);
                deleteForm.action = url;
                deleteName.textContent = name;
            });

            // Print report via POST
            window.printReport = function(id) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('inventory.report.print', ':id') }}".replace(':id', id);
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);
                // Tambahkan connection_type dan target dari prompt sederhana
                const connType = prompt('Tipe koneksi (windows/network/file):', 'windows');
                if (!connType) return;
                const target = prompt('Target printer (nama/IP:port/path):', 'EPSON TM-T20');
                if (!target) return;
                const ct = document.createElement('input');
                ct.type = 'hidden';
                ct.name = 'connection_type';
                ct.value = connType;
                form.appendChild(ct);
                const trg = document.createElement('input');
                trg.type = 'hidden';
                trg.name = 'target';
                trg.value = target;
                form.appendChild(trg);
                document.body.appendChild(form);
                form.submit();
            };

            window.printAggregated = function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('inventory.report.print-aggregated') }}";
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);
                const typeInput = document.createElement('input');
                typeInput.type = 'hidden';
                typeInput.name = 'type';
                typeInput.value = '{{ $type }}';
                form.appendChild(typeInput);
                const dateInput = document.createElement('input');
                dateInput.type = 'hidden';
                dateInput.name = 'date';
                dateInput.value = '{{ $date ?? '' }}';
                form.appendChild(dateInput);
                const connType = prompt('Tipe koneksi (windows/network/file):', 'windows');
                if (!connType) return;
                const target = prompt('Target printer:', 'EPSON TM-T20');
                if (!target) return;
                const ct = document.createElement('input');
                ct.type = 'hidden';
                ct.name = 'connection_type';
                ct.value = connType;
                form.appendChild(ct);
                const trg = document.createElement('input');
                trg.type = 'hidden';
                trg.name = 'target';
                trg.value = target;
                form.appendChild(trg);
                document.body.appendChild(form);
                form.submit();
            };
        });
    </script>
@endpush

@section('content')
<div class="container-fluid py-4 page-shell">
    <section class="page-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <p class="eyebrow mb-2">📊 Reports</p>
            <h2 class="mb-1">
                @if($type == 'daily') Daily Report
                @elseif($type == 'weekly') Weekly Report
                @else Monthly Report @endif
            </h2>
            <p class="mb-0">
                Periode: <strong>{{ $date ? \Carbon\Carbon::parse($date)->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</strong>
                @if($type == 'weekly') (Minggu ke-{{ \Carbon\Carbon::parse($date)->weekOfYear }})
                @elseif($type == 'monthly') ({{ \Carbon\Carbon::parse($date)->translatedFormat('F Y') }})
                @endif
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <form id="filterForm" action="{{ route('inventory.report.index') }}" method="GET" class="d-flex flex-wrap gap-2 filter-form">
                <select name="type" id="filterType" class="form-select form-select-sm" style="width:auto;">
                    <option value="daily" {{ $type == 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ $type == 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ $type == 'monthly' ? 'selected' : '' }}>Monthly</option>
                </select>
                <input type="date" name="date" id="filterDate" class="form-control form-control-sm" value="{{ $date ?? now()->toDateString() }}" style="width:auto;">
                <button type="submit" class="btn btn-sm btn-light"><i class="fas fa-sync-alt"></i> Show</button>
            </form>
            <a href="{{ route('inventory.report.generate-form') }}" class="btn btn-sm btn-success">
                <i class="fas fa-plus me-1"></i> Generate Daily
            </a>
            @if($type != 'daily' && !empty($data))
                <button type="button" class="btn btn-sm btn-primary" onclick="printAggregated()">
                    <i class="fas fa-print me-1"></i> Print Summary
                </button>
            @endif
        </div>
    </section>

    <div class="card soft-card mt-4">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h4 class="mb-1">
                    @if($type == 'daily') List of daily reports
                    @elseif($type == 'weekly') Weekly Recap
                    @else Monthly Recap @endif
                </h4>
                <p class="text-muted mb-0">
                    {{ $type == 'daily' ? 'Each report per shift' : 'Aggregation of daily reports' }}
                </p>
            </div>
            <span class="stat-chip bg-primary-subtle text-primary">
                <i class="fas fa-boxes me-1"></i>
                {{ $type == 'daily' ? $reports->count() : count($data) }} record
            </span>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('failed'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('failed') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive mb-3">
                @if($type == 'daily')
                    <!-- ========== DAILY ========== -->
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Shift</th>
                                <th>Location</th>
                                <th>Cashier</th>
                                <th>Total Sold</th>
                                <th>Handle</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $report)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('d/m/Y') }}</td>
                                    <td>{{ $report->period->shift->name ?? '-' }}</td>
                                    <td>{{ $report->location }}</td>
                                    <td>{{ $report->cashier_name }}</td>
                                    <td class="fw-bold">{{ number_format($report->total_products_sold, 0) }}</td>
                                    <td class="action-buttons d-md-flex flex-md-row align-items-md-center gap-2">
                                        <a href="{{ route('inventory.report.preview', $report->id) }}" target="_blank" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Preview
                                        </a>
                                        <button type="button" class="btn btn-sm btn-success" onclick="printReport({{ $report->id }})">
                                            <i class="fas fa-print"></i> Print
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-id="{{ $report->id }}"
                                            data-name="Laporan {{ $report->report_date }} - {{ $report->period->shift->name ?? '' }}">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center py-4 text-muted">Data not found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{-- <div class="d-flex justify-content-between align-items-center mt-3">
                        {{ $reports->links() ?? '' }}
                    </div> --}}
                @else
                    <!-- ========== WEEKLY / MONTHLY ========== -->
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>First Stock</th>
                                <th>Stock In</th>
                                <th>Selling</th>
                                <th>Remain</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $item)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $item['product_variant']->variant_name ?? '-' }}</td>
                                    <td>{{ number_format($item['first_stock'], 0) }}</td>
                                    <td>{{ number_format($item['stock_in'], 0) }}</td>
                                    <td class="fw-bold">{{ number_format($item['selling'], 0) }}</td>
                                    <td class="fw-bold {{ $item['remain'] <= 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($item['remain'], 0) }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-4 text-muted">Data not found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Delete (hanya untuk daily) -->
@if($type == 'daily')
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Delete Report</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteForm" method="POST">
                @csrf @method('DELETE')
                <div class="modal-body text-center py-4">
                    <div class="mb-3"><i class="fas fa-trash-alt text-danger" style="font-size: 4rem;"></i></div>
                    <h5 class="mb-3">Are you sure?</h5>
                    <p class="text-muted mb-0">You want delete <strong id="delete_report_name"></strong>.</p>
                    <p class="text-danger small mt-2 mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-2"></i> Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection