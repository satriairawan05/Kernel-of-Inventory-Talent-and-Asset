<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ env('APP_NAME') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="512x512" href="{{ asset('assets/img/favicons/android-chrome-512x512.png') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('assets/img/favicons/android-chrome-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicons/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/img/favicons/manifest.json') }}">
    <meta name="theme-color" content="#ffffff">
    <meta name="description"
        content="Kernel of Inventory Talent and Asset adalah project yang mengintegrasikan HRIS, Inventory dan POS dalam 1 wadah">
</head>

<body>
    <div class="container-fluid">

        <!-- Header -->
        <nav class="navbar navbar-expand-lg navbar-orange mb-5">
            <div class="container-fluid">

                <span class="navbar-brand text-white fw-bold">
                    {{ env('APP_NAME') }}
                </span>

                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">

                        <i class="fa-solid fa-user me-2"></i>
                        {{ auth()->user()->name }}
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <span class="dropdown-item-text">
                                {{ auth()->user()->email }}
                            </span>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i>
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>

            </div>
        </nav>

        <!-- Menu -->
        <div class="row g-4">

            <!-- Dashboard -->
            <div class="col-12 col-md-6 col-xl-4">
                <a href="#" class="text-decoration-none">
                    <div class="card dashboard-card dash-card">
                        <div class="card-body">
                            <i class="fa-solid fa-chart-line"></i>
                            <h4>Dashboard</h4>
                        </div>
                    </div>
                </a>
            </div>

            <!-- HR -->
            <div class="col-12 col-md-6 col-xl-4">
                <a href="{{ route('hr.home') }}" class="text-decoration-none">
                    <div class="card dashboard-card hr-card">
                        <div class="card-body">
                            <i class="fa-solid fa-users"></i>
                            <h4>Human Resources</h4>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Presence -->
            <div class="col-12 col-md-6 col-xl-4">
                <a href="#" class="text-decoration-none">
                    <div class="card dashboard-card presence-card">
                        <div class="card-body">
                            <i class="fa-solid fa-user-check"></i>
                            <h4>Presence</h4>
                        </div>
                    </div>
                </a>
            </div>

            <!-- POS -->
            <div class="col-12 col-md-6 col-xl-4">
                <a href="{{ route('pos.home') }}" class="text-decoration-none">
                    <div class="card dashboard-card pos-card">
                        <div class="card-body">
                            <i class="fa-solid fa-cash-register"></i>
                            <h4>Point of Sales</h4>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Inventory -->
            <div class="col-12 col-md-6 col-xl-4">
                <a href="{{ route('inventory.home') }}" class="text-decoration-none">
                    <div class="card dashboard-card inv-card">
                        <div class="card-body">
                            <i class="fa-solid fa-boxes-stacked"></i>
                            <h4>Inventories</h4>
                        </div>
                    </div>
                </a>
            </div>

            <!-- System -->
            <div class="col-12 col-md-6 col-xl-4">
                <a href="{{ route('setting.home') }}" class="text-decoration-none">
                    <div class="card dashboard-card sys-card">
                        <div class="card-body">
                            <i class="fa-solid fa-gears"></i>
                            <h4>System Settings</h4>
                        </div>
                    </div>
                </a>
            </div>

        </div>

    </div>

    <style>
        body {
            background: #f4f6f9;
            min-height: 100vh;
        }

        /* NAVBAR */
        .navbar-orange {
            background: linear-gradient(135deg,
                    #001f3f 0%,
                    #2c3e50 100%);
            box-shadow: 0 4px 15px rgba(0, 31, 63, .25);
        }

        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 700;
        }

        /* CARD */
        .dashboard-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            transition: all .3s ease;
            height: 220px;
            cursor: pointer;
        }

        .dashboard-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, .15);
        }

        .dashboard-card .card-body {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .dashboard-card i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: white;
        }

        .dashboard-card h4 {
            color: white;
            font-weight: 600;
            margin: 0;
        }

        /* MODULE COLORS */
        .dash-card {
            background: linear-gradient(135deg, #0a2540, #1a3a5f);
        }

        .hr-card {
            background: linear-gradient(135deg, #3d1c00, #7a3d00);
        }

        .pos-card {
            background: linear-gradient(135deg, #0d3b2b, #1b5e48);
        }

        .inv-card {
            background: linear-gradient(135deg, #0d3d35, #1b6b5e);
        }

        .presence-card {
            background: linear-gradient(135deg, #4a0d1a, #8b1e2e);
        }

        .sys-card {
            background: linear-gradient(135deg, #0a1a3a, #1e3a6b);
        }

        /* MOBILE */

        @media (max-width: 768px) {

            .navbar-brand {
                font-size: 1rem;
            }

            .dashboard-card {
                height: 180px;
            }

            .dashboard-card i {
                font-size: 3rem;
            }

            .dashboard-card h4 {
                font-size: 1rem;
            }

            .dropdown button {
                font-size: .85rem;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
