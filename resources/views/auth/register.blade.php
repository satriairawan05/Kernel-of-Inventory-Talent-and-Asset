@extends('layouts.main')

@section('content')
    <main class="main" id="top">
        <div class="container-fluid bg-body-tertiary dark__bg-gray-1200">
            <div class="bg-holder bg-auth-card-overlay" style="background-image:url({{ asset('assets/img/bg/37.png') }});">
            </div>
            <div class="row flex-center position-relative min-vh-100 g-0 py-5">
                <div class="col-12 col-sm-10 col-xl-8">
                    <div class="card border border-translucent auth-card">
                        <div class="card-body pe-md-0">
                            <div class="row align-items-center gx-0 gy-7">

                                <div
                                    class="col-auto bg-body-highlight dark__bg-gray-1100 rounded-3 position-relative overflow-hidden auth-title-box">
                                    <div class="bg-holder"
                                        style="background-image:url({{ asset('assets/img/bg/38.png') }});"></div>
                                    <div
                                        class="position-relative px-4 px-lg-7 pt-7 pb-7 pb-sm-5 text-center text-md-start pb-lg-7 card-sign-up">
                                        <h3 class="mb-3 text-body-emphasis fs-7">KITA</h3>
                                        <p class="text-body-tertiary">Kernel of Inventory, Talent and Asset</p>
                                    </div>
                                    <div class="position-relative z-n1 mb-6 d-none d-md-block text-center mt-md-15">
                                        <img class="auth-title-box-img d-dark-none"
                                            src="{{ asset('assets/img/spot-illustrations/auth.png') }}" alt="" />
                                        <img class="auth-title-box-img d-light-none"
                                            src="{{ asset('assets/img/spot-illustrations/auth-dark.png') }}"
                                            alt="" />
                                    </div>
                                </div>

                                <div class="col mx-auto">
                                    <div class="auth-form-box">
                                        <div class="text-center mb-3">
                                            <a class="d-flex flex-center text-decoration-none mb-4"
                                                href="{{ route('register') }}">
                                                <div class="d-flex align-items-center fw-bolder fs-3 d-inline-block">
                                                    <img src="{{ asset('assets/img/icons/av color.png') }}" alt="phoenix"
                                                        width="58" />
                                                </div>
                                            </a>
                                            <h3 class="text-body-highlight">Sign Up</h3>
                                            <p class="text-body-tertiary">Create a new account</p>
                                        </div>

                                        <form method="POST" action="{{ route('register') }}">
                                            @csrf

                                            <div class="mb-3 text-start">
                                                <label class="form-label" for="name">Name</label>
                                                <input class="form-control @error('name') is-invalid @enderror"
                                                    id="name" name="name" value="{{ old('name') }}"
                                                    autocomplete="name" type="text" placeholder="Name" />
                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="mb-3 text-start">
                                                <label class="form-label" for="email">Email address</label>
                                                <input class="form-control @error('email') is-invalid @enderror"
                                                    id="email" type="email" name="email" value="{{ old('email') }}"
                                                    autocomplete="email" placeholder="name@example.com" />
                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="row g-3 mb-3">
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="password">Password</label>
                                                    <div class="position-relative" data-password="data-password">
                                                        <div class="position-relative" data-password="data-password">
                                                            <input
                                                                class="form-control @error('password') is-invalid @enderror form-icon-input pe-6"
                                                                id="password" name="password" type="password"
                                                                placeholder="Password"
                                                                data-password-input="data-password-input" />

                                                            <button type="button"
                                                                class="btn bg-body-tertriary px-3 py-0 h-100 position-absolute top-0 end-0 fs-7 text-tertriary rounded-start-0"
                                                                data-password-toggle="data-password-toggle">
                                                                <span class="fas fa-eye show"></span>
                                                                <span class="fas fa-eye-slash hide"></span>
                                                            </button>
                                                        </div>

                                                        @error('password')
                                                            <span class="invalid-feedback d-block" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <label class="form-label" for="confirmPassword">Confirm Password</label>
                                                    <div class="position-relative" data-password="data-password">
                                                        <input
                                                            class="form-control @error('password_confirmation') is-invalid @enderror form-icon-input pe-6"
                                                            id="confirmPassword" name="password_confirmation" type="password"
                                                            placeholder="Confirm Password"
                                                            data-password-input="data-password-input" autocomplete="new-password" />

                                                        <button type="button"
                                                            class="btn bg-body-tertriary px-3 py-0 h-100 position-absolute top-0 end-0 fs-7 text-tertriary rounded-start-0"
                                                            data-password-toggle="data-password-toggle">
                                                            <span class="fas fa-eye show"></span>
                                                            <span class="fas fa-eye-slash hide"></span>
                                                        </button>

                                                        @error('password_confirmation')
                                                            <span class="invalid-feedback d-block" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-primary w-100 mb-3">Sign Up</button>
                                            <div class="text-center">
                                                <a class="fs-9 fw-bold" href="{{ route('login') }}">Already have an
                                                    account? Sign In</a>
                                            </div>
                                        </form>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    {{-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> --}}
@endsection
