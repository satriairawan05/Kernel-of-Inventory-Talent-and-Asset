@php
    // Ambil kode error dari exception atau dari section
    $statusCode = isset($exception) && method_exists($exception, 'getStatusCode') 
        ? $exception->getStatusCode() 
        : 500;
    
    $code = (int) (trim($__env->yieldContent('code')) ?: $statusCode);
    $title = trim($__env->yieldContent('title')) ?: ($exception ? $exception->getMessage() : 'Error');
    $message = trim($__env->yieldContent('message')) ?: ($exception ? $exception->getMessage() : 'Something went wrong.');

    // Peta ikon berdasarkan kode error
    $iconMap = [
        401 => ['icon' => 'fa-lock', 'color' => 'text-danger', 'label' => 'Unauthorized'],
        402 => ['icon' => 'fa-credit-card', 'color' => 'text-warning', 'label' => 'Payment Required'],
        403 => ['icon' => 'fa-ban', 'color' => 'text-danger', 'label' => 'Forbidden'],
        404 => ['icon' => 'fa-file-search', 'color' => 'text-primary', 'label' => 'Page Not Found', 'illustration' => true],
        419 => ['icon' => 'fa-clock', 'color' => 'text-warning', 'label' => 'Session Expired'],
        429 => ['icon' => 'fa-tachometer-alt', 'color' => 'text-danger', 'label' => 'Too Many Requests'],
        500 => ['icon' => 'fa-exclamation-triangle', 'color' => 'text-danger', 'label' => 'Server Error'],
        503 => ['icon' => 'fa-wrench', 'color' => 'text-warning', 'label' => 'Service Unavailable'],
    ];

    $errorData = $iconMap[$code] ?? ['icon' => 'fa-exclamation-circle', 'color' => 'text-danger', 'label' => 'Error'];
    $isIllustration = isset($errorData['illustration']) && $errorData['illustration'] === true;

    $pageTitle = $title ?: $errorData['label'] . ' (' . $code . ')';
@endphp

<!DOCTYPE html>
<html lang="en-US" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $pageTitle }} - {{ config('app.name', 'KitaPOS') }}</title>

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="512x512" href="{{ asset('assets/img/favicons/android-chrome-512x512.png') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('assets/img/favicons/android-chrome-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicons/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/img/favicons/manifest.json') }}">
    <meta name="theme-color" content="#ffffff">

    <!-- Vendor & Theme CSS -->
    <script src="{{ asset('vendors/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap" rel="stylesheet">
    <link href="{{ asset('vendors/simplebar/simplebar.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link href="{{ asset('assets/css/theme-rtl.min.css') }}" type="text/css" rel="stylesheet" id="style-rtl">
    <link href="{{ asset('assets/css/theme.min.css') }}" type="text/css" rel="stylesheet" id="style-default">
    <link href="{{ asset('assets/css/user-rtl.min.css') }}" type="text/css" rel="stylesheet" id="user-style-rtl">
    <link href="{{ asset('assets/css/user.min.css') }}" type="text/css" rel="stylesheet" id="user-style-default">

    <script>
        var phoenixIsRTL = window.config.config.phoenixIsRTL;
        if (phoenixIsRTL) {
            var linkDefault = document.getElementById('style-default');
            var userLinkDefault = document.getElementById('user-style-default');
            linkDefault.setAttribute('disabled', true);
            userLinkDefault.setAttribute('disabled', true);
            document.querySelector('html').setAttribute('dir', 'rtl');
        } else {
            var linkRTL = document.getElementById('style-rtl');
            var userLinkRTL = document.getElementById('user-style-rtl');
            linkRTL.setAttribute('disabled', true);
            userLinkRTL.setAttribute('disabled', true);
        }
    </script>

    <style>
        .error-icon {
            font-size: 8rem;
            color: var(--bs-primary);
        }
        .error-icon-lg {
            font-size: 12rem;
        }
        .error-illustration {
            max-width: 400px;
            width: 100%;
        }
        .error-icon .fas {
            display: inline-block;
        }
    </style>
</head>

<body>
    <main class="main" id="top">
        <div class="px-3">
            <div class="row min-vh-100 flex-center p-5">
                <div class="col-12 col-xl-10 col-xxl-8">
                    <div class="row justify-content-center align-items-center g-5">

                        <!-- Kolom Ilustrasi / Ikon -->
                        <div class="col-12 col-lg-6 text-center order-lg-1">
                            @if ($isIllustration)
                                <img class="img-fluid w-lg-100 d-dark-none error-illustration"
                                     src="{{ asset('assets/img/spot-illustrations/404-illustration.png') }}"
                                     alt="404 Illustration" />
                                <img class="img-fluid w-md-50 w-lg-100 d-light-none error-illustration"
                                     src="{{ asset('assets/img/spot-illustrations/dark_404-illustration.png') }}"
                                     alt="404 Illustration Dark" />
                            @else
                                <div class="error-icon error-icon-lg {{ $errorData['color'] ?? 'text-danger' }}">
                                    <i class="fas {{ $errorData['icon'] ?? 'fa-exclamation-circle' }}"></i>
                                </div>
                                <p class="text-muted mt-3 fw-semibold">{{ $errorData['label'] ?? 'Error' }}</p>
                            @endif
                        </div>

                        <!-- Kolom Informasi -->
                        <div class="col-12 col-lg-6 text-center text-lg-start">
                            <h1 class="display-1 fw-bolder text-primary mb-3">{{ $code }}</h1>
                            <h2 class="text-body-secondary fw-bolder mb-3">{{ $title }}</h2>
                            <p class="text-body mb-5">{{ $message }}</p>
                            <a class="btn btn-lg btn-primary" href="{{ auth()->check() ? route('home') : url('/') }}">
                                <i class="fas fa-home me-2"></i> Go Home
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScripts -->
    <script src="{{ asset('vendors/popper/popper.min.js') }}"></script>
    <script src="{{ asset('vendors/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendors/anchorjs/anchor.min.js') }}"></script>
    <script src="{{ asset('vendors/is/is.min.js') }}"></script>
    <script src="{{ asset('vendors/fontawesome/all.min.js') }}"></script>
    <script src="{{ asset('vendors/lodash/lodash.min.js') }}"></script>
    <script src="{{ asset('vendors/list.js/list.min.js') }}"></script>
    <script src="{{ asset('vendors/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('vendors/dayjs/dayjs.min.js') }}"></script>
    <script src="{{ asset('assets/js/phoenix.js') }}"></script>
</body>
</html>