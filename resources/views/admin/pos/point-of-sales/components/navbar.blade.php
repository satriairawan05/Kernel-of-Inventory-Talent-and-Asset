<nav class="pos-navbar" x-data="navbarComponent">
    <div class="container-fluid px-0 px-sm-2">
        <div class="row g-2 align-items-center">
            <!-- === LOGO (Desktop & Mobile) === -->
            <div class="col-6 col-md-3 d-flex align-items-center">
                <span class="brand"><i class="bi bi-shop"></i> Kita<span>POS</span></span>
                <span class="cart-badge d-md-none" x-text="$store.pos.cartCount">0</span>
            </div>

            <!-- === SEARCH (Desktop) === -->
            <div class="col-md-4 d-none d-md-flex justify-content-md-start">
                <input type="text" class="search-box" id="searchMenuDesktop" placeholder="Search menu..."
                    x-model="$store.pos.searchQuery" @input.debounce="$store.pos.filterMenu()" />
            </div>

            <!-- === DESKTOP NAVIGATION === -->
            <div class="col-md-5 d-none d-md-flex justify-content-between align-items-center">
                <!-- Action Buttons -->
                <div class="d-flex align-items-center gap-2">
                    <button class="home-nav-btn" @click="$store.pos.goHome()" title="Main Menu">
                        <i class="bi bi-house-fill"></i>
                    </button>
                    <button class="calc-nav-btn" @click="$store.pos.openCalculator()" title="Calculator">
                        <i class="bi bi-calculator"></i> Calculator
                    </button>
                    <button class="history-nav-btn" @click="$store.pos.openHistory()" title="History">
                        <i class="bi bi-clock-history"></i> History
                    </button>
                    <i class="bi bi-cart3 fs-5" style="color:#ffc107;"></i>
                    <span class="fw-700" style="color:#fff;">Cart</span>
                    <span class="cart-badge" x-text="$store.pos.cartCount">0</span>
                </div>

                <!-- ===== CASHIER INDICATOR (Desktop) ===== -->
                <div class="d-flex align-items-center gap-2">
                    <span class="cashier-indicator"
                        :class="{ 'online': $store.pos.isCashierOnline, 'offline': !$store.pos.isCashierOnline }">
                        <span class="indicator-dot"></span>
                        <span class="cashier-name" x-text="$store.pos.cashierName"></span>
                    </span>
                </div>
            </div>

            <!-- === MOBILE NAVIGATION === -->
            <div class="col-6 d-md-none d-flex justify-content-end align-items-center" style="gap: 4px; padding: 0;">
                <!-- Indicator (kiri) -->
                <span class="cashier-indicator mobile"
                    :class="{ 'online': $store.pos.isCashierOnline, 'offline': !$store.pos.isCashierOnline }"
                    :data-cashier="$store.pos.cashierName" title="{{ auth()->user()->name ?? 'Kasir' }}">
                    <span class="indicator-dot"></span>
                    <span class="cashier-name" x-text="$store.pos.cashierName"></span>
                </span>

                <!-- Tombol aksi (kanan, rapat) -->
                <div class="d-flex align-items-center" style="gap: 2px;">
                    <button class="btn btn-sm btn-nav-icon" @click="$store.pos.toggleMobileCart()"
                        style="padding: 4px 6px;">
                        <i class="bi bi-cart3"></i>
                        <span id="mobileCartBadge" class="badge bg-light"
                            style="color:#7a1a1a; font-size: 0.6rem; padding: 1px 5px;"
                            x-text="$store.pos.cartCount">0</span>
                    </button>
                    <button class="btn btn-sm btn-history-mobile" @click="$store.pos.openHistory()"
                        style="padding: 4px 6px;">
                        <i class="bi bi-clock-history"></i>
                    </button>
                    <button class="btn btn-sm btn-add-menu" @click="$store.pos.openAddMenu()" style="padding: 4px 6px;">
                        <i class="bi bi-plus-circle"></i>
                    </button>
                </div>
            </div>

            <!-- === SEARCH (Mobile) === -->
            <div class="col-12 d-md-none mt-1">
                <input type="text" class="search-box w-100" id="searchMenuMobile" placeholder="Search menu..."
                    x-model="$store.pos.searchQuery" @input.debounce="$store.pos.filterMenu()" />
            </div>
        </div>
    </div>
</nav>
