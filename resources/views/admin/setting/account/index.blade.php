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
            <h4 class="card-title">List Account</h4>
        </div>
        <div class="card-body">
            <a href="{{ route('setting.account.create') }}" class="btn btn-success btn-add-company">
                <i class="fas fa-plus me-1"></i>
                Add New
            </a>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Handle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                            <tr>
                                <th scope="row">{{ $accounts->firstItem() + $loop->index }}</th>
                                <td>{{ $account->name }}</td>
                                <td>{{ $account->email }}</td>
                                <td class="action-buttons d-md-flex flex-md-row align-items-md-center gap-2">
                                    <a href="{{ route('setting.account.edit', $account) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Edit</a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteAccountModal{{ $account->id }}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>

                                    <div class="modal fade" id="deleteAccountModal{{ $account->id }}" tabindex="-1"
                                        data-bs-backdrop="static"
                                        aria-labelledby="deleteAccountModalLabel{{ $account->id }}" aria-hidden="true">

                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">

                                                <div class="modal-header bg-danger">
                                                    <h5 class="modal-title text-white"
                                                        id="deleteAccountModalLabel{{ $account->id }}">
                                                        <i class="fas fa-triangle-exclamation me-2"></i>
                                                        Delete Account
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
                                                        <strong>{{ $account->name }}</strong>.
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

                                                    <form action="{{ route('setting.account.destroy', $account) }}"
                                                        method="POST">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit" class="btn btn-danger">

                                                            <i class="fas fa-trash me-2"></i>
                                                            Delete Account

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
                                <td colspan="6" class="text-center">
                                    No Data Found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3 pagination-wrapper">
                {{ $accounts->links() }}
            </div>
        </div>
    </div>
@endsection
