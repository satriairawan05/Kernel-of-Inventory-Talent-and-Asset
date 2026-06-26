@extends('admin.layouts.app')

@push('css')
    <style>
        /* ===== HERO ===== */
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

        .top-menu-card .card-header {
            background: #fefce8;
            border-bottom: 2px solid #fde68a;
            padding: 0.75rem 1.5rem;
            border-radius: 1.5rem 1.5rem 0 0;
            flex-wrap: wrap;
        }

        .top-menu-card .card-header h5 {
            color: #78350f;
            font-weight: 700;
            margin: 0;
            font-size: 1.1rem;
        }

        .top-menu-card .card-body {
            padding: 0.5rem 0.75rem;
        }

        .top-menu-card .table-wrap {
            max-width: 820px;
            margin: 0 auto;
            padding: 0 0.5rem;
        }

        .top-menu-card .table {
            margin-bottom: 0;
            width: 100%;
        }

        .top-menu-card .table th {
            font-weight: 600;
            color: #78350f;
            border-bottom: 2px solid #fde68a;
            padding: 0.75rem 0.75rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: center;
        }

        .top-menu-card .table th:first-child {
            width: 50px;
        }

        .top-menu-card .table th:nth-child(2) {
            text-align: left;
            padding-left: 1rem;
        }

        .top-menu-card .table th:nth-child(3),
        .top-menu-card .table th:nth-child(4) {
            text-align: center;
        }

        .top-menu-card .table td {
            padding: 0.7rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid #fef3c7;
            text-align: center;
        }

        .top-menu-card .table td:nth-child(2) {
            text-align: left;
            padding-left: 1rem;
        }

        .top-menu-card .table tbody tr:last-child td {
            border-bottom: none;
        }

        .top-menu-card .table tbody tr:hover {
            background-color: #fffbeb;
        }

        .rank-number {
            display: inline-block;
            width: 30px;
            height: 30px;
            line-height: 30px;
            border-radius: 50%;
            font-weight: 700;
            font-size: 0.85rem;
            color: #fff;
            text-align: center;
            background: #d1d5db;
        }

        .rank-1 {
            background: #f59e0b;
        }

        .rank-2 {
            background: #9ca3af;
        }

        .rank-3 {
            background: #d97706;
        }

        .rank-4 {
            background: #9ca3af;
        }

        .rank-5 {
            background: #9ca3af;
        }

        .menu-name {
            font-weight: 600;
            color: #1f2937;
        }

        .sold-number {
            font-weight: 700;
            font-size: 1rem;
            color: #1f2937;
        }

        .sold-unit {
            font-size: 0.7rem;
            color: #9ca3af;
            font-weight: 400;
            margin-left: 2px;
        }

        .progress-sold {
            height: 6px;
            background-color: #fef3c7;
            border-radius: 10px;
            overflow: hidden;
            max-width: 120px;
            margin: 0 auto;
        }

        .progress-sold .progress-bar {
            height: 100%;
            border-radius: 10px;
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
            transition: width 0.6s ease;
        }

        .table-footer-total {
            background: #fefce8;
            border-top: 2px solid #fde68a;
        }

        .table-footer-total td {
            padding: 0.75rem 0.75rem;
            font-weight: 700;
            color: #78350f;
            text-align: center;
        }

        .table-footer-total td:first-child {
            text-align: right;
            padding-right: 1.5rem;
        }

        .table-footer-total .total-value {
            font-size: 1.1rem;
            color: #065f46;
        }

        .month-nav-btn {
            background: transparent;
            border: 1px solid #fde68a;
            border-radius: 30px;
            padding: 0.25rem 0.8rem;
            font-size: 0.8rem;
            color: #78350f;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .month-nav-btn:hover {
            background: #fde68a;
            border-color: #f59e0b;
        }

        .month-label {
            font-weight: 600;
            color: #78350f;
            font-size: 0.9rem;
            min-width: 110px;
            text-align: center;
        }

        .category-filter {
            border: 1px solid #fde68a;
            border-radius: 30px;
            padding: 0.2rem 0.8rem;
            font-size: 0.8rem;
            background: #fffbeb;
            color: #78350f;
            outline: none;
        }

        .category-filter:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);
        }

        @media (max-width: 767.98px) {
            .top-menu-card .card-header {
                padding: 0.5rem 1rem;
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.3rem;
            }

            .top-menu-card .card-header h5 {
                font-size: 1rem;
            }

            .top-menu-card .table-wrap {
                padding: 0 0.25rem;
            }

            .top-menu-card .table th,
            .top-menu-card .table td {
                padding: 0.45rem 0.35rem;
                font-size: 0.75rem;
            }

            .top-menu-card .table th:first-child,
            .top-menu-card .table td:first-child {
                width: 35px;
            }

            .top-menu-card .table th:nth-child(2),
            .top-menu-card .table td:nth-child(2) {
                padding-left: 0.4rem;
            }

            .rank-number {
                width: 24px;
                height: 24px;
                line-height: 24px;
                font-size: 0.7rem;
            }

            .sold-number {
                font-size: 0.85rem;
            }

            .progress-sold {
                max-width: 50px;
                height: 4px;
            }

            .table-footer-total td {
                font-size: 0.75rem;
                padding: 0.4rem 0.35rem;
            }

            .table-footer-total .total-value {
                font-size: 0.9rem;
            }

            .progress-col {
                display: none;
            }

            .month-nav-btn {
                font-size: 0.7rem;
                padding: 0.15rem 0.6rem;
            }

            .month-label {
                font-size: 0.8rem;
                min-width: 90px;
            }

            .category-filter {
                font-size: 0.7rem;
                padding: 0.15rem 0.6rem;
            }
        }

        @media (min-width: 768px) and (max-width: 991.98px) {
            .top-menu-card .table-wrap {
                max-width: 100%;
                padding: 0 1rem;
            }

            .top-menu-card .table th,
            .top-menu-card .table td {
                padding: 0.6rem 0.6rem;
                font-size: 0.85rem;
            }

            .rank-number {
                width: 28px;
                height: 28px;
                line-height: 28px;
                font-size: 0.8rem;
            }

            .progress-sold {
                max-width: 80px;
            }
        }

        @media (min-width: 992px) {
            .top-menu-card .table-wrap {
                max-width: 780px;
            }

            .top-menu-card .table th,
            .top-menu-card .table td {
                padding: 0.8rem 1rem;
                font-size: 0.9rem;
            }

            .rank-number {
                width: 32px;
                height: 32px;
                line-height: 32px;
                font-size: 0.9rem;
            }

            .sold-number {
                font-size: 1.05rem;
            }

            .progress-sold {
                max-width: 110px;
                height: 6px;
            }

            .table-footer-total .total-value {
                font-size: 1.15rem;
            }
        }
    </style>
