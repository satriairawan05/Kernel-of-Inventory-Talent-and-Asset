@extends('admin.layouts.app')

@push('css')
    <style>
        .form-icon {
            pointer-events: none;
        }
    </style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.password-toggle')
                .forEach(function(toggle) {

                    toggle.addEventListener('click', function() {

                        const input = this
                            .closest('.input-group')
                            .querySelector('input');

                        const icon = this.querySelector('.fa-eye, .fa-eye-slash');
                        console.log(icon);

                        if (input.type === 'password') {

                            input.type = 'text';

                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');

                        } else {

                            input.type = 'password';

                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');

                        }

                    });

                });
        });
    </script>
@endpush

@section('content')
    <div class="card">

        <div class="card-header">
            <h4>Add New Account</h4>
        </div>

        <div class="card-body">

            <form action="{{ route('setting.account.store') }}" method="POST">

                @csrf

                <div class="mb-3">

                    <label class="form-label" for="name">
                        Full Name
                    </label>

                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror" placeholder="Nazril Ahmad">

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="mb-3">

                    <label class="form-label" for="email">
                        Email Address
                    </label>

                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror" placeholder="nazril@aam-group.com">

                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label" for="password">
                            Password
                        </label>

                        <div class="input-group">
                            <input type="password" id="password" name="password" class="form-control"
                                placeholder="********">
                            <span class="input-group-text password-toggle" style="cursor:pointer;">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label" for="password_confirmation">
                            Confirm Password
                        </label>

                        <div class="input-group">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-control" placeholder="********">

                            <span class="input-group-text password-toggle" style="cursor:pointer;">

                                <i class="fas fa-eye"></i>

                            </span>
                        </div>

                    </div>

                </div>

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('setting.account.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">

                        Add Account

                    </button>

                </div>

            </form>

        </div>

    </div>
@endsection
