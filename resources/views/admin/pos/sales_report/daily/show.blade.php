@extends('admin.layouts.app')

@push('css')
    <style>
        .company-logo {
            max-width: 80px;
            max-height: 80px;
            object-fit: contain;
        }

        @media (max-width: 768px) {

            .card-header h4 {
                font-size: 1rem;
                margin-bottom: 0;
            }

            .card-body {
                padding: 1rem;
            }

            .btn-add-company {
                width: 100%;
                margin-bottom: 1rem;
            }

            .action-buttons {
                display: flex;
                flex-direction: column;
                gap: .5rem;
                min-width: 100px;
            }

            .action-buttons .btn {
                width: 100%;
            }

            .company-logo {
                max-width: 50px;
                max-height: 50px;
            }

            .pagination-wrapper {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .pagination-wrapper>div {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="container mt-4">
                        <div class="card">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0 text-white">Sales Report -
                                    {{ \Carbon\Carbon::parse($salesReport['report_date'])->format('d F Y') }} & Arrived at {{ \Carbon\Carbon::parse($salesReport['arrived_date'])->format('d F Y') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th width="50">No</th>
                                                <th>Company Name</th>
                                                <th class="text-end">Accessories (Rp)</th>
                                                <th class="text-end">Service (Rp)</th>
                                                <th class="text-end">Pulsa (Rp)</th>
                                                <th class="text-end">Total Amount (Rp)</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($salesReport['data'] as $index => $report)
                                                @php
                                                    $company = App\Models\Company::where(
                                                        'id',
                                                        $report['company_id'],
                                                    )->first();
                                                @endphp
                                                <tr>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td>{{ $company->company_name ?? '-' }}</td>
                                                    <td class="text-end">
                                                        {{ Carbon\Carbon::rupiah($report['accessories_amount'], 0, ',', '.') }}
                                                    </td>
                                                    <td class="text-end">
                                                        {{ Carbon\Carbon::rupiah($report['service_amount'], 0, ',', '.') }}
                                                    </td>
                                                    <td class="text-end">
                                                        {{ Carbon\Carbon::rupiah($report['pulsa_amount'], 0, ',', '.') }}
                                                    </td>
                                                    <td class="text-end text-primary fw-bold">
                                                        {{ Carbon\Carbon::rupiah($report['total_amount'], 0, ',', '.') }}
                                                    </td>
                                                    <td>{{ $report['notes'] ?? '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-4">
                                                        <em>Data Not Found</em>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot class="table-light fw-bold">
                                            <tr>
                                                <th colspan="3" class="text-end">TOTAL:</th>
                                                <th class="text-end">
                                                    {{ \Carbon\Carbon::rupiah($salesReport['summary']['total_accessories'], 0, ',', '.') }}
                                                </th>
                                                <th class="text-end">
                                                    {{ \Carbon\Carbon::rupiah($salesReport['summary']['total_service'], 0, ',', '.') }}
                                                </th>
                                                <th class="text-end">
                                                    {{ \Carbon\Carbon::rupiah($salesReport['summary']['total_pulsa'], 0, ',', '.') }}
                                                </th>
                                                <th class="text-end text-primary">
                                                    {{ \Carbon\Carbon::rupiah($salesReport['summary']['grand_total'], 0, ',', '.') }}
                                                </th>
                                            </tr>
                                            <tr class="table-info">
                                                <th colspan="6" class="text-end">Total Companies:</th>
                                                <th colspan="2">{{ $salesReport['summary']['total_companies'] }}
                                                    Perusahaan</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('pos.report.daily') }}" class="btn btn-secondary">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