@endpush

@push('js')
    @php
        $companyId = auth()->user()->company_id ?? 2;

        $companyMenus = [
            1 => [
                'categories' => ['Seafood', 'Nasi', 'Ayam', 'Sate'],
                'items' => [
                    'Seafood' => [
                        'Kerang Hijau',
                        'Kerang Simping',
                        'Kerang Dara',
                        'Cumi',
                        'Kepiting',
                        'Kepiting Mix'
                    ],
                    'Nasi' => [
                        'Nasi Goreng Seafood',
                        'Nasi Goreng Biasa',
                        'Nasi Goreng Spesial',
                        'Nasi Goreng Ayam Geprek',
                        'Nasi Goreng Kampung'
                    ],
                    'Ayam' => [
                        'Ayam Ganza Besar',
                        'Ayam Ganza Kecil',
                        'Ayam Lalapan Kecil',
                        'Ayam Lalapan Besar',
                        'Paket Hemat',
                    ],
                    'Sate' => [
                        'Sate Hati',
                        'Sate Usus',
                        'Sate Ceker',
                        'Kepala Ayam',
                        'Sate Brutu'
                    ]
                ]
            ],
            2 => [
                'categories' => ['Ayam', 'Drink', 'Snack', 'Additional'],
                'items' => [
                    'Ayam' => [
                        'Ayam Geprek',
                        'Ayam Geprek Keju',
                        'Ayam Lada Hitam',
                        'Ayam Saus BBQ',
                        'Ayam Keju'
                    ],
                    'Drink' => ['Es Teh', 'Es Teh Manis', 'Es Jeruk','Jus Apel', 'Kopi Hitam'],
                    'Snack' => ['Kentang Goreng Kecil', 'Kentang Goreng Besar', 'Nugget Kecil', 'Nugget Besar','Singkong Goreng'],
                    'Additional' => ['Saus BBQ', 'Saus Lada Hitam', 'Saus Keju', 'Chili Oil','Kecap Manis']
                ]
            ]
        ];

        // Pilih menu berdasarkan company_id
        $currentCompany = $companyMenus[$companyId] ?? $companyMenus[2];
        $categories = $currentCompany['categories'];
        $baseItems = $currentCompany['items'];

        // Generate item list dengan variasi saus untuk seafood (jika company_id == 1)
        $allItems = [];
        $sauceVariations = ['Asam Manis', 'Saus Padang', 'Lada Hitam'];

        foreach ($baseItems as $category => $items) {
            foreach ($items as $item) {
                // Jika category Seafood dan company_id == 1, tambahkan variasi saus
                if ($companyId == 1 && $category == 'Seafood') {
                    foreach ($sauceVariations as $sauce) {
                        $allItems[] = [
                            'name' => $item . ' ' . $sauce,
                            'category' => $category
                        ];
                    }
                } else {
                    $allItems[] = [
                        'name' => $item,
                        'category' => $category
                    ];
                }
            }
        }

        // Fungsi untuk generate data bulan dengan setiap item ~1000 pcs
        function generateMonthData($monthOffset, $allItems)
        {
            $date = \Carbon\Carbon::now()->subMonths($monthOffset);
            $label = $date->translatedFormat('F Y');

            $items = [];
            foreach ($allItems as $item) {
                $variation = rand(-35, 55);
                $sold = max(0, 900 + $variation);
                $items[] = [
                    'name' => $item['name'],
                    'total_sold' => $sold,
                    'category' => $item['category']
                ];
            }

            // Sort by sold descending
            usort($items, fn($a, $b) => $b['total_sold'] - $a['total_sold']);

            return [
                'label' => $label,
                'items' => $items
            ];
        }

        $monthsData = [];
        for ($i = 0; $i < 3; $i++) {
            $monthsData[] = generateMonthData($i, $allItems);
        }

        // Tambahkan opsi kategori "All"
        $categoryOptions = array_merge(['All'], $categories);
    @endphp

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    <script type="text/javascript">
        document.addEventListener('alpine:init', () => {
            Alpine.data('topMenu', () => ({
                months: @json($monthsData),
                currentIndex: 0,
                selectedCategory: 'All',
                categories: @json($categoryOptions),

                get currentMonth() {
                    return this.months[this.currentIndex] || this.months[0];
                },

                get filteredItems() {
                    if (!this.currentMonth || !this.currentMonth.items) return [];
                    if (this.selectedCategory === 'All') {
                        return this.currentMonth.items.slice(0, 5); // Top 5 all categories
                    }
                    const filtered = this.currentMonth.items
                        .filter(item => item.category === this.selectedCategory)
                        .slice(0, 5);
                    return filtered;
                },

                get maxSold() {
                    if (!this.filteredItems.length) return 0;
                    return Math.max(...this.filteredItems.map(item => item.total_sold));
                },

                get totalAll() {
                    if (!this.filteredItems.length) return 0;
                    return this.filteredItems.reduce((sum, item) => sum + item.total_sold, 0);
                },

                goPrev() {
                    if (this.currentIndex > 0) this.currentIndex--;
                },

                goNext() {
                    if (this.currentIndex < this.months.length - 1) this.currentIndex++;
                },

                formatNumber(value) {
                    return Number(value).toLocaleString('id-ID');
                }
            }));
        });
    </script>
