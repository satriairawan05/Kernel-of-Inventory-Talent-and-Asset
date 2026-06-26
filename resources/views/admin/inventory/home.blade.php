@extends('admin.layouts.app')

@push('css')
    <style>
        .inventory-hero {
            background: linear-gradient(135deg, #f0f9ff 0%, #dcfce7 100%);
            border-radius: 2rem;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(34, 197, 94, 0.2);
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
            color: #16a34a;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        .inventory-card {
            border: none;
            border-radius: 1.5rem;
            transition: all 0.25s ease;
            background: white;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid #eef2ff;
            height: 100%;
            overflow: hidden;
        }

        .inventory-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 30px -12px rgba(0, 0, 0, 0.1);
            border-color: #bbf7d0;
        }

        .card-icon {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: #f0fdf4;
            color: #16a34a;
            font-size: 1.6rem;
        }

        .badge-subtle {
            background: #dcfce7;
            color: #15803d;
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
            color: #16a34a;
            transform: translateX(3px);
        }

        .info-row {
            border-top: 1px solid #f0f4fe;
            margin-top: 0.8rem;
            padding-top: 0.8rem;
            font-size: 0.75rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4 py-3">
        <!-- Hero Section -->
        <div class="inventory-hero d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-4">
                <div class="greeting-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1" style="color: #14532d;">Inventory Management</h2>
                    <p class="mb-0 text-secondary">Hello, <strong>{{ auth()->user()->name }}</strong>! Manage stock, incoming/outgoing goods, and view realtime reports.</p>
                    <div class="mt-2 small text-muted">
                        <i class="fas fa-cubes me-1"></i> KITA helps you monitor every inventory movement.
                    </div>
                </div>
            </div>
            <div class="text-muted">
                <i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row g-4 mb-5">
            <div class="col-md-3 col-6">
                <div class="card inventory-card p-3">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <h6 class="text-muted">Total Products</h6>
                            <h3 class="fw-bold text-success mb-0">{{ number_format($stats['total_products']) }}</h3>
                            <small class="text-muted">registered products</small>
                        </div>
                        <div class="col-6">
                            <h6 class="text-muted">Total Variants</h6>
                            <h3 class="fw-bold text-primary mb-0">{{ number_format($stats['total_variants']) }}</h3>
                            <small class="text-muted">product variants</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card inventory-card p-3 text-center">
                    <h6 class="text-muted">Low Stock (<=5)</h6>
                    <h3 class="fw-bold text-warning">{{ number_format($stats['low_stock_count']) }}</h3>
                    <small class="text-muted">need restock</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card inventory-card p-3 text-center">
                    <h6 class="text-muted">Incoming Goods (this month)</h6>
                    <h3 class="fw-bold text-primary">{{ number_format($stats['incoming_transactions']) }}</h3>
                    <small class="text-muted">transactions</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card inventory-card p-3 text-center">
                    <h6 class="text-muted">Outgoing Goods (this month)</h6>
                    <h3 class="fw-bold text-info">{{ number_format($stats['outgoing_transactions']) }}</h3>
                    <small class="text-muted">transactions</small>
                </div>
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

        <!-- Menu Groups -->
        <div class="row g-4">
            @if ($access['Product']['Read'] == 1 || $access['Stock']['Read'] == 1)
            <!-- Master Data -->
            <div class="col-xl-4 col-md-6">
                <div class="card inventory-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="card-icon mb-2"><i class="fas fa-database"></i></div>
                            <span class="badge-subtle">Master Data</span>
                        </div>
                        <h5 class="fw-bold">Master Data</h5>
                        <p class="small text-secondary">Manage products, and initial stock.</p>
                        <ul class="submenu-list">
                            {{-- @if ($access['Category']['Read'] == 1)
                                <li><a href="{{ route('inventory.category.index') }}"><i class="fas fa-tag fa-fw"></i>
                                        Category</a></li>
                            @endif --}}
                            @if ($access['Product']['Read'] == 1)
                                <li><a href="{{ route('inventory.product.index') }}"><i class="fas fa-box-open fa-fw"></i>
                                        Product</a></li>
                            @endif
                            @if ($access['Stock']['Read'] == 1)
                                <li><a href="{{ route('inventory.stock.index') }}"><i class="fas fa-chart-line fa-fw"></i>
                                        Stock</a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            @if ($access['Incoming Good']['Read'] == 1)
                <!-- Incoming Goods -->
                <div class="col-xl-4 col-md-6">
                    <div class="card inventory-card">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="card-icon mb-2"><i class="fas fa-download"></i></div>
                                <span class="badge-subtle">Receiving</span>
                            </div>
                            <h5 class="fw-bold">Incoming Goods</h5>
                            <p class="small text-secondary">Record goods received into the warehouse.</p>
                            <ul class="submenu-list">
                                @if ($access['Incoming Good']['Create'] == 1)
                                    <li><a href="{{ route('inventory.stock-in.create') }}"><i
                                                class="fas fa-plus-circle fa-fw"></i>
                                            New Transaction</a></li>
                                @endif
                                <li><a href="{{ route('inventory.stock-in.index') }}"><i class="fas fa-history fa-fw"></i>
                                        Transaction List</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if ($access['Exit Item']['Read'] == 1)
                <!-- Exit Items -->
                <div class="col-xl-4 col-md-6">
                    <div class="card inventory-card">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="card-icon mb-2"><i class="fas fa-upload"></i></div>
                                <span class="badge-subtle">Outgoing</span>
                            </div>
                            <h5 class="fw-bold">Exit Items</h5>
                            <p class="small text-secondary">Record goods issued (sales, usage).</p>
                            <ul class="submenu-list">
                                @if ($access['Exit Item']['Create'] == 1)
                                    <li><a href="{{ route('inventory.stock-out.create') }}"><i
                                                class="fas fa-plus-circle fa-fw"></i> New Transaction</a></li>
                                @endif
                                <li><a href="{{ route('inventory.stock-out.index') }}"><i class="fas fa-history fa-fw"></i>
                                        Transaction List</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if ($access['Return Item']['Read'] == 1)
                <!-- Return Items -->
                <div class="col-xl-4 col-md-6">
                    <div class="card inventory-card">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="card-icon mb-2"><i class="fas fa-undo-alt"></i></div>
                                <span class="badge-subtle">Return</span>
                            </div>
                            <h5 class="fw-bold">Return Items</h5>
                            <p class="small text-secondary">Handle product returns from customers.</p>
                            <ul class="submenu-list">
                                @if ($access['Return Item']['Create'] == 1)
                                    <li><a href="{{ route('inventory.return-stock.create') }}"><i
                                                class="fas fa-plus-circle fa-fw"></i> New Transaction</a></li>
                                @endif
                                <li><a href="{{ route('inventory.return-stock.index') }}"><i
                                            class="fas fa-history fa-fw"></i> Transaction List</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if ($access['Stock Opname']['Read'] == 1)
                <!-- Stock Opnames -->
                <div class="col-xl-4 col-md-6">
                    <div class="card inventory-card">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="card-icon mb-2"><i class="fas fa-clipboard-list"></i></div>
                                <span class="badge-subtle">Stock Opname</span>
                            </div>
                            <h5 class="fw-bold">Stock Opnames</h5>
                            <p class="small text-secondary">Perform physical stock checks.</p>
                            <ul class="submenu-list">
                                <li><a href="{{ route('inventory.stock-opname.index') }}"><i
                                            class="fas fa-chart-bar fa-fw"></i> Reports</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if ($access['Report']['Read'] == 1)
                <!-- Reports -->
                <div class="col-xl-4 col-md-6">
                    <div class="card inventory-card">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="card-icon mb-2"><i class="fas fa-chart-pie"></i></div>
                                <span class="badge-subtle">Analytics</span>
                            </div>
                            <h5 class="fw-bold">Reports</h5>
                            <p class="small text-secondary">Monitor stock movements periodically.</p>
                            <ul class="submenu-list">
                                <li><a href="{{ route('inventory.report.index') }}"><i class="fas fa-chart-line fa-fw"></i> Reports</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
        </div>
        @endif

        <!-- KITA Motivation -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-light border rounded-4 shadow-sm d-flex align-items-center gap-3"
                    style="background: #ffffff;">
                    <i class="fas fa-heart text-danger fa-2x"></i>
                    <div>
                        <strong class="d-block">💡 KITA — Kernel of Inventory, Talent & Asset</strong>
                        <span class="small text-secondary">Every incoming and outgoing item is neatly recorded. Optimize stock, reduce losses, and improve efficiency. KITA is here to support your operations!</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection