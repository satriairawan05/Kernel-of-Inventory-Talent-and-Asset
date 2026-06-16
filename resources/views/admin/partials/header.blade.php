<!DOCTYPE html>
<html lang="en-US" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">
<!-- Mirrored from prium.github.io/phoenix/v1.24.0/pages/starter.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 Jun 2026 07:08:48 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>{{ env('APP_NAME') }}</title>
    
    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="512x512" href="{{ asset('assets/img/favicons/android-chrome-512x512.png') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('assets/img/favicons/android-chrome-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicons/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/img/favicons/manifest.json') }}">
    <meta name="theme-color" content="#ffffff">
    <meta name="description" content="Kernel of Inventory Talent and Asset adalah project yang mengintegrasikan HRIS, Inventory dan POS dalam 1 wadah">
    <script src="{{ asset('vendors/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap"
        rel="stylesheet">
    <link href="{{ asset('vendors/simplebar/simplebar.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="../../../../unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link href="{{ asset('assets/css/theme-rtl.min.css') }}" type="text/css" rel="stylesheet" id="style-rtl">
    <link href="{{ asset('assets/css/theme.min.css') }}" type="text/css" rel="stylesheet" id="style-default">
    <link href="{{ asset('assets/css/user-rtl.min.css') }}" type="text/css" rel="stylesheet" id="user-style-rtl">
    <link href="{{ asset('assets/css/user.min.css') }}" type="text/css" rel="stylesheet" id="user-style-default">
    <style>
        :root {
            --soft-surface: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            --soft-border: rgba(148, 163, 184, 0.18);
            --soft-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
        }

        body {
            background:
                radial-gradient(circle at top, rgba(56, 189, 248, 0.08), transparent 22%),
                linear-gradient(180deg, #f5f7fb 0%, #eef4ff 100%);
        }

        .card {
            border: 1px solid var(--soft-border) !important;
            border-radius: 22px !important;
            box-shadow: var(--soft-shadow) !important;
            background: var(--soft-surface) !important;
        }

        .card-header {
            background: linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(248,250,252,0.98) 100%) !important;
            border-bottom: 1px solid var(--soft-border) !important;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background: linear-gradient(180deg, #eef4ff 0%, #f8fbff 100%) !important;
            color: #334155;
            font-weight: 700;
            letter-spacing: .02em;
            border-bottom: 1px solid var(--soft-border) !important;
        }

        .table tbody tr {
            transition: transform .15s ease, background-color .15s ease;
        }

        .table tbody tr:hover {
            background: #f8fbff;
            transform: translateY(-1px);
        }

        .btn {
            border-radius: 12px !important;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%) !important;
            border: none !important;
        }

        .btn-success {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
            border: none !important;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%) !important;
            border: none !important;
        }

        .badge {
            border-radius: 999px !important;
            font-weight: 600;
        }

        .user-initial-avatar {
            width: 100%;
            height: 100%;

            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #000;
            color: #fff;

            font-size: 20px;
            font-weight: 700;
        }
    </style>

    @stack('css')
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
</head>
