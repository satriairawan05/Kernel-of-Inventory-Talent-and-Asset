@extends('admin.layouts.app')

@push('css')
    <style>
        .sales-hero {
            background: linear-gradient(135deg, #ccf0f2 0%, #a7f3d0 100%);
            border-radius: 2rem;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .greeting-icon {
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #10b981;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        .sales-card {
            border: none;
            border-radius: 1.5rem;
            transition: all 0.25s ease;
            background: white;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid #d1fae5;
            height: 100%;
            overflow: hidden;
        }

        .sales-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 30px -12px rgba(0, 0, 0, 0.1);
            border-color: #6ee7b7;
        }

        .card-icon {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: #ecfdf5;
            color: #10b981;
            font-size: 1.6rem;
        }

        .badge-subtle {
            background: #d1fae5;
            color: #047857;
            border-radius: 30px;
            padding: 0.25rem 0.8rem;
            font-size: 0.7rem;
        }

        .submenu-list {
            list-style: none;
            padding-left: 0;
            margin-top: 0.75rem;
        }

        .submenu-list li {
            margin-bottom: 0.5rem;
        }

        .submenu-list a {
            text-decoration: none;
            font-size: 0.8rem;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            transition: 0.2s;
        }

        .submenu-list a:hover {
            color: #10b981;
            transform: translateX(3px);
        }

        .info-row {
            border-top: 1px solid #d1fae5;
            margin-top: 0.8rem;
            padding-top: 0.8rem;
            font-size: 0.75rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4 py-3">
        <!-- Hero Section -->
        <div class="sales-hero d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-4">
                <div class="greeting-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1" style="color: #065f46;">Sales Management</h2>
                    <p class="mb-0 text-secondary">Hello, <strong>{{ auth()->user()->name }}</strong>! Monitor sales, create transactions, and view realtime reports.</p>
                    <div class="mt-2 small text-muted">
                        <i class="fas fa-cash-register me-1"></i> KITA helps boost revenue and cashier efficiency.
                    </div>
                </div>
            </div>
            <div class="text-muted">
                <i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if (session('failed'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('failed') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row g-4 mb-5">
            <div class="col-md-3 col-6">
                <div class="card sales-card p-3 text-center">
                    <h6 class="text-muted">Total Transactions (today)</h6>
                    <h3 class="fw-bold text-success">64</h3>
                    <small class="text-muted">+12 from yesterday</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card sales-card p-3 text-center">
                    <h6 class="text-muted">Gross Revenue</h6>
                    <h3 class="fw-bold text-primary">Rp 12.8jt</h3>
                    <small class="text-muted">today</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card sales-card p-3 text-center">
                    <h6 class="text-muted">Average Transaction</h6>
                    <h3 class="fw-bold text-info">Rp 200k</h3>
                    <small class="text-muted">per receipt</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card sales-card p-3 text-center">
                    <h6 class="text-muted">Best Selling Product</h6>
                    <h3 class="fw-bold text-warning">Ayam Geprek</h3>
                    <small class="text-muted">37 portions</small>
                </div>
            </div>
        </div>

        <!-- Sales Menu Cards -->
        <div class="row g-4">
            @if ($access['POS']['Read'] == 1)
                <!-- Point of Sales -->
                <div class="col-xl-6 col-md-6">
                    <div class="card sales-card">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="card-icon mb-2"><i class="fas fa-cash-register"></i></div>
                                <span class="badge-subtle">Active</span>
                            </div>
                            <h5 class="fw-bold">Point of Sales</h5>
                            <p class="small text-secondary">Process direct sales transactions, manage cart, discounts, and print receipts.</p>
                            <div class="info-row">
                                <a href="#" class="small text-success fw-semibold">Start Transaction <i class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($access['Sale Reports (POS)']['Read'] == 1)
                <!-- Sales Reports -->
                <div class="col-xl-6 col-md-6">
                    <div class="card sales-card">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="card-icon mb-2"><i class="fas fa-chart-bar"></i></div>
                                <span class="badge-subtle">Reports</span>
                            </div>
                            <h5 class="fw-bold">Sales Reports</h5>
                            <p class="small text-secondary">View sales reports by period for business analysis.</p>
                            <ul class="submenu-list">
                                <li><a href="#"><i class="fas fa-calendar-day fa-fw"></i> Daily</a></li>
                                <li><a href="#"><i class="fas fa-calendar-week fa-fw"></i> Weekly</a></li>
                                <li><a href="#"><i class="fas fa-calendar-alt fa-fw"></i> Monthly</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- KITA Motivation -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-light border rounded-4 shadow-sm d-flex align-items-center gap-3"
                    style="background: #ffffff;">
                    <i class="fas fa-heart text-danger fa-2x"></i>
                    <div>
                        <strong class="d-block">💡 KITA — Kernel of Inventory, Talent & Asset</strong>
                        <span class="small text-secondary">Sales are the lifeblood of business. Monitor every transaction, identify trends, and maximize profits with accurate data. KITA is ready to support your sales success!</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection