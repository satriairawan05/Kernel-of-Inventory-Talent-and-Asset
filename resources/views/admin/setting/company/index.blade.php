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
        <div class="card-header">
            <h4 class="card-title">List Outlet</h4>
        </div>
        <div class="card-body">
            <a href="{{ route('setting.company.create') }}" class="btn btn-success btn-add-company">
                <i class="fas fa-plus me-1"></i>
                Add New Outlet
            </a>
            <div class="table-responsive mb-3">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Address</th>
                            <th scope="col">Logo</th>
                            <th scope="col">Handle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($companies as $company)
                            <tr>
                                <th scope="row">{{ $companies->firstItem() + $loop->index }}</th>
                                <td>{{ $company->company_name }}</td>
                                <td>{{ $company->company_email }}</td>
                                <td>{{ $company->company_phone }}</td>
                                <td>{{ $company->company_address }}</td>
                                <td>
                                    <img src="{{ $company->logo_url }}" alt="{{ $company->company_name }}"
                                        class="company-logo rounded">
                                </td>
                                <td class="action-buttons d-md-flex flex-md-row align-items-md-center gap-2">
                                    <a href="{{ route('setting.company.edit', $company) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Edit Outlet</a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteCompanyModal{{ $company->id }}">
                                        <i class="fas fa-trash"></i> Delete Outlet
                                    </button>

                                    <div class="modal fade" id="deleteCompanyModal{{ $company->id }}" tabindex="-1"
                                        data-bs-backdrop="static"
                                        aria-labelledby="deleteCompanyModalLabel{{ $company->id }}" aria-hidden="true">

                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">

                                                <div class="modal-header bg-danger">
                                                    <h5 class="modal-title text-white"
                                                        id="deleteCompanyModalLabel{{ $company->id }}">
                                                        <i class="fas fa-triangle-exclamation me-2"></i>
                                                        Delete Company
                                                    </h5>

                                                    <button class="btn p-1" type="button" data-bs-dismiss="modal"
                                                        aria-label="Close">

                                                        <i class="fas fa-times text-white"></i>

                                                    </button>
                                                </div>

                                                <div class="modal-body text-center py-4">

                                                    <div class="mb-3">
                                                        <i class="fas fa-trash-alt text-danger"
                                                            style="font-size: 4rem;"></i>
                                                    </div>

                                                    <h5 class="mb-3">
                                                        Are you sure?
                                                    </h5>

                                                    <p class="text-muted mb-0">
                                                        You are about to delete
                                                        <strong>{{ $company->company_name }}</strong>.
                                                    </p>

                                                    <p class="text-danger small mt-2 mb-0">
                                                        This action cannot be undone.
                                                    </p>

                                                </div>

                                                <div class="modal-footer">

                                                    <button type="button" class="btn btn-outline-secondary"
                                                        data-bs-dismiss="modal">
                                                        Cancel
                                                    </button>

                                                    <form action="{{ route('setting.company.destroy', $company) }}"
                                                        method="POST">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit" class="btn btn-danger">

                                                            <i class="fas fa-trash me-2"></i>
                                                            Delete Company

                                                        </button>

                                                    </form>

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty

                            <tr>
                                <td colspan="7" class="text-center">
                                    No Data Found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3 pagination-wrapper">
                {{ $companies->links() }}
            </div>
        </div>
    </div>
@endsection
