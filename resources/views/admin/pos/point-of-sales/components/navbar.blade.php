<nav class="pos-navbar" x-data="navbarComponent">
    <div class="container-fluid px-0 px-sm-2">
        <div class="row g-2 align-items-center">
            <!-- Logo / Brand -->
            <div class="col-6 col-md-3 d-flex align-items-center">
                <span class="brand"><i class="bi bi-shop"></i> Kita<span>POS</span></span>
                <span class="cart-badge d-md-none" x-text="$store.pos.cartCount">0</span>
            </div>

            <!-- Search Desktop -->
            <div class="col-md-4 d-none d-md-flex justify-content-md-start">
                <input type="text" class="search-box" id="searchMenuDesktop" placeholder="Search menu..."
                    x-model="$store.pos.searchQuery" @input.debounce="$store.pos.filterMenu()" />
            </div>

            <!-- Right Side Desktop -->
            <div class="col-md-5 d-none d-md-flex justify-content-between align-items-center">
                <!-- Left group -->
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

                <!-- ======= DROPDOWN KASIR (BOOTSTRAP) ======= -->
                <div class="dropdown" x-data="{ open: false }" @click.away="open = false">
                    <button class="dropdown-toggle d-flex align-items-center gap-2 bg-transparent border-0 text-white"
                            @click="open = !open"
                            type="button"
                            style="padding:4px 8px; border-radius:30px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08);">
                        <div class="avatar-circle"
                             :class="{ 'online': $store.pos.isCashierOnline, 'offline': !$store.pos.isCashierOnline }">
                            <span x-text="$store.pos.cashierInitials"></span>
                        </div>
                        <span class="fw-medium" x-text="$store.pos.cashierName"></span>
                        <i class="bi bi-chevron-down" :class="{ 'rotate-180': open }" style="font-size:10px;"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end" :class="{'show': open}" style="min-width:200px; padding:0;">
                        <!-- Header -->
                        <div class="dropdown-header d-flex align-items-center gap-2 border-bottom" style="padding:10px 16px;">
                            <div class="avatar-circle"
                                 :class="{ 'online': $store.pos.isCashierOnline, 'offline': !$store.pos.isCashierOnline }"
                                 style="width:36px; height:36px; font-size:14px;">
                                <span x-text="$store.pos.cashierInitials"></span>
                            </div>
                            <div>
                                <div class="fw-bold text-dark" x-text="$store.pos.cashierName"></div>
                                <div class="small">
                                    <span x-show="$store.pos.isCashierOnline" class="text-success">● Online</span>
                                    <span x-show="!$store.pos.isCashierOnline" class="text-danger">● Offline</span>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Item -->
                        <template x-if="$store.pos.isCashierOnline">
                            <button class="dropdown-item d-flex align-items-center gap-2"
                                    @click="$store.pos.closeCashier(); open = false;"
                                    style="padding:8px 16px;">
                                <i class="bi bi-box-arrow-right text-danger"></i>
                                <span>Tutup Kasir</span>
                            </button>
                        </template>
                        <template x-if="!$store.pos.isCashierOnline">
                            <button class="dropdown-item d-flex align-items-center gap-2"
                                    @click="$store.pos.openCashier(); open = false;"
                                    style="padding:8px 16px;">
                                <i class="bi bi-box-arrow-in-right text-success"></i>
                                <span>Buka Kasir</span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Mobile Layout -->
            <div class="col-6 d-md-none d-flex justify-content-between align-items-center gap-1">
                <!-- ======= DROPDOWN KASIR MOBILE (BOOTSTRAP) ======= -->
                <div class="dropdown" x-data="{ open: false }" @click.away="open = false">
                    <button class="dropdown-toggle d-flex align-items-center gap-1 bg-transparent border-0 text-white p-1"
                            @click="open = !open"
                            type="button"
                            style="border-radius:30px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08); padding:2px 8px 2px 4px;">
                        <div class="avatar-circle small"
                             :class="{ 'online': $store.pos.isCashierOnline, 'offline': !$store.pos.isCashierOnline }">
                            <span x-text="$store.pos.cashierInitials"></span>
                        </div>
                        <span class="fw-medium small" x-text="$store.pos.cashierName"></span>
                        <i class="bi bi-chevron-down" :class="{ 'rotate-180': open }" style="font-size:8px;"></i>
                    </button>

                    <div class="dropdown-menu" :class="{'show': open}" style="min-width:180px; padding:0; left:0; right:auto;">
                        <div class="dropdown-header d-flex align-items-center gap-2 border-bottom" style="padding:8px 14px;">
                            <div class="avatar-circle"
                                 :class="{ 'online': $store.pos.isCashierOnline, 'offline': !$store.pos.isCashierOnline }"
                                 style="width:32px; height:32px; font-size:12px;">
                                <span x-text="$store.pos.cashierInitials"></span>
                            </div>
                            <div>
                                <div class="fw-bold text-dark small" x-text="$store.pos.cashierName"></div>
                                <div class="small">
                                    <span x-show="$store.pos.isCashierOnline" class="text-success">● Online</span>
                                    <span x-show="!$store.pos.isCashierOnline" class="text-danger">● Offline</span>
                                </div>
                            </div>
                        </div>

                        <template x-if="$store.pos.isCashierOnline">
                            <button class="dropdown-item d-flex align-items-center gap-2"
                                    @click="$store.pos.openCloseModal(); open = false;"
                                    style="padding:6px 14px; font-size:13px;">
                                <i class="bi bi-box-arrow-right text-danger"></i>
                                <span>Close Cashier</span>
                            </button>
                        </template>
                        <template x-if="!$store.pos.isCashierOnline">
                            <button class="dropdown-item d-flex align-items-center gap-2"
                                    @click="$store.pos.openCashier(); open = false;"
                                    style="padding:6px 14px; font-size:13px;">
                                <i class="bi bi-box-arrow-in-right text-success"></i>
                                <span>Open Cashier</span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Mobile Action Buttons -->
                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-nav-icon" @click="$store.pos.toggleMobileCart()">
                        <i class="bi bi-cart3"></i> <span id="mobileCartBadge" class="badge bg-light"
                            style="color:#7a1a1a;" x-text="$store.pos.cartCount">0</span>
                    </button>
                    <button class="btn btn-sm btn-history-mobile" @click="$store.pos.openHistory()">
                        <i class="bi bi-clock-history"></i>
                    </button>
                    <button class="btn btn-sm btn-add-menu" @click="$store.pos.openAddMenu()">
                        <i class="bi bi-plus-circle"></i> <span class="d-none d-sm-inline">Add</span>
                    </button>
                </div>
            </div>

            <!-- Search Mobile -->
            <div class="col-12 d-md-none mt-1">
                <input type="text" class="search-box w-100" id="searchMenuMobile" placeholder="Search menu..."
                    x-model="$store.pos.searchQuery" @input.debounce="$store.pos.filterMenu()" />
            </div>
        </div>
    </div>
</nav>