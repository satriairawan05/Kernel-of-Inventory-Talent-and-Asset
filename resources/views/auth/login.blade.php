@extends('layouts.main')

@section('content')
    <main class="main" id="top">
        <div class="container-fluid bg-body-tertiary dark__bg-gray-1200">
            <div class="bg-holder bg-auth-card-overlay" style="background-image:url({{ asset('assets/img/bg/37.png') }});">
            </div>
            <!--/.bg-holder-->
            <div class="row flex-center position-relative min-vh-100 g-0 py-5">
                <div class="col-12 col-sm-10 col-xl-8">
                    <div class="card border border-translucent auth-card">
                        <div class="card-body pe-md-0">
                            <div class="row align-items-center gx-0 gy-7">
                                <div
                                    class="col-auto bg-body-highlight dark__bg-gray-1100 rounded-3 position-relative overflow-hidden auth-title-box">
                                    <div class="bg-holder"
                                        style="background-image:url({{ asset('assets/img/bg/38.png') }});"></div>
                                    <!--/.bg-holder-->
                                    <div
                                        class="position-relative px-4 px-lg-7 pt-7 pb-7 pb-sm-5 text-center text-md-start pb-lg-7 card-sign-up">
                                        <h3 class="mb-3 text-body-emphasis fs-7">KITA</h3>
                                        <p class="text-body-tertiary">Kernel of Inventory, Talent and Asset</p>
                                        {{-- <ul class="list-unstyled mb-0 w-max-content w-md-auto">
                        <li class="d-flex align-items-center"><span class="fas fa-check-circle text-success me-2"></span><span class="text-body-tertiary fw-semibold">Fast</span></li>
                        <li class="d-flex align-items-center"><span class="fas fa-check-circle text-success me-2"></span><span class="text-body-tertiary fw-semibold">Simple</span></li>
                        <li class="d-flex align-items-center"><span class="fas fa-check-circle text-success me-2"></span><span class="text-body-tertiary fw-semibold">Responsive</span></li>
                      </ul> --}}
                                    </div>
                                    <div class="position-relative z-n1 mb-6 d-none d-md-block text-center mt-md-15"><img
                                            class="auth-title-box-img d-dark-none"
                                            src="{{ asset('assets/img/spot-illustrations/auth.png') }}"
                                            alt="" /><img class="auth-title-box-img d-light-none"
                                            src="{{ asset('assets/img/spot-illustrations/auth-dark.png') }}"
                                            alt="" /></div>
                                </div>
                                <div class="col mx-auto">
                                    <div class="auth-form-box">
                                        <div class="text-center mb-3"><a
                                                class="d-flex flex-center text-decoration-none mb-4"
                                                href="{{ route('login') }}">
                                                <div class="d-flex align-items-center fw-bolder fs-3 d-inline-block"><img
                                                        src="{{ asset('assets/img/icons/av color.png') }}" alt="phoenix"
                                                        width="58" /></div>
                                            </a>
                                            <h3 class="text-body-highlight">Sign In</h3>
                                            <p class="text-body-tertiary">Welcome back! Please enter your details</p>
                                        </div>
                                        <form method="POST" action="{{ route('login') }}">
                                            @csrf
                                            <div class="mb-3 text-start"><label class="form-label" for="email">Email
                                                    address</label>
                                                <div class="form-icon-container"><input class="form-control @error('email')
                                                    is-invalid
                                                @enderror form-icon-input"
                                                        id="email" type="email" placeholder="name@example.com" name="email" value="{{ old('email') }}" />
                                                        @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror<span
                                                        class="fas fa-user text-body fs-9 form-icon"></span></div>
                                            </div>
                                            <div class="mb-3 text-start"><label class="form-label"
                                                    for="password">Password</label>
                                                <div class="form-icon-container" data-password="data-password"><input
                                                        class="form-control form-icon-input pe-6" id="password" name="password"
                                                        type="password" placeholder="Password"
                                                        data-password-input="data-password-input" /><span
                                                        class="fas fa-key text-body fs-9 form-icon"></span><button
                                                        class="btn px-3 py-0 h-100 position-absolute top-0 end-0 fs-7 text-body-tertiary"
                                                        data-password-toggle="data-password-toggle"><span
                                                            class="fas fa-eye show"></span><span
                                                            class="fas fa-eye-slash hide"></span></button></div>
                                            </div>
                                            <div class="row flex-between-center mb-8">
                                                <div class="col-auto">
                                                    <div class="form-check mb-0"><input class="form-check-input"
                                                            id="basic-checkbox" type="checkbox" checked="checked" /><label
                                                            class="form-check-label mb-0" for="basic-checkbox">Remember
                                                            me</label></div>
                                                </div>
                                                <div class="col-auto">
                                                    {{-- <a class="fs-9 fw-semibold"
                                                        href="{{ route('password.request') }}">Forgot Password?</a></div> --}}
                                            </div><button class="btn btn-primary w-100 mb-3">Sign In</button>
                                            {{-- <div class="text-center"><a class="fs-9 fw-bold" href="{{ route('register') }}">Create an
                                                    account</a></div> --}}
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="form-check mb-3">
                            <input class="form-check-input" id="termsService" type="checkbox" /><label class="form-label fs-9 text-transform-none" for="termsService">I accept the <a href="#!">terms </a>and <a href="#!">privacy policy</a></label>
                        </div> --}}
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
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

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
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> --}}
@endsection
