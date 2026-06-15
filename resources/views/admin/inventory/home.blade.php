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
        <!-- Hero Section : Sambutan Hangat -->
        <div class="inventory-hero d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-4">
                <div class="greeting-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1" style="color: #14532d;">Inventory Management</h2>
                    <p class="mb-0 text-secondary">Halo, <strong>{{ auth()->user()->name }}</strong>! Kelola stok, barang
                        masuk/keluar, dan lihat laporan realtime.</p>
                    <div class="mt-2 small text-muted">
                        <i class="fas fa-cubes me-1"></i> KITA bantu pantau setiap pergerakan inventaris.
                    </div>
                </div>
            </div>
            <div class="text-muted">
                <i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </div>
        </div>

        <!-- Statistik Cepat (contoh, bisa diganti data real) -->
        <div class="row g-4 mb-5">
            <div class="col-md-3 col-6">
                <div class="card inventory-card p-3">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <h6 class="text-muted">Total Produk</h6>
                            <h3 class="fw-bold text-success mb-0">{{ number_format($stats['total_products']) }}</h3>
                            <small class="text-muted">produk terdaftar</small>
                        </div>
                        <div class="col-6">
                            <h6 class="text-muted">Total Varian</h6>
                            <h3 class="fw-bold text-primary mb-0">{{ number_format($stats['total_variants']) }}</h3>
                            <small class="text-muted">varian produk</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card inventory-card p-3 text-center">
                    <h6 class="text-muted">Stok Menipis (<=5)</h6>
                    <h3 class="fw-bold text-warning">{{ number_format($stats['low_stock_count']) }}</h3>
                    <small class="text-muted">perlu restok</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card inventory-card p-3 text-center">
                    <h6 class="text-muted">Barang Masuk (bulan ini)</h6>
                    <h3 class="fw-bold text-primary">{{ number_format($stats['incoming_transactions']) }}</h3>
                    <small class="text-muted">transaksi</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card inventory-card p-3 text-center">
                    <h6 class="text-muted">Barang Keluar (bulan ini)</h6>
                    <h3 class="fw-bold text-info">{{ number_format($stats['outgoing_transactions']) }}</h3>
                    <small class="text-muted">transaksi</small>
                </div>
            </div>
        </div>

        <!-- Menu Groups -->
        <div class="row g-4">
            <!-- Master Data -->
            <div class="col-xl-4 col-md-6">
                <div class="card inventory-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="card-icon mb-2"><i class="fas fa-database"></i></div>
                            <span class="badge-subtle">Data Induk</span>
                        </div>
                        <h5 class="fw-bold">Master Data</h5>
                        <p class="small text-secondary">Kelola kategori, produk, dan stok awal.</p>
                        <ul class="submenu-list">
                            <li><a href="{{ route('inventory.category.index') }}"><i class="fas fa-tag fa-fw"></i>
                                    Category</a></li>
                            <li><a href="{{ route('inventory.product.index') }}"><i class="fas fa-box-open fa-fw"></i>
                                    Product</a></li>
                            <li><a href="{{ route('inventory.stock.index') }}"><i class="fas fa-chart-line fa-fw"></i> Stock</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Incoming Goods -->
            <div class="col-xl-4 col-md-6">
                <div class="card inventory-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="card-icon mb-2"><i class="fas fa-download"></i></div>
                            <span class="badge-subtle">Penerimaan</span>
                        </div>
                        <h5 class="fw-bold">Incoming Goods</h5>
                        <p class="small text-secondary">Catat barang masuk ke gudang.</p>
                        <ul class="submenu-list">
                            <li><a href="#"><i class="fas fa-plus-circle fa-fw"></i> New Transaction</a></li>
                            <li><a href="#"><i class="fas fa-history fa-fw"></i> Transaction List</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Exit Items -->
            <div class="col-xl-4 col-md-6">
                <div class="card inventory-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="card-icon mb-2"><i class="fas fa-upload"></i></div>
                            <span class="badge-subtle">Pengeluaran</span>
                        </div>
                        <h5 class="fw-bold">Exit Items</h5>
                        <p class="small text-secondary">Catat barang keluar (penjualan, pemakaian).</p>
                        <ul class="submenu-list">
                            <li><a href="#"><i class="fas fa-plus-circle fa-fw"></i> New Transaction</a></li>
                            <li><a href="#"><i class="fas fa-history fa-fw"></i> Transaction List</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Return Items -->
            <div class="col-xl-4 col-md-6">
                <div class="card inventory-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="card-icon mb-2"><i class="fas fa-undo-alt"></i></div>
                            <span class="badge-subtle">Retur</span>
                        </div>
                        <h5 class="fw-bold">Return Items</h5>
                        <p class="small text-secondary">Proses retur barang dari pelanggan.</p>
                        <ul class="submenu-list">
                            <li><a href="#"><i class="fas fa-plus-circle fa-fw"></i> New Transaction</a></li>
                            <li><a href="#"><i class="fas fa-history fa-fw"></i> Transaction List</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Stock Opnames -->
            <div class="col-xl-4 col-md-6">
                <div class="card inventory-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="card-icon mb-2"><i class="fas fa-clipboard-list"></i></div>
                            <span class="badge-subtle">Stok Opname</span>
                        </div>
                        <h5 class="fw-bold">Stock Opnames</h5>
                        <p class="small text-secondary">Lakukan pengecekan fisik stok.</p>
                        <ul class="submenu-list">
                            <li><a href="#"><i class="fas fa-file-alt fa-fw"></i> New Report</a></li>
                            <li><a href="#"><i class="fas fa-chart-bar fa-fw"></i> Reports</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Reports -->
            <div class="col-xl-4 col-md-6">
                <div class="card inventory-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="card-icon mb-2"><i class="fas fa-chart-pie"></i></div>
                            <span class="badge-subtle">Analisa</span>
                        </div>
                        <h5 class="fw-bold">Reports</h5>
                        <p class="small text-secondary">Pantau pergerakan stok secara periodik.</p>
                        <ul class="submenu-list">
                            <li><a href="#"><i class="fas fa-sun fa-fw"></i> Daily</a></li>
                            <li><a href="#"><i class="fas fa-calendar-week fa-fw"></i> Weekly</a></li>
                            <li><a href="#"><i class="fas fa-calendar-alt fa-fw"></i> Monthly</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Motivasi KITA -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-light border rounded-4 shadow-sm d-flex align-items-center gap-3"
                    style="background: #ffffff;">
                    <i class="fas fa-heart text-danger fa-2x"></i>
                    <div>
                        <strong class="d-block">💡 KITA — Kernel of Inventory, Talent & Asset</strong>
                        <span class="small text-secondary">Setiap barang yang masuk dan keluar tercatat rapi. Optimalkan
                            stok, kurangi kerugian, dan tingkatkan efisiensi. KITA di sini untuk mendukung
                            operasionalmu!</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