@endpush

@section('content')
    <div class="container-fluid px-4 py-3">

        <!-- ===== HERO ===== -->
        <div class="sales-hero d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-4">
                <div class="greeting-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1" style="color: #065f46;">Sales Management</h2>
                    <p class="mb-0 text-secondary">Hello, <strong>{{ auth()->user()->name }}</strong>! Monitor sales, create
                        transactions, and view realtime reports.</p>
                    <div class="mt-2 small text-muted">
                        <i class="fas fa-cash-register me-1"></i> KITA helps boost revenue and cashier efficiency.
                    </div>
                </div>
            </div>
            <div class="text-muted">
                <i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </div>
        </div>

        <!-- ===== ALERT ===== -->
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

        <!-- ===== QUICK STATS ===== -->
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

        <!-- ===== SALES MENU CARDS ===== -->
        <div class="row g-4 mb-4">
            @if ($access['POS']['Read'] == 1)
                <div class="col-xl-6 col-md-6">
                    <div class="card sales-card">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="card-icon mb-2"><i class="fas fa-cash-register"></i></div>
                                <span class="badge-subtle">Active</span>
                            </div>
                            <h5 class="fw-bold">Point of Sales</h5>
                            <p class="small text-secondary">Process direct sales transactions, manage cart, discounts, and
                                print receipts.</p>
                            <div class="info-row">
                                <a href="#" class="small text-success fw-semibold">Start Transaction <i
                                        class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($access['Sale Reports (POS)']['Read'] == 1)
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

        <!-- ===== TOP 5 BEST SELLING MENU DENGAN FILTER KATEGORI & NAVIGASI BULAN ===== -->
        <div class="row mb-4" x-data="topMenu">
            <div class="col-12">
                <div class="card sales-card top-menu-card">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                        <h5>
                            <i class="fas fa-trophy text-warning me-2"></i>Top 5 Best Selling Menu
                        </h5>
                        <div class="d-flex align-items-center gap-2">
                            <!-- Pilihan Kategori -->
                            <select class="category-filter" x-model="selectedCategory">
                                <template x-for="cat in categories" :key="cat">
                                    <option x-text="cat" :value="cat"></option>
                                </template>
                            </select>

                            <!-- Navigasi Bulan -->
                            <template x-if="currentIndex > 0">
                                <button class="month-nav-btn" @click="goPrev">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                            </template>
                            <span class="month-label" x-text="currentMonth.label"></span>
                            <template x-if="currentIndex < months.length - 1">
                                <button class="month-nav-btn" @click="goNext">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </template>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-wrap">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Menu</th>
                                            <th>Sold</th>
                                            <th class="progress-col">Trend</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-if="filteredItems.length > 0">
                                            <template x-for="(item, index) in filteredItems" :key="index">
                                                <tr>
                                                    <td>
                                                        <span class="rank-number" :class="'rank-' + (index + 1)"
                                                            x-text="index + 1"></span>
                                                    </td>
                                                    <td>
                                                        <span class="menu-name" x-text="item.name"></span>
                                                    </td>
                                                    <td>
                                                        <span class="sold-number"
                                                            x-text="formatNumber(item.total_sold)"></span>
                                                        <span class="sold-unit">pcs</span>
                                                    </td>
                                                    <td class="progress-col">
                                                        <div class="progress-sold">
                                                            <div class="progress-bar"
                                                                :style="'width: ' + ((item.total_sold / maxSold) * 100) + '%;'">
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                        </template>
                                        <template x-if="filteredItems.length === 0">
                                            <tr>
                                                <td colspan="4" class="text-center py-3 text-muted">
                                                    No items found for this category.
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                    <tfoot class="table-footer-total" x-show="filteredItems.length > 0">
                                        <tr>
                                            <td colspan="2" class="text-end fw-bold">Total All 5 Menus</td>
                                            <td>
                                                <span class="total-value" x-text="formatNumber(totalAll)"></span>
                                                <span class="sold-unit">pcs</span>
                                            </td>
                                            <td class="progress-col"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== KITA MOTIVATION ===== -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-light border rounded-4 shadow-sm d-flex align-items-center gap-3"
                    style="background: #ffffff;">
                    <i class="fas fa-heart text-danger fa-2x"></i>
                    <div>
                        <strong class="d-block">💡 KITA — Kernel of Inventory, Talent & Asset</strong>
                        <span class="small text-secondary">Sales are the lifeblood of business. Monitor every transaction,
                            identify trends, and maximize profits with accurate data. KITA is ready to support your sales
                            success!</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection