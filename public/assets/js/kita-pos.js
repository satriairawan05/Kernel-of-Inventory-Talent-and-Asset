// ================================================================
// assets/js/kita-pos.js - KitaPOS with Alpine.js
// All data from API (jQuery AJAX), no hardcoded menus.
// Multiple Draft Sessions (Dine In / Take Away) - Database persisted
// Cart management integrated with API
// Stock status realtime check
// Print via Bluetooth (default) or Server (ESC/POS)
// Cashier Session (Open / Close) integrated with modal
// ================================================================

document.addEventListener("alpine:init", function () {
    Alpine.store("pos", {
        // ---- STATE ----
        menuItems: [],
        nextId: 1,
        openingBalance: 0,
        sessions: [],
        activeSessionId: null,
        selectedSession: null,
        cart: [],
        cartId: null,
        transactionHistory: [],
        currentCategory: "all",
        searchQuery: "",
        mobileCartOpen: false,
        toastMessage: "Notification",
        draftLoading: false,

        _csrfRefreshing: false,
        _previousCashier: null,

        // Cashier
        cashierName: "Guest",
        isCashierOnline: false,

        // Outlet
        outletName: "My Fried Chicken",
        outletAddress: "Pusat",
        outletId: 1,

        // Calculator
        calcExpression: "",
        calcDisplay: "0",

        // New/Edit item
        newItem: {
            name: "",
            price: "",
            category: "food",
            status: "available",
            icon: "🍽️",
            imagePreview: null,
            imageData: null,
        },
        editItemId: null,
        editItem: {
            name: "",
            price: "",
            category: "food",
            status: "available",
            icon: "🍽️",
            imagePreview: null,
            imageData: null,
        },
        editOpeningBalance: "",

        // Checkout
        paymentMethod: "cash",
        paymentAmount: "",
        paymentAmountRaw: 0,
        changeAmount: 0,
        discountType: "rp",
        discountValue: 0,
        discountDisplay: "0",

        // Printer
        defaultPrinterSize: "58mm",
        printMethod: "auto", // 'bluetooth' | 'server' | 'auto'
        strukData: {
            id: "",
            timestamp: "",
            items: [],
            total: 0,
            totalQty: 0,
            paid: 0,
            change: 0,
            method: "Cash",
            discount: 0,
            subtotal: 0,
        },

        toast: null,

        // New session modal
        newSessionType: "dinein",
        newSessionTable: "",
        loading: false,
        apiError: false,

        // ---- STOCK STATUS CACHE ----
        stockStatusCache: {},

        // ---- MODAL CLOSE CASHIER STATE ----
        closeModalOpen: false,
        closingBalanceInput: "",
        shiftSummary: {
            session_id: null,
            opening_balance: 0,
            total_transactions: 0,
            total_sales: 0,
            payment_summary: {}
        },

        // ---- COMPUTED ----
        get filteredMenu() {
            var items = this.menuItems;
            if (this.currentCategory !== "all") {
                items = items.filter(
                    function (item) {
                        return item.category === this.currentCategory;
                    }.bind(this),
                );
            }
            if (this.searchQuery.trim()) {
                var q = this.searchQuery.trim().toLowerCase();
                items = items.filter(function (item) {
                    return item.name.toLowerCase().indexOf(q) !== -1;
                });
            }
            return items;
        },

        // ---- DRAFT (sessions) helpers ----
        getTotalSessionsCount: function () {
            return this.sessions.reduce(function (sum, s) {
                return (
                    sum +
                    s.items.reduce(function (acc, i) {
                        return acc + i.qty;
                    }, 0)
                );
            }, 0);
        },
        getTotalSessionsTotal: function () {
            return this.sessions.reduce(
                function (sum, s) {
                    return sum + this.getSessionTotal(s.id);
                }.bind(this),
                0,
            );
        },
        getSessionTotal: function (sessionId) {
            var session = this.sessions.find(function (s) {
                return s.id === sessionId;
            });
            if (!session) return 0;
            if (session.subtotal !== undefined && session.subtotal !== null) {
                return this.toNumber(session.subtotal);
            }
            return session.items.reduce(
                function (sum, item) {
                    return (
                        sum +
                        this.toNumber(item.price) * this.toNumber(item.qty)
                    );
                }.bind(this),
                0,
            );
        },
        getDraftQty: function (id) {
            var session = this.sessions.find(
                function (s) {
                    return s.id === this.activeSessionId;
                }.bind(this),
            );
            if (!session) return 0;
            var item = session.items.find(function (i) {
                return i.menu_item_id === id;
            });
            return item ? item.qty : 0;
        },
        getDisplayDraftQty: function (id) {
            var qty = this.getDraftQty(id);
            return qty > 0 ? qty : 1;
        },

        getCartQty: function (id) {
            var item = this.cart.find(function (c) {
                return c.id === id;
            });
            return item ? item.qty : 0;
        },
        getDisplayQty: function (id) {
            var qty = this.getCartQty(id);
            return qty > 0 ? qty : 1;
        },

        // ---- HISTORY COMPUTED ----
        get totalRevenue() {
            return this.transactionHistory.reduce(
                (sum, trx) => sum + (trx.total || 0),
                0,
            );
        },
        get grandTotal() {
            return this.openingBalance + this.totalRevenue;
        },
        get totalItemsSold() {
            return this.transactionHistory.reduce((sum, trx) => {
                return sum + trx.items.reduce((s, item) => s + item.qty, 0);
            }, 0);
        },

        // ---- CASHIER ----
        setCashier: function (name, online) {
            if (online === undefined) online = true;
            var previous = this.cashierName;
            this.cashierName = name || "Guest";
            this.isCashierOnline = online;
            try {
                localStorage.setItem("cashierName", this.cashierName);
                localStorage.setItem(
                    "isCashierOnline",
                    JSON.stringify(this.isCashierOnline),
                );
            } catch (e) { }
            if (previous !== this.cashierName || !this._previousCashier) {
                this._previousCashier = this.cashierName;
                setTimeout(
                    function () {
                        this.refreshCsrfToken();
                    }.bind(this),
                    100,
                );
            }
        },
        loadCashier: function () {
            try {
                var name = localStorage.getItem("cashierName");
                var online = localStorage.getItem("isCashierOnline");
                var sessionId = localStorage.getItem("activeSessionId");
                if (name) this.cashierName = name;
                if (online !== null) this.isCashierOnline = JSON.parse(online);
                if (sessionId) this.activeSessionId = parseInt(sessionId, 10);
            } catch (e) { }
        },

        // ---- GET CSRF TOKEN HELPER ----
        getCsrfToken: function () {
            var token = document.querySelector('meta[name="csrf-token"]');
            if (token) {
                return token.getAttribute("content");
            }
            return "";
        },

        // ---- REFRESH CSRF TOKEN ----
        refreshCsrfToken: function () {
            if (this._csrfRefreshing) {
                return;
            }
            this._csrfRefreshing = true;
            var self = this;
            $.ajax({
                url: "/refresh-csrf",
                type: "GET",
                dataType: "json",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": this.getCsrfToken(),
                },
                success: function (response) {
                    if (response.success) {
                        var newToken = response.data.csrf_token;
                        $('meta[name="csrf-token"]').attr("content", newToken);
                        $.ajaxSetup({
                            headers: {
                                "X-CSRF-TOKEN": newToken,
                            },
                        });
                        self.showToast("🔑 Security token updated");
                    } else {
                        self.showToast("❌ Failed to refresh token");
                    }
                    self._csrfRefreshing = false;
                },
                error: function (xhr) {
                    self.showToast("❌ Failed to refresh security token.");
                    self._csrfRefreshing = false;
                },
            });
        },

        // ---- HELPER TO NUMBER ----
        toNumber: function (value) {
            if (value === undefined || value === null) return 0;
            if (typeof value === "number") return value;
            if (typeof value === "string") {
                var str = value.replace(/,/g, "");
                var lastDotIndex = str.lastIndexOf(".");
                if (lastDotIndex !== -1) {
                    var afterDot = str.substring(lastDotIndex + 1);
                    if (afterDot.length === 2) {
                        var num = parseFloat(str);
                        return Math.round(num);
                    }
                }
                var cleaned = str.replace(/\./g, "");
                return parseInt(cleaned, 10) || 0;
            }
            return 0;
        },

        // ---- FORMAT RUPIAH ----
        formatRupiah: function (angka) {
            if (angka === undefined || angka === null) return "0";
            var num = this.toNumber(angka);
            if (isNaN(num) || num === 0) return "0";
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },

        // ---- CHECK ADMIN ----
        isAdmin: function () {
            return (
                window.KitaPOS &&
                window.KitaPOS.user &&
                window.KitaPOS.user.group_id === 1
            );
        },

        // ---- INIT ----
        init: function () {
            try {
                this.loadCashier();
                var storedOB = null;
                try {
                    storedOB = localStorage.getItem("openingBalance");
                } catch (e) { }
                this.openingBalance =
                    storedOB !== null ? parseInt(storedOB, 10) || 0 : 150000;
                try {
                    localStorage.setItem(
                        "openingBalance",
                        this.openingBalance.toString(),
                    );
                } catch (e) { }

                this._loadMenuFromAPI();

                try {
                    var storedHistory =
                        localStorage.getItem("transactionHistory");
                    if (storedHistory) {
                        this.transactionHistory = JSON.parse(storedHistory);
                    }
                } catch (e) { }

                try {
                    var savedSize = localStorage.getItem("defaultPrinterSize");
                    this.defaultPrinterSize = savedSize || "58mm";
                    localStorage.setItem(
                        "defaultPrinterSize",
                        this.defaultPrinterSize,
                    );
                } catch (e) { }

                try {
                    var savedMethod = localStorage.getItem("printMethod");
                    this.printMethod = savedMethod || "auto";
                } catch (e) { }

                var toastEl = document.getElementById("liveToast");
                if (
                    toastEl &&
                    typeof bootstrap !== "undefined" &&
                    bootstrap.Toast
                ) {
                    this.toast = new bootstrap.Toast(toastEl, { delay: 2500 });
                }

                this.loadDraftsFromAPI();
                this.loadCartFromAPI();

                setTimeout(
                    function () {
                        this.refreshCsrfToken();
                    }.bind(this),
                    500,
                );
            } catch (error) {
                console.error("Init error:", error);
                if (this.menuItems.length === 0) {
                    this.menuItems = [];
                    this.nextId = 1;
                }
            }
        },

        // ===== LOAD MENU FROM API =====
        _loadMenuFromAPI: function () {
            var self = this;
            var companyId = window.KitaPOS?.outlet?.id || 1;
            var url = "/api/menu?company_id=" + companyId;

            this.loading = true;
            this.apiError = false;

            console.log("[KitaPOS] Loading menu from:", url);

            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                headers: { Accept: "application/json" },
                success: function (response) {
                    console.log("[KitaPOS] Menu API response:", response);
                    if (
                        response &&
                        response.success &&
                        Array.isArray(response.data)
                    ) {
                        self.menuItems = response.data.sort(function (a, b) {
                            if (
                                a.category === "additional" &&
                                b.category !== "additional"
                            )
                                return 1;
                            if (
                                a.category !== "additional" &&
                                b.category === "additional"
                            )
                                return -1;
                            return a.name.localeCompare(b.name);
                        });
                        var maxId = 0;
                        self.menuItems.forEach(function (item) {
                            if (item.id > maxId) maxId = item.id;
                        });
                        self.nextId = maxId + 1;

                        try {
                            self.refreshAllStockStatus();
                        } catch (e) {
                            console.warn(
                                "[KitaPOS] Stock status refresh failed:",
                                e,
                            );
                        }

                        self.showToast(
                            "✅ Menu loaded (" +
                            self.menuItems.length +
                            " items)",
                        );
                    } else {
                        console.error(
                            "[KitaPOS] Invalid menu response:",
                            response,
                        );
                        self.menuItems = [];
                        self.nextId = 1;
                        self.apiError = true;
                        self.showToast(
                            "⚠️ Failed to load menu: invalid response",
                        );
                    }
                    self.loading = false;
                },
                error: function (xhr, status, error) {
                    console.error(
                        "[KitaPOS] Menu AJAX error:",
                        status,
                        error,
                        xhr,
                    );
                    self.menuItems = [];
                    self.nextId = 1;
                    self.apiError = true;
                    self.loading = false;
                    var errorMsg = "⚠️ Failed to load menu from server. ";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg += xhr.responseJSON.message;
                    } else if (xhr.status === 500) {
                        errorMsg += "Internal server error. Check logs.";
                    } else {
                        errorMsg += "Please refresh the page.";
                    }
                    self.showToast(errorMsg);
                },
            });
        },

        // ============================================================
        // STOCK STATUS (Real-time)
        // ============================================================

        fetchStockStatus: function (menuItemId, callback) {
            var self = this;
            var csrfToken = this.getCsrfToken();

            if (this.stockStatusCache[menuItemId]) {
                if (callback) callback(this.stockStatusCache[menuItemId]);
                return;
            }

            $.ajax({
                url: "/api/menu/" + menuItemId + "/stock",
                type: "GET",
                dataType: "json",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                success: function (response) {
                    if (response && response.success && response.data) {
                        self.stockStatusCache[menuItemId] = {
                            stock: response.data.stock || 0,
                            status: response.data.status || "available",
                            status_label:
                                response.data.status_label || "Available",
                        };
                        var menuItem = self.menuItems.find(function (mi) {
                            return mi.id === menuItemId;
                        });
                        if (menuItem) {
                            menuItem.status = response.data.status;
                            menuItem.stock = response.data.stock;
                            menuItem.stock_status = response.data.status;
                        }
                        if (callback)
                            callback(self.stockStatusCache[menuItemId]);
                    } else {
                        if (callback) callback(null);
                    }
                },
                error: function (xhr) {
                    console.warn(
                        "[KitaPOS] Stock fetch failed for item " + menuItemId,
                        xhr.status,
                    );
                    if (callback) callback(null);
                },
            });
        },

        refreshAllStockStatus: function () {
            var self = this;
            this.stockStatusCache = {};
            this.menuItems.forEach(function (item) {
                self.fetchStockStatus(item.id, function (data) { });
            });
        },

        refreshStockStatus: function () {
            this.stockStatusCache = {};
            this.refreshAllStockStatus();
            this.showToast("🔄 Stock status updated");
        },

        // ============================================================
        // CART API METHODS
        // ============================================================

        loadCartFromAPI: function () {
            var self = this;
            var csrfToken = this.getCsrfToken();

            $.ajax({
                url: "/api/cart",
                type: "GET",
                dataType: "json",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                success: function (response) {
                    if (response && response.success && response.data) {
                        var cartData = response.data;
                        self.cartId = cartData.id;
                        self.cart = cartData.items.map(function (item) {
                            var menuItem = self.menuItems.find(function (mi) {
                                return mi.id === item.menu_item_id;
                            });
                            return {
                                id: item.menu_item_id || item.id,
                                _itemId: item.id,
                                name: item.name,
                                price: self.toNumber(item.price),
                                qty: item.qty,
                                icon: menuItem ? menuItem.icon : "🍽️",
                            };
                        });
                        if (cartData.discount_type) {
                            self.discountType = cartData.discount_type;
                            self.discountValue = self.toNumber(
                                cartData.discount_value,
                            );
                            self.discountDisplay =
                                cartData.discount_type === "rp"
                                    ? self.formatRupiah(cartData.discount_value)
                                    : cartData.discount_value.toString();
                        }
                    } else {
                        self.cart = [];
                        self.cartId = null;
                    }
                },
                error: function (xhr) {
                    // silent fail
                },
            });
        },

        syncCartItem: function (itemId, qty) {
            var self = this;
            var csrfToken = this.getCsrfToken();

            if (!this.cartId) return;

            var cartItem = this.cart.find(function (c) {
                return c.id === itemId;
            });
            if (!cartItem || !cartItem._itemId) {
                return;
            }

            var url = "/api/cart/" + this.cartId + "/items/" + cartItem._itemId;

            $.ajax({
                url: url,
                type: "PUT",
                dataType: "json",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                data: { qty: qty },
                success: function (response) {
                    if (response && response.success) {
                        var cart = response.data.cart;
                        self.cart = cart.items.map(function (item) {
                            var menuItem = self.menuItems.find(function (mi) {
                                return mi.id === item.menu_item_id;
                            });
                            return {
                                id: item.menu_item_id || item.id,
                                _itemId: item.id,
                                name: item.name,
                                price: self.toNumber(item.price),
                                qty: item.qty,
                                icon: menuItem ? menuItem.icon : "🍽️",
                            };
                        });
                        if (cart.discount_type) {
                            self.discountType = cart.discount_type;
                            self.discountValue = self.toNumber(
                                cart.discount_value,
                            );
                            self.discountDisplay =
                                cart.discount_type === "rp"
                                    ? self.formatRupiah(cart.discount_value)
                                    : cart.discount_value.toString();
                        }
                    } else {
                        self.showToast(
                            "❌ Failed to update cart item: " +
                            (response ? response.message : "Unknown error"),
                        );
                        self.loadCartFromAPI();
                    }
                },
                error: function (xhr) {
                    self.showToast("❌ Failed to update cart item.");
                },
            });
        },

        applyDiscountToCart: function () {
            var self = this;
            var csrfToken = this.getCsrfToken();

            if (!this.cartId) return;

            $.ajax({
                url: "/api/cart/" + this.cartId + "/discount",
                type: "POST",
                dataType: "json",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                data: {
                    type: this.discountType,
                    value: this.discountValue,
                },
                success: function (response) {
                    if (response && response.success) {
                        var cart = response.data.cart;
                        self.cart = cart.items.map(function (item) {
                            var menuItem = self.menuItems.find(function (mi) {
                                return mi.id === item.menu_item_id;
                            });
                            return {
                                id: item.menu_item_id || item.id,
                                _itemId: item.id,
                                name: item.name,
                                price: self.toNumber(item.price),
                                qty: item.qty,
                                icon: menuItem ? menuItem.icon : "🍽️",
                            };
                        });
                        if (cart.discount_type) {
                            self.discountType = cart.discount_type;
                            self.discountValue = self.toNumber(
                                cart.discount_value,
                            );
                            self.discountDisplay =
                                cart.discount_type === "rp"
                                    ? self.formatRupiah(cart.discount_value)
                                    : cart.discount_value.toString();
                        }
                        self.showToast("✅ Discount applied");
                    } else {
                        self.showToast(
                            "❌ Failed to apply discount: " +
                            (response ? response.message : "Unknown error"),
                        );
                    }
                },
                error: function (xhr) {
                    self.showToast("❌ Failed to apply discount.");
                },
            });
        },

        // ============================================================
        // DRAFT API METHODS
        // ============================================================

        refreshDrafts: function () {
            this.loadDraftsFromAPI();
        },

        loadDraftsFromAPI: function () {
            var self = this;
            var companyId = window.KitaPOS?.outlet?.id || 1;
            var url = "/api/drafts?company_id=" + companyId;

            this.draftLoading = true;
            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                headers: { Accept: "application/json" },
                success: function (response) {
                    if (response && response.success && response.data) {
                        self.sessions = response.data.map(function (draft) {
                            return {
                                id: draft.id,
                                name: draft.name,
                                type: draft.type,
                                table: draft.table,
                                typeLabel: draft.typeLabel,
                                items: draft.items.map(function (item) {
                                    return {
                                        id: item.id,
                                        menu_item_id: item.menu_item_id,
                                        name: item.name,
                                        price: self.toNumber(item.price),
                                        qty: item.qty,
                                        total: self.toNumber(item.total),
                                    };
                                }),
                                createdAt: draft.createdAt,
                                status: draft.status,
                                subtotal: self.toNumber(draft.subtotal),
                                _persisted: true,
                            };
                        });
                        if (self.sessions.length > 0 && !self.activeSessionId) {
                            self.activeSessionId = self.sessions[0].id;
                        }
                        if (self.sessions.length > 0) {
                            self.showToast(
                                "📋 " +
                                self.sessions.length +
                                " draft(s) loaded",
                            );
                        }
                    } else {
                        self.sessions = [];
                    }
                    self.draftLoading = false;
                },
                error: function (xhr, status, error) {
                    self.sessions = [];
                    self.draftLoading = false;
                    if (xhr.status >= 500) {
                        self.showToast(
                            "⚠️ Failed to load drafts from server. (Error " +
                            xhr.status +
                            ")",
                        );
                    } else if (xhr.status === 403) {
                        self.showToast(
                            "⚠️ You don't have permission to access drafts.",
                        );
                    } else if (xhr.status !== 404 && xhr.status !== 0) {
                        self.showToast("⚠️ Failed to load drafts.");
                    }
                },
            });
        },

        createNewSession: function () {
            var type = this.newSessionType;
            var table = this.newSessionTable
                ? parseInt(this.newSessionTable, 10)
                : null;
            var name = "";
            if (type === "dinein") {
                if (!table || table < 1) {
                    this.showToast("❌ Please enter a valid table number");
                    return;
                }
                name = "Table " + table;
            } else {
                name = "Take Away";
            }

            var self = this;
            var companyId = window.KitaPOS?.outlet?.id || 1;
            var csrfToken = this.getCsrfToken();
            if (!csrfToken) {
                this.showToast(
                    "❌ CSRF token not found. Please refresh the page.",
                );
                return;
            }

            this.showToast("⏳ Creating order...");

            $.ajax({
                url: "/api/drafts",
                type: "POST",
                dataType: "json",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                data: {
                    company_id: companyId,
                    type: type,
                    table_number: table,
                    name: name,
                    items: [],
                },
                success: function (response) {
                    if (response && response.success) {
                        var draft = response.data;
                        self.sessions.push({
                            id: draft.id,
                            name: draft.name,
                            type: draft.type,
                            table: draft.table,
                            typeLabel: draft.typeLabel,
                            items: [],
                            createdAt: draft.createdAt,
                            status: draft.status,
                            subtotal: self.toNumber(draft.subtotal),
                            _persisted: true,
                        });
                        self.activeSessionId = draft.id;
                        self.showToast("✅ New order created: " + draft.name);
                    } else {
                        self.showToast(
                            "❌ Failed to create order: " +
                            (response ? response.message : "Unknown error"),
                        );
                    }
                },
                error: function (xhr) {
                    var errorMsg = "❌ Failed to create order. ";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg += xhr.responseJSON.message;
                    } else if (xhr.status === 422) {
                        errorMsg += "Validation failed. Check input data.";
                    } else if (xhr.status === 419) {
                        errorMsg += "Session expired. Refresh the page.";
                    } else {
                        errorMsg += "Try again.";
                    }
                    self.showToast(errorMsg);
                },
            });

            var el = document.getElementById("newSessionModal");
            if (el && typeof bootstrap !== "undefined" && bootstrap.Modal) {
                var modal = bootstrap.Modal.getInstance(el);
                if (modal) modal.hide();
            }
        },

        removeSession: function (id) {
            if (!confirm("Delete this session?")) return;

            var self = this;
            var csrfToken = this.getCsrfToken();

            $.ajax({
                url: "/api/drafts/" + id,
                type: "DELETE",
                dataType: "json",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                success: function (response) {
                    if (response && response.success) {
                        self.sessions = self.sessions.filter(function (s) {
                            return s.id !== id;
                        });
                        if (self.activeSessionId === id) {
                            self.activeSessionId =
                                self.sessions.length > 0
                                    ? self.sessions[0].id
                                    : null;
                        }
                        self.showToast("🗑️ Session deleted");
                    } else {
                        self.showToast(
                            "❌ Failed to delete: " +
                            (response ? response.message : "Unknown error"),
                        );
                    }
                },
                error: function (xhr) {
                    self.showToast("❌ Failed to delete session.");
                },
            });
        },

        confirmSessionToCart: function (sessionId) {
            var self = this;
            var session = this.sessions.find(function (s) {
                return s.id === sessionId;
            });
            if (!session) {
                this.showToast("❌ Session not found");
                return;
            }
            if (session.items.length === 0) {
                this.showToast("❌ Session is empty!");
                return;
            }

            var csrfToken = this.getCsrfToken();

            $.ajax({
                url: "/api/drafts/" + sessionId + "/to-cart",
                type: "POST",
                dataType: "json",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                success: function (response) {
                    if (response && response.success) {
                        var data = response.data;
                        if (data.cart) {
                            self.cartId = data.cart.id;
                            self.cart = data.cart.items.map(function (item) {
                                var menuItem = self.menuItems.find(
                                    function (mi) {
                                        return mi.id === item.menu_item_id;
                                    },
                                );
                                return {
                                    id: item.menu_item_id || item.id,
                                    _itemId: item.id,
                                    name: item.name,
                                    price: self.toNumber(item.price),
                                    qty: item.qty,
                                    icon: menuItem ? menuItem.icon : "🍽️",
                                };
                            });
                            if (data.cart.discount_type) {
                                self.discountType = data.cart.discount_type;
                                self.discountValue = self.toNumber(
                                    data.cart.discount_value,
                                );
                                self.discountDisplay =
                                    data.cart.discount_type === "rp"
                                        ? self.formatRupiah(
                                            data.cart.discount_value,
                                        )
                                        : data.cart.discount_value.toString();
                            }
                        } else {
                            data.items.forEach(function (item) {
                                var existing = self.cart.find(function (c) {
                                    return c.id === item.id;
                                });
                                if (existing) {
                                    existing.qty += item.qty;
                                } else {
                                    var menuItem = self.menuItems.find(
                                        function (mi) {
                                            return mi.id === item.id;
                                        },
                                    );
                                    self.cart.push({
                                        id: item.id,
                                        name: item.name,
                                        price: self.toNumber(item.price),
                                        qty: item.qty,
                                        icon: menuItem ? menuItem.icon : "🍽️",
                                    });
                                }
                            });
                        }

                        self.sessions = self.sessions.filter(function (s) {
                            return s.id !== sessionId;
                        });
                        if (self.activeSessionId === sessionId) {
                            self.activeSessionId =
                                self.sessions.length > 0
                                    ? self.sessions[0].id
                                    : null;
                        }

                        var el = document.getElementById("sessionDetailModal");
                        if (
                            el &&
                            typeof bootstrap !== "undefined" &&
                            bootstrap.Modal
                        ) {
                            var modal = bootstrap.Modal.getInstance(el);
                            if (modal) modal.hide();
                        }

                        self.showToast(
                            "🛒 " +
                            (data.draftName || data.name || "Draft") +
                            " moved to Cart!",
                        );
                    } else {
                        self.showToast(
                            "❌ Failed to move to cart: " +
                            (response ? response.message : "Unknown error"),
                        );
                    }
                },
                error: function (xhr) {
                    var msg = "❌ Failed to move to cart. ";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg += xhr.responseJSON.message;
                    }
                    self.showToast(msg);
                },
            });
        },

        activateDraft: function (id) {
            var self = this;
            var csrfToken = this.getCsrfToken();

            $.ajax({
                url: "/api/drafts/" + id + "/activate",
                type: "POST",
                dataType: "json",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                success: function (response) {
                    if (response && response.success) {
                        var session = self.sessions.find(function (s) {
                            return s.id === id;
                        });
                        if (session) {
                            session.status = "active";
                        }
                        self.showToast("✅ Draft reactivated");
                    } else {
                        self.showToast(
                            "❌ Failed to activate: " +
                            (response ? response.message : "Unknown error"),
                        );
                    }
                },
                error: function (xhr) {
                    self.showToast("❌ Failed to activate draft.");
                },
            });
        },

        setActiveSession: function (id) {
            this.activeSessionId = id;
            var session = this.sessions.find(function (s) {
                return s.id === id;
            });
            this.showToast(
                "🔁 Active session: " + (session ? session.name : "unknown"),
            );
        },

        openSessionDetailModal: function (sessionId) {
            var session = this.sessions.find(function (s) {
                return s.id === sessionId;
            });
            if (!session) {
                this.showToast("❌ Session not found");
                return;
            }
            this.selectedSession = session;
            var el = document.getElementById("sessionDetailModal");
            if (el && typeof bootstrap !== "undefined" && bootstrap.Modal) {
                new bootstrap.Modal(el).show();
            } else {
                this.showToast("❌ Modal not found");
            }
        },

        // ============================================================
        // DRAFT ITEM MUTATIONS
        // ============================================================

        incrementDraftQty: function (id) {
            if (!this.activeSessionId) {
                this.showToast("❌ Create a new order first!");
                this.openNewSessionModal();
                return;
            }
            var session = this.sessions.find(
                function (s) {
                    return s.id === this.activeSessionId;
                }.bind(this),
            );
            if (!session) {
                this.showToast("❌ Session not found");
                return;
            }
            if (session.status === "processing") {
                this.showToast(
                    '⚠️ Draft is being processed. Activate it first using the "Active" button.',
                );
                return;
            }

            var menuItem = this.menuItems.find(function (i) {
                return i.id === id;
            });
            if (!menuItem) {
                this.showToast("❌ Menu not found");
                return;
            }
            if (menuItem.status === "out") {
                this.showToast("❌ " + menuItem.name + " is out of stock!");
                return;
            }

            var self = this;
            var csrfToken = this.getCsrfToken();

            var existing = session.items.find(function (i) {
                return i.menu_item_id === id;
            });
            if (existing) {
                var newQty = existing.qty + 1;
                var url = "/api/drafts/" + session.id + "/items/" + existing.id;
                $.ajax({
                    url: url,
                    type: "PUT",
                    dataType: "json",
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    data: { qty: newQty },
                    success: function (response) {
                        if (response && response.success) {
                            self._updateSessionFromResponse(
                                session.id,
                                response.data.draft,
                            );
                            self.showToast(
                                "📝 " + menuItem.name + " quantity increased",
                            );
                        } else {
                            self.showToast(
                                "❌ Failed to update item: " +
                                (response
                                    ? response.message
                                    : "Unknown error"),
                            );
                        }
                    },
                    error: function (xhr) {
                        var msg = "❌ Failed to update item. ";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg += xhr.responseJSON.message;
                        }
                        self.showToast(msg);
                    },
                });
            } else {
                var url = "/api/drafts/" + session.id + "/items";
                $.ajax({
                    url: url,
                    type: "POST",
                    dataType: "json",
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    data: {
                        menu_item_id: menuItem.id,
                        name: menuItem.name,
                        price: this.toNumber(menuItem.price),
                        qty: 1,
                    },
                    success: function (response) {
                        if (response && response.success) {
                            self._updateSessionFromResponse(
                                session.id,
                                response.data.draft,
                            );
                            self.showToast(
                                "📝 " +
                                menuItem.name +
                                " added to " +
                                session.name,
                            );
                        } else {
                            self.showToast(
                                "❌ Failed to add item: " +
                                (response
                                    ? response.message
                                    : "Unknown error"),
                            );
                        }
                    },
                    error: function (xhr) {
                        var msg = "❌ Failed to add item. ";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg += xhr.responseJSON.message;
                        }
                        self.showToast(msg);
                    },
                });
            }
        },

        decrementDraftQty: function (id) {
            var session = this.sessions.find(
                function (s) {
                    return s.id === this.activeSessionId;
                }.bind(this),
            );
            if (!session) return;
            if (session.status === "processing") {
                this.showToast(
                    '⚠️ Draft is being processed. Activate it first using the "Active" button.',
                );
                return;
            }

            var item = session.items.find(function (i) {
                return i.menu_item_id === id;
            });
            if (!item) return;

            var self = this;
            var csrfToken = this.getCsrfToken();
            var newQty = item.qty - 1;
            var url = "/api/drafts/" + session.id + "/items/" + item.id;

            if (newQty <= 0) {
                $.ajax({
                    url: url,
                    type: "DELETE",
                    dataType: "json",
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    success: function (response) {
                        if (response && response.success) {
                            self._updateSessionFromResponse(
                                session.id,
                                response.data.draft,
                            );
                            self.showToast("🗑️ Item removed from draft");
                        } else {
                            self.showToast(
                                "❌ Failed to delete item: " +
                                (response
                                    ? response.message
                                    : "Unknown error"),
                            );
                        }
                    },
                    error: function (xhr) {
                        var msg = "❌ Failed to delete item. ";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg += xhr.responseJSON.message;
                        }
                        self.showToast(msg);
                    },
                });
            } else {
                $.ajax({
                    url: url,
                    type: "PUT",
                    dataType: "json",
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    data: { qty: newQty },
                    success: function (response) {
                        if (response && response.success) {
                            self._updateSessionFromResponse(
                                session.id,
                                response.data.draft,
                            );
                            self.showToast("📝 Quantity decreased");
                        } else {
                            self.showToast(
                                "❌ Failed to update item: " +
                                (response
                                    ? response.message
                                    : "Unknown error"),
                            );
                        }
                    },
                    error: function (xhr) {
                        var msg = "❌ Failed to update item. ";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg += xhr.responseJSON.message;
                        }
                        self.showToast(msg);
                    },
                });
            }
        },

        updateDraftQtyFromInput: function (id, event) {
            var val = parseInt(event.target.value, 10);
            if (isNaN(val) || val < 0) {
                event.target.value = this.getDisplayDraftQty(id);
                return;
            }

            var session = this.sessions.find(
                function (s) {
                    return s.id === this.activeSessionId;
                }.bind(this),
            );
            if (!session) {
                event.target.value = 1;
                return;
            }
            if (session.status === "processing") {
                this.showToast(
                    '⚠️ Draft is being processed. Activate it first using the "Active" button.',
                );
                event.target.value = this.getDisplayDraftQty(id);
                return;
            }

            var item = session.items.find(function (i) {
                return i.menu_item_id === id;
            });
            var self = this;
            var csrfToken = this.getCsrfToken();

            if (!item && val > 0) {
                var menuItem = this.menuItems.find(function (i) {
                    return i.id === id;
                });
                if (menuItem && menuItem.status !== "out") {
                    $.ajax({
                        url: "/api/drafts/" + session.id + "/items",
                        type: "POST",
                        dataType: "json",
                        headers: {
                            Accept: "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                        },
                        data: {
                            menu_item_id: menuItem.id,
                            name: menuItem.name,
                            price: this.toNumber(menuItem.price),
                            qty: val,
                        },
                        success: function (response) {
                            if (response && response.success) {
                                self._updateSessionFromResponse(
                                    session.id,
                                    response.data.draft,
                                );
                                self.showToast(
                                    "📝 " +
                                    menuItem.name +
                                    " added (" +
                                    val +
                                    "x)",
                                );
                            } else {
                                self.showToast(
                                    "❌ Failed to add item: " +
                                    (response
                                        ? response.message
                                        : "Unknown error"),
                                );
                            }
                        },
                        error: function (xhr) {
                            self.showToast("❌ Failed to add item.");
                        },
                    });
                } else {
                    this.showToast("❌ Item not available");
                    event.target.value = this.getDisplayDraftQty(id);
                }
                return;
            }

            if (!item) {
                event.target.value = this.getDisplayDraftQty(id);
                return;
            }

            var url = "/api/drafts/" + session.id + "/items/" + item.id;

            if (val === 0) {
                $.ajax({
                    url: url,
                    type: "DELETE",
                    dataType: "json",
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    success: function (response) {
                        if (response && response.success) {
                            self._updateSessionFromResponse(
                                session.id,
                                response.data.draft,
                            );
                            self.showToast("🗑️ Item deleted");
                        } else {
                            self.showToast(
                                "❌ Failed to delete item: " +
                                (response
                                    ? response.message
                                    : "Unknown error"),
                            );
                        }
                    },
                    error: function (xhr) {
                        self.showToast("❌ Failed to delete item.");
                    },
                });
            } else {
                $.ajax({
                    url: url,
                    type: "PUT",
                    dataType: "json",
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    data: { qty: val },
                    success: function (response) {
                        if (response && response.success) {
                            self._updateSessionFromResponse(
                                session.id,
                                response.data.draft,
                            );
                            self.showToast("📝 Quantity updated to " + val);
                        } else {
                            self.showToast(
                                "❌ Failed to update item: " +
                                (response
                                    ? response.message
                                    : "Unknown error"),
                            );
                        }
                    },
                    error: function (xhr) {
                        self.showToast("❌ Failed to update item.");
                    },
                });
            }
        },

        _updateSessionFromResponse: function (sessionId, draftData) {
            var sessionIndex = this.sessions.findIndex(function (s) {
                return s.id === sessionId;
            });
            if (sessionIndex === -1) return;

            var items = draftData.items.map(
                function (item) {
                    return {
                        id: item.id,
                        menu_item_id: item.menu_item_id,
                        name: item.name,
                        price: this.toNumber(item.price),
                        qty: item.qty,
                        total: this.toNumber(item.total),
                    };
                }.bind(this),
            );

            var updatedSession = {
                id: draftData.id,
                name: draftData.name,
                type: draftData.type,
                table: draftData.table,
                typeLabel:
                    draftData.type === "dinein" ? "🍽️ Dine In" : "🛍️ Take Away",
                items: items,
                subtotal: this.toNumber(draftData.subtotal),
                createdAt: draftData.createdAt,
                status: draftData.status,
                _persisted: true,
            };

            this.sessions.splice(sessionIndex, 1, updatedSession);
            if (this.selectedSession && this.selectedSession.id === sessionId) {
                this.selectedSession = updatedSession;
            }
        },

        // ============================================================
        // SESSION MANAGEMENT
        // ============================================================

        openNewSessionModal: function () {
            this.newSessionType = "dinein";
            this.newSessionTable = "";
            var el = document.getElementById("newSessionModal");
            if (el && typeof bootstrap !== "undefined" && bootstrap.Modal) {
                new bootstrap.Modal(el).show();
            }
        },

        // ============================================================
        // SAVE / LOAD
        // ============================================================

        saveOpeningBalance: function (value) {
            this.openingBalance = value;
            try {
                localStorage.setItem("openingBalance", value.toString());
            } catch (e) { }
        },
        saveTransactionHistory: function () {
            try {
                localStorage.setItem(
                    "transactionHistory",
                    JSON.stringify(this.transactionHistory),
                );
            } catch (e) { }
        },
        saveTransaction: function (
            method,
            total,
            paid,
            change,
            items,
            discountAmt,
            discountType,
            discountValue,
            subtotal,
            nomorTransaksi,
        ) {
            var now = new Date();
            var timestamp = this.formatTanggalIndonesia(now);
            var transaction = {
                id: this.transactionHistory.length + 1,
                nomor_transaksi: nomorTransaksi || "#" + (this.transactionHistory.length + 1),
                timestamp: timestamp,
                items: items,
                total: total,
                subtotal: subtotal,
                discount: discountAmt,
                discountType: discountType,
                discountValue: discountValue,
                method: method,
                paid: paid,
                change: change,
            };
            this.transactionHistory.push(transaction);
            this.saveTransactionHistory();
            return transaction;
        },
        showToast: function (msg) {
            this.toastMessage = msg;
            if (this.toast) {
                try {
                    this.toast.show();
                } catch (e) { }
            }
        },

        generateTransactionNumber: function (callback) {
            var self = this;
            var csrfToken = this.getCsrfToken();

            $.ajax({
                url: "/trx-number",
                type: "GET",
                dataType: "json",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                success: function (response) {
                    if (response && response.success && response.data) {
                        callback(response.data);
                    } else {
                        self.showToast("❌ Gagal menghasilkan nomor transaksi");
                        callback(null);
                    }
                },
                error: function (xhr) {
                    self.showToast(
                        "❌ Gagal menghubungi server untuk nomor transaksi",
                    );
                    callback(null);
                },
            });
        },

        // ============================================================
        // PRINT METHODS
        // ============================================================

        setPrintMethod: function (method) {
            this.printMethod = method;
            try {
                localStorage.setItem("printMethod", method);
            } catch (e) { }
            var label =
                method === "bluetooth"
                    ? "Bluetooth"
                    : method === "server"
                        ? "Server"
                        : "Auto (Server fallback)";
            this.showToast("🖨️ Metode print: " + label);
        },

        printTransaction: function (transactionId) {
            var self = this;
            var trx = this.transactionHistory.find(function (t) {
                return t.id === transactionId;
            });
            if (!trx) {
                this.showToast("❌ Transaksi tidak ditemukan");
                return;
            }

            var method = this.printMethod || "auto";

            if (method === "bluetooth") {
                this.printStrukMobile(trx);
                return;
            }

            if (method === "server") {
                this._printViaServer(transactionId, function (success) {
                    if (!success) {
                        self.showToast(
                            "⚠️ Server print gagal. Silahkan coba metode Bluetooth.",
                        );
                    }
                });
                return;
            }

            this.showToast("⏳ Mencetak via server...");
            this._printViaServer(transactionId, function (success) {
                if (!success) {
                    self.showToast(
                        "⚠️ Server print gagal, fallback ke Bluetooth...",
                    );
                    setTimeout(function () {
                        self.printStrukMobile(trx);
                    }, 500);
                }
            });
        },

        _printViaServer: function (transactionId, callback) {
            var self = this;
            var csrfToken = this.getCsrfToken();

            $.ajax({
                url: "/api/transactions/" + transactionId + "/print",
                type: "POST",
                dataType: "json",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                success: function (response) {
                    if (response && response.success) {
                        self.showToast("✅ Struk berhasil dicetak via server");
                        if (callback) callback(true);
                    } else {
                        self.showToast(
                            "❌ Server print error: " +
                            (response.message || "Unknown error"),
                        );
                        if (callback) callback(false);
                    }
                },
                error: function (xhr) {
                    var msg = "❌ Server print gagal: ";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg += xhr.responseJSON.message;
                    } else {
                        msg += xhr.statusText || "Network error";
                    }
                    self.showToast(msg);
                    if (callback) callback(false);
                },
            });
        },

        // ============================================================
        // HELPERS
        // ============================================================

        formatPriceInput: function (event) {
            var value = event.target.value.replace(/\D/g, "");
            if (value === "") {
                event.target.value = "";
                return;
            }
            var number = parseInt(value, 10);
            if (isNaN(number)) {
                event.target.value = "";
                return;
            }
            event.target.value = this.formatRupiah(number);
        },
        parseRupiah: function (str) {
            if (!str) return 0;
            return parseInt(str.replace(/\D/g, ""), 10) || 0;
        },
        formatTanggalIndonesia: function (date) {
            var month = [
                "Januari",
                "Februari",
                "Maret",
                "April",
                "Mei",
                "Juni",
                "Juli",
                "Agustus",
                "September",
                "Oktober",
                "November",
                "Desember",
            ];
            var hour = String(date.getHours()).padStart(2, "0");
            var minute = String(date.getMinutes()).padStart(2, "0");
            return (
                date.getDate() +
                " " +
                month[date.getMonth()] +
                " " +
                date.getFullYear() +
                " " +
                hour +
                ":" +
                minute
            );
        },
        formatReceiptLine: function (leftText, rightText, is80mm) {
            if (is80mm === undefined) is80mm = false;
            var lineLength = is80mm ? 48 : 32;
            var left = leftText.toString();
            var right = rightText.toString();
            var spaceLength = lineLength - left.length - right.length;
            if (spaceLength < 1) {
                left = left.substring(0, lineLength - right.length - 2) + "..";
                spaceLength = 0;
            }
            return left + " ".repeat(spaceLength) + right;
        },
        getInitials: function (name) {
            if (!name) return "??";
            var words = name.split(" ");
            var initials = "";
            for (var i = 0; i < words.length; i++) {
                if (words[i].length > 0) {
                    initials += words[i].charAt(0).toUpperCase();
                }
            }
            if (initials.length < 2) {
                initials = name.substring(0, 2).toUpperCase();
            }
            return initials;
        },

        // ============================================================
        // UI NAVIGATION
        // ============================================================

        goHome: function () {
            window.location.href = window.KitaPOS.routes.home;
        },
        openCalculator: function () {
            var el = document.getElementById("calcModal");
            if (el && typeof bootstrap !== "undefined" && bootstrap.Modal) {
                new bootstrap.Modal(el).show();
            }
        },
        openHistory: function () {
            var el = document.getElementById("historyModal");
            if (el && typeof bootstrap !== "undefined" && bootstrap.Modal) {
                new bootstrap.Modal(el).show();
            }
        },
        toggleMobileCart: function () {
            this.mobileCartOpen = !this.mobileCartOpen;
        },
        closeMobileCart: function () {
            this.mobileCartOpen = false;
        },

        // ============================================================
        // CART OPERATIONS
        // ============================================================

        incrementQty: function (id) {
            var existing = this.cart.find(function (c) {
                return c.id === id;
            });
            if (existing) {
                var menuItem = this.menuItems.find(function (i) {
                    return i.id === id;
                });
                if (menuItem && menuItem.status === "out") {
                    this.showToast("❌ " + menuItem.name + " is out of stock!");
                    return;
                }
                existing.qty += 1;
                this.syncCartItem(id, existing.qty);
            } else {
                this.showToast("❌ Item not in cart.");
            }
        },
        decrementQty: function (id) {
            var idx = -1;
            for (var i = 0; i < this.cart.length; i++) {
                if (this.cart[i].id === id) {
                    idx = i;
                    break;
                }
            }
            if (idx === -1) return;
            if (this.cart[idx].qty > 1) {
                this.cart[idx].qty -= 1;
                this.syncCartItem(id, this.cart[idx].qty);
            } else {
                this.cart.splice(idx, 1);
                this.syncCartItem(id, 0);
            }
        },
        updateQtyFromInput: function (id, event) {
            var val = parseInt(event.target.value, 10);
            if (isNaN(val) || val < 0) {
                event.target.value = this.getDisplayQty(id);
                return;
            }
            var idx = -1;
            for (var i = 0; i < this.cart.length; i++) {
                if (this.cart[i].id === id) {
                    idx = i;
                    break;
                }
            }
            if (idx === -1) {
                event.target.value = this.getDisplayQty(id);
                this.showToast("❌ Item not in cart.");
                return;
            }
            if (val === 0) {
                this.cart.splice(idx, 1);
                this.syncCartItem(id, 0);
            } else {
                var menuItem = this.menuItems.find(function (i) {
                    return i.id === id;
                });
                if (menuItem && menuItem.status === "out") {
                    this.showToast("❌ " + menuItem.name + " is out of stock!");
                    event.target.value = this.getDisplayQty(id);
                    return;
                }
                this.cart[idx].qty = val;
                this.syncCartItem(id, val);
            }
        },
        resetTo: function (id, targetQty) {
            var item = this.cart.find(function (c) {
                return c.id === id;
            });
            if (item && item.qty > targetQty) {
                item.qty = targetQty;
                this.syncCartItem(id, targetQty);
                this.showToast("✅ Quantity reset to " + targetQty);
            }
        },

        // ============================================================
        // MENU MANAGEMENT
        // ============================================================

        openAddMenu: function (category) {
            if (category === undefined) category = "food";
            if (category !== "additional" && !this.isAdmin()) {
                this.showToast("❌ You are not authorized to add menu.");
                return;
            }
            this.newItem = {
                name: "",
                price: "",
                category: category,
                status: "available",
                icon: category === "additional" ? "➕" : "🍽️",
                imagePreview: null,
                imageData: null,
            };
            setTimeout(function () {
                if (typeof $ !== "undefined" && $.fn && $.fn.select2) {
                    $("#manualCategory")
                        .val(category)
                        .trigger("change.select2");
                    $("#manualStatus")
                        .val("available")
                        .trigger("change.select2");
                }
            }, 50);
            var el = document.getElementById("addItemModal");
            if (el && typeof bootstrap !== "undefined" && bootstrap.Modal) {
                new bootstrap.Modal(el).show();
            }
        },
        onCategoryChange: function () {
            if (this.newItem.category === "additional") {
                this.newItem.status = "available";
                this.newItem.icon = "➕";
            } else if (!this.newItem.icon || this.newItem.icon === "➕") {
                this.newItem.icon = "🍽️";
            }
        },
        saveNewItem: function () {
            if (this.newItem.category !== "additional" && !this.isAdmin()) {
                this.showToast("❌ You are not authorized to add menu.");
                return;
            }

            if (!this.newItem.name.trim()) {
                this.showToast("❌ Menu name is required!");
                return;
            }
            var price =
                parseInt(this.newItem.price.replace(/\D/g, ""), 10) || 0;
            if (price <= 0) {
                this.showToast("❌ Price must be a positive number!");
                return;
            }

            var self = this;
            var csrfToken = this.getCsrfToken();

            var formData = new FormData();
            formData.append("name", this.newItem.name.trim());
            formData.append("price", price);
            formData.append("category", this.newItem.category);
            formData.append("status", this.newItem.status);
            if (
                this.newItem.imageData &&
                this.newItem.imageData.startsWith("data:image")
            ) {
                try {
                    var byteString = atob(this.newItem.imageData.split(",")[1]);
                    var mimeString = this.newItem.imageData
                        .split(",")[0]
                        .split(":")[1]
                        .split(";")[0];
                    var ab = new ArrayBuffer(byteString.length);
                    var ia = new Uint8Array(ab);
                    for (var i = 0; i < byteString.length; i++) {
                        ia[i] = byteString.charCodeAt(i);
                    }
                    var blob = new Blob([ab], { type: mimeString });
                    formData.append("image", blob, "menu_image.jpg");
                } catch (e) {
                    // ignore
                }
            }

            this.showToast("⏳ Saving menu...");

            $.ajax({
                url: "/api/menu",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                },
                success: function (response) {
                    if (response && response.success) {
                        var newItem = response.data;
                        self.menuItems.push({
                            id: newItem.id,
                            name: newItem.name,
                            price: newItem.price,
                            category: newItem.category,
                            status: newItem.status,
                            icon: self.newItem.icon || "🍽️",
                            image: newItem.image || null,
                        });
                        if (newItem.id >= self.nextId) {
                            self.nextId = newItem.id + 1;
                        }
                        var el = document.getElementById("addItemModal");
                        if (
                            el &&
                            typeof bootstrap !== "undefined" &&
                            bootstrap.Modal
                        ) {
                            var modal = bootstrap.Modal.getInstance(el);
                            if (modal) modal.hide();
                        }
                        self.showToast(
                            '✅ Menu "' +
                            newItem.name +
                            '" added successfully!',
                        );
                    } else {
                        self.showToast(
                            "❌ Failed to save menu: " +
                            (response ? response.message : "Unknown error"),
                        );
                    }
                },
                error: function (xhr) {
                    var msg = "❌ Failed to save menu. ";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg += xhr.responseJSON.message;
                    } else if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        var firstKey = Object.keys(errors)[0];
                        if (firstKey && errors[firstKey].length > 0) {
                            msg += errors[firstKey][0];
                        } else {
                            msg += "Validation error.";
                        }
                    } else {
                        msg += "Please try again.";
                    }
                    self.showToast(msg);
                },
            });
        },
        handleImageUpload: function (event) {
            var file = event.target.files[0];
            if (!file) {
                this.newItem.imagePreview = null;
                this.newItem.imageData = null;
                return;
            }
            var reader = new FileReader();
            reader.onload = function (e) {
                this.newItem.imagePreview = e.target.result;
                this.newItem.imageData = e.target.result;
            }.bind(this);
            reader.readAsDataURL(file);
        },
        handleEditImageUpload: function (event) {
            var file = event.target.files[0];
            if (!file) {
                this.editItem.imagePreview = null;
                this.editItem.imageData = null;
                return;
            }
            var reader = new FileReader();
            reader.onload = function (e) {
                this.editItem.imagePreview = e.target.result;
                this.editItem.imageData = e.target.result;
            }.bind(this);
            reader.readAsDataURL(file);
        },
        openEditMenu: function (id) {
            if (!this.isAdmin()) {
                this.showToast("❌ You are not authorized to edit menu.");
                return;
            }

            var item = this.menuItems.find(function (i) {
                return i.id === id;
            });
            if (!item) {
                this.showToast("❌ Menu not found!");
                return;
            }

            this.editItemId = id;
            this.editItem = {
                id: item.id,
                name: item.name,
                price: item.price,
                category: item.category,
                status: item.status,
                icon: item.icon || "🍽️",
                imagePreview: item.image || null,
                imageData: null,
            };
            var fileInput = document.getElementById("editImage");
            if (fileInput) fileInput.value = "";

            var titleEl = document.querySelector("#editItemModal .modal-title");
            if (titleEl) {
                if (item.category === "additional") {
                    titleEl.innerHTML =
                        '<i class="bi bi-pencil-square me-2"></i> Edit Additional Menu';
                } else {
                    titleEl.innerHTML =
                        '<i class="bi bi-pencil-square me-2"></i> Edit Menu';
                }
            }

            var el = document.getElementById("editItemModal");
            if (el && typeof bootstrap !== "undefined" && bootstrap.Modal) {
                var modal = new bootstrap.Modal(el);
                modal.show();
            }
            setTimeout(
                function () {
                    if (typeof $ !== "undefined" && $.fn && $.fn.select2) {
                        $("#editCategory, #editStatus").select2("destroy");
                        $("#editCategory, #editStatus").select2({
                            theme: "default",
                            width: "100%",
                            dropdownParent: $("#editItemModal"),
                            dropdownAutoWidth: true,
                            placeholder: "Select...",
                            allowClear: false,
                        });
                        $("#editCategory").on(
                            "change",
                            function (e) {
                                this.editItem.category = e.target.value;
                            }.bind(this),
                        );
                        $("#editStatus").on(
                            "change",
                            function (e) {
                                this.editItem.status = e.target.value;
                            }.bind(this),
                        );
                        $("#editCategory")
                            .val(this.editItem.category)
                            .trigger("change.select2");
                        $("#editStatus")
                            .val(this.editItem.status)
                            .trigger("change.select2");
                    }
                }.bind(this),
                100,
            );
        },
        saveEditItem: function () {
            if (!this.isAdmin()) {
                this.showToast("❌ You are not authorized to edit menu.");
                return;
            }

            var id = this.editItemId;
            if (id === null || id === undefined) {
                this.showToast("❌ No item selected to edit!");
                return;
            }

            var name = this.editItem.name.trim();
            var rawPrice = this.editItem.price.toString().replace(/\D/g, "");
            var price = parseInt(rawPrice, 10) || 0;

            if (!name) {
                this.showToast("❌ Menu name is required!");
                return;
            }
            if (price <= 0) {
                this.showToast("❌ Price must be a positive number!");
                return;
            }

            var self = this;
            var csrfToken = this.getCsrfToken();

            var formData = new FormData();
            formData.append("name", name);
            formData.append("price", price);
            formData.append("category", this.editItem.category);
            formData.append("status", this.editItem.status);

            if (
                this.editItem.imageData &&
                this.editItem.imageData.startsWith("data:image")
            ) {
                try {
                    var byteString = atob(
                        this.editItem.imageData.split(",")[1],
                    );
                    var mimeString = this.editItem.imageData
                        .split(",")[0]
                        .split(":")[1]
                        .split(";")[0];
                    var ab = new ArrayBuffer(byteString.length);
                    var ia = new Uint8Array(ab);
                    for (var i = 0; i < byteString.length; i++) {
                        ia[i] = byteString.charCodeAt(i);
                    }
                    var blob = new Blob([ab], { type: mimeString });
                    formData.append("image", blob, "menu_image.jpg");
                } catch (e) {
                    // ignore
                }
            }

            $.ajax({
                url: "/api/menu/" + id,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "X-HTTP-Method-Override": "PUT",
                },
                success: function (response) {
                    if (response && response.success) {
                        var index = -1;
                        for (var i = 0; i < self.menuItems.length; i++) {
                            if (self.menuItems[i].id === id) {
                                index = i;
                                break;
                            }
                        }
                        if (index !== -1) {
                            self.menuItems[index] = {
                                id: id,
                                name: name,
                                price: price,
                                category: self.editItem.category,
                                status: self.editItem.status,
                                icon: self.editItem.icon || "🍽️",
                                image: response.data.image || null,
                            };
                        }

                        self.sessions.forEach(function (session) {
                            session.items.forEach(function (item) {
                                if (item.menu_item_id === id) {
                                    item.name = name;
                                    item.price = price;
                                }
                            });
                        });
                        self.cart.forEach(function (item) {
                            if (item.id === id) {
                                item.name = name;
                                item.price = price;
                                item.icon = self.editItem.icon || "🍽️";
                            }
                        });

                        var el = document.getElementById("editItemModal");
                        if (
                            el &&
                            typeof bootstrap !== "undefined" &&
                            bootstrap.Modal
                        ) {
                            var modal = bootstrap.Modal.getInstance(el);
                            if (modal) modal.hide();
                        }

                        self.editItemId = null;
                        self.editItem = {
                            name: "",
                            price: "",
                            category: "food",
                            status: "available",
                            icon: "🍽️",
                            imagePreview: null,
                            imageData: null,
                        };
                        self.showToast(
                            '✅ Menu "' + name + '" updated successfully!',
                        );
                    } else {
                        self.showToast(
                            "❌ Failed to update menu: " +
                            (response ? response.message : "Unknown error"),
                        );
                    }
                },
                error: function (xhr) {
                    var msg = "❌ Failed to update menu. ";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg += xhr.responseJSON.message;
                    } else if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        var firstKey = Object.keys(errors)[0];
                        if (firstKey && errors[firstKey].length > 0) {
                            msg += errors[firstKey][0];
                        } else {
                            msg += "Validation error.";
                        }
                    } else if (xhr.status === 404) {
                        msg += "Menu item not found.";
                    } else {
                        msg += "Please try again.";
                    }
                    self.showToast(msg);
                },
            });
        },

        deleteMenu: function (id) {
            if (!this.isAdmin()) {
                this.showToast("❌ You are not authorized to delete menu.");
                return;
            }
            if (!confirm("Delete this menu item?")) return;

            var self = this;
            var csrfToken = this.getCsrfToken();

            $.ajax({
                url: "/api/menu/" + id,
                type: "DELETE",
                dataType: "json",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                success: function (response) {
                    if (response && response.success) {
                        self.menuItems = self.menuItems.filter(function (item) {
                            return item.id !== id;
                        });
                        self.cart = self.cart.filter(function (item) {
                            return item.id !== id;
                        });
                        self.sessions.forEach(function (session) {
                            session.items = session.items.filter(
                                function (item) {
                                    return item.menu_item_id !== id;
                                },
                            );
                        });
                        self.showToast("🗑️ Menu deleted successfully");
                    } else {
                        self.showToast(
                            "❌ Failed to delete menu: " +
                            (response ? response.message : "Unknown error"),
                        );
                    }
                },
                error: function (xhr) {
                    var msg = "❌ Failed to delete menu. ";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg += xhr.responseJSON.message;
                    } else {
                        msg += "Please try again.";
                    }
                    self.showToast(msg);
                },
            });
        },

        // ============================================================
        // OPENING BALANCE
        // ============================================================

        openEditOpeningBalance: function () {
            this.editOpeningBalance = this.formatRupiah(this.openingBalance);
            var el = document.getElementById("editOpeningBalanceModal");
            if (el && typeof bootstrap !== "undefined" && bootstrap.Modal) {
                new bootstrap.Modal(el).show();
            }
        },
        saveOpeningBalance: function () {
            this.openingBalance =
                parseInt(this.editOpeningBalance.replace(/\D/g, ""), 10) || 0;
            this.saveOpeningBalance(this.openingBalance);
            var el = document.getElementById("editOpeningBalanceModal");
            if (el && typeof bootstrap !== "undefined" && bootstrap.Modal) {
                var modal = bootstrap.Modal.getInstance(el);
                if (modal) modal.hide();
            }
        },

        // ============================================================
        // CASHIER SESSION API (OPEN / CLOSE)
        // ============================================================

        /**
         * Open cashier session.
         * POST /api/cashier/open
         */
        openCashier: function () {
            var self = this;
            var csrfToken = this.getCsrfToken();

            this.showToast("⏳ Opening cashier...");

            $.ajax({
                url: "/api/cashier/open",
                type: "POST",
                dataType: "json",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                success: function (response) {
                    if (response && response.success) {
                        self.isCashierOnline = true;
                        self.activeSessionId = response.data.session_id;
                        self.openingBalance = response.data.opening_balance;
                        localStorage.setItem(
                            "isCashierOnline",
                            JSON.stringify(true),
                        );
                        localStorage.setItem(
                            "activeSessionId",
                            self.activeSessionId,
                        );
                        self.showToast(
                            "✅ Cashier opened successfully! (Opening: " +
                            self.formatRupiah(response.data.opening_balance) +
                            ")",
                        );
                    } else {
                        self.showToast(
                            "❌ Failed to open cashier: " +
                            (response ? response.message : "Unknown error"),
                        );
                    }
                },
                error: function (xhr) {
                    var msg = "❌ Failed to open cashier. ";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg += xhr.responseJSON.message;
                    } else if (xhr.status === 403) {
                        msg += "You don't have permission.";
                    } else if (xhr.status === 400) {
                        msg += xhr.responseJSON?.message || "Bad request.";
                    } else {
                        msg += "Please try again.";
                    }
                    self.showToast(msg);
                },
            });
        },

        /**
         * Open modal close cashier & load shift summary.
         */
        openCloseModal: function () {
            var self = this;
            if (!this.isCashierOnline) {
                this.showToast("❌ Cashier is not open.");
                return;
            }

            var csrfToken = this.getCsrfToken();
            this.showToast("⏳ Loading shift summary...");

            $.ajax({
                url: "/api/cashier/shift-summary",
                type: "GET",
                dataType: "json",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                success: function (response) {
                    if (response && response.success) {
                        var data = response.data;
                        // ✅ Konversi ke number
                        data.opening_balance = parseFloat(data.opening_balance) || 0;
                        data.total_sales = parseFloat(data.total_sales) || 0;
                        data.total_transactions = parseInt(data.total_transactions) || 0;
                        self.shiftSummary = data;

                        var theoretical = data.opening_balance + data.total_sales;
                        self.closingBalanceInput = self.formatRupiah(theoretical);

                        var modalEl = document.getElementById('closeCashierModal');
                        if (modalEl) {
                            var modal = new bootstrap.Modal(modalEl);
                            modal.show();
                            self.closeModalOpen = true;
                        } else {
                            self.showToast("❌ Modal element not found.");
                        }
                    } else {
                        var msg = response ? response.message : "Unknown error";
                        self.showToast("❌ Failed to get shift summary: " + msg);
                    }
                },
                error: function (xhr, status, error) {
                    var msg = "❌ Failed to get shift summary. ";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg += xhr.responseJSON.message;
                    } else {
                        msg += "Please try again.";
                    }
                    self.showToast(msg);
                }
            });
        },

        /**
         * Alias untuk closeCashier (backward compatibility)
         * Memanggil openCloseModal untuk membuka modal tutup kasir.
         */
        closeCashier: function () {
            this.openCloseModal();
        },

        /**
         * Submit close cashier from modal.
         */
        submitCloseCashier: function () {
            var self = this;
            var balance = parseInt(this.closingBalanceInput.replace(/\D/g, ''), 10) || 0;

            if (balance <= 0) {
                this.showToast("❌ Closing balance must be greater than 0.");
                return;
            }

            var csrfToken = this.getCsrfToken();
            this.showToast("⏳ Closing cashier...");

            $.ajax({
                url: "/api/cashier/close",
                type: "POST",
                dataType: "json",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                data: { actual_balance: balance },
                success: function (response) {
                    if (response && response.success) {
                        self.isCashierOnline = false;
                        self.activeSessionId = null;
                        localStorage.setItem("isCashierOnline", JSON.stringify(false));
                        localStorage.removeItem("activeSessionId");
                        self.closeModalOpen = false;

                        // Tutup modal
                        var modalEl = document.getElementById('closeCashierModal');
                        if (modalEl) {
                            var modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                        }

                        self.showToast("✅ Cashier closed successfully! (Closing: " + self.formatRupiah(balance) + ")");
                        self.loadCartFromAPI();
                        self.loadDraftsFromAPI();
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    } else {
                        self.showToast("❌ Failed to close cashier: " + (response ? response.message : "Unknown error"));
                    }
                },
                error: function (xhr) {
                    var msg = "❌ Failed to close cashier. ";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg += xhr.responseJSON.message;
                    }
                    self.showToast(msg);
                }
            });
        },

        /**
         * Check cashier session before transactions.
         */
        checkCashierSession: function () {
            if (!this.isCashierOnline) {
                this.showToast(
                    "❌ Cashier is not open. Please open cashier first.",
                );
                return false;
            }
            return true;
        },

        // ============================================================
        // CHECKOUT
        // ============================================================

        openCheckout: function () {
            if (!this.checkCashierSession()) return;

            if (this.cart.length === 0) return;
            this.paymentMethod = "cash";
            this.paymentAmount = "";
            this.paymentAmountRaw = 0;
            this.changeAmount = 0;
            this.discountType = "rp";
            this.discountValue = 0;
            this.discountDisplay = "0";
            setTimeout(function () {
                if (typeof $ !== "undefined" && $.fn && $.fn.select2) {
                    $("#paymentMethod").val("cash").trigger("change.select2");
                }
            }, 50);
            this.handlePaymentMethodChange();
            var el = document.getElementById("checkoutModal");
            if (el && typeof bootstrap !== "undefined" && bootstrap.Modal) {
                new bootstrap.Modal(el).show();
            }
        },

        setQuickPay: function (val) {
            this.paymentAmountRaw = val;
            this.paymentAmount = this.formatRupiah(val);
            this.updateChange();
        },
        updateChange: function () {
            try {
                if (this.paymentMethod === "cash") {
                    if (this.paymentAmount) {
                        var raw = this.parseRupiah(this.paymentAmount);
                        this.paymentAmountRaw = raw;
                        if (raw > 0) {
                            this.paymentAmount = this.formatRupiah(raw);
                        }
                    }
                    var total = this.discountedTotal;
                    this.changeAmount = this.paymentAmountRaw - total;
                } else {
                    var total = this.discountedTotal;
                    this.paymentAmount = this.formatRupiah(total);
                    this.paymentAmountRaw = total;
                    this.changeAmount = 0;
                }
            } catch (error) {
                // silent error
            }
        },
        handlePaymentMethodChange: function () {
            try {
                if (this.paymentMethod === "qris") {
                    var total = this.discountedTotal;
                    this.paymentAmount = this.formatRupiah(total);
                    this.paymentAmountRaw = total;
                    this.changeAmount = 0;
                } else {
                    this.paymentAmount = "";
                    this.paymentAmountRaw = 0;
                    this.changeAmount = 0;
                }
            } catch (error) {
                // silent error
            }
        },

        confirmCheckout: function () {
            if (!this.checkCashierSession()) return;

            try {
                var total = this.discountedTotal;
                var method = this.paymentMethod;
                var paid = this.paymentAmountRaw;
                var change = method === "cash" ? paid - total : 0;

                if (method === "cash" && paid < total) {
                    this.showToast("❌ Payment insufficient!");
                    return;
                }

                if (!this.cartId) {
                    this.showToast("❌ No active cart to checkout!");
                    return;
                }

                var self = this;
                var csrfToken = this.getCsrfToken();

                $.ajax({
                    url: "/api/cart/" + this.cartId + "/checkout",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    data: {
                        payment_method: method,
                        paid: paid,
                        change: change,
                    },
                    success: function (response) {
                        if (response && response.success) {
                            var transaction = response.data.transaction;

                            var items = transaction.items.map(function (item) {
                                return {
                                    name: item.name,
                                    qty: item.qty,
                                    price: item.price,
                                    subtotal: item.subtotal,
                                };
                            });

                            var localTrx = self.saveTransaction(
                                transaction.payment_method,
                                transaction.total,
                                transaction.paid,
                                transaction.change,
                                items,
                                transaction.discount_amount,
                                transaction.discount_type,
                                transaction.discount_value,
                                transaction.subtotal,
                                transaction.transaction_number,
                            );

                            var printData = {
                                id: transaction.id,
                                transaction_number: transaction.transaction_number,
                                nomor_transaksi: transaction.transaction_number,
                                timestamp: self.formatTanggalIndonesia(
                                    new Date(transaction.transaction_date),
                                ),
                                items: items,
                                total: transaction.total,
                                subtotal: transaction.subtotal,
                                discount: transaction.discount_amount,
                                discountType: transaction.discount_type,
                                discountValue: transaction.discount_value,
                                method: transaction.payment_method,
                                paid: transaction.paid,
                                change: transaction.change,
                            };

                            self.showToast("✅ Checkout successful!");

                            if (self.printMethod === "server") {
                                self._printViaServer(
                                    transaction.id,
                                    function (success) {
                                        if (!success) {
                                            self.showToast(
                                                "⚠️ Server print failed, please print manually from history",
                                            );
                                        }
                                    },
                                );
                            } else {
                                self.printStrukMobile(printData);
                            }

                            self.refreshStockStatus();

                            self.cart = [];
                            self.cartId = null;
                            self.mobileCartOpen = false;

                            var el = document.getElementById("checkoutModal");
                            if (
                                el &&
                                typeof bootstrap !== "undefined" &&
                                bootstrap.Modal
                            ) {
                                var modal = bootstrap.Modal.getInstance(el);
                                if (modal) modal.hide();
                            }
                        } else {
                            self.showToast(
                                "❌ Checkout failed: " +
                                (response
                                    ? response.message
                                    : "Unknown error"),
                            );
                        }
                    },
                    error: function (xhr) {
                        var msg = "❌ Checkout failed. ";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg += xhr.responseJSON.message;
                        }
                        self.showToast(msg);
                    },
                });
            } catch (error) {
                this.showToast("❌ Checkout error: " + (error.message || ""));
            }
        },

        updateDiscount: function (event) {
            var raw = event.target.value.replace(/\D/g, "");
            if (this.discountType === "rp") {
                var val = parseInt(raw, 10) || 0;
                this.discountValue = val;
                this.discountDisplay = this.formatRupiah(val);
                event.target.value = this.formatRupiah(val);
            } else {
                var pct = parseInt(raw, 10) || 0;
                if (pct > 100) pct = 100;
                this.discountValue = pct;
                this.discountDisplay = pct.toString();
                event.target.value = pct.toString();
            }
            if (this.cartId) {
                this.applyDiscountToCart();
            }
            this.updateChange();
        },
        reformatDiscountDisplay: function () {
            if (this.discountType === "rp") {
                this.discountDisplay = this.formatRupiah(this.discountValue);
            } else {
                this.discountDisplay = this.discountValue.toString();
            }
            this.updateChange();
        },

        // ---- COMPUTED for cart/discount ----
        get cartTotal() {
            return this.cart.reduce(function (sum, item) {
                return sum + item.price * item.qty;
            }, 0);
        },
        get cartCount() {
            return this.cart.reduce(function (sum, item) {
                return sum + item.qty;
            }, 0);
        },
        get discountAmount() {
            var total = this.cartTotal;
            if (this.discountType === "rp") {
                var val = this.discountValue || 0;
                return Math.min(val, total);
            } else if (this.discountType === "percent") {
                var pct = Math.min(this.discountValue || 0, 100);
                return (total * pct) / 100;
            }
            return 0;
        },
        get discountedTotal() {
            return Math.max(this.cartTotal - this.discountAmount, 0);
        },

        // ===== QUICK PAY OPTIONS =====
        _buildQuickPayOptions: function (total) {
            if (total <= 0) return [0];

            var options = [total];
            options.push(total + 1000);
            options.push(total + 2000);
            options.push(total + 5000);

            if (total <= 50000) {
                options.push(50000);
            }
            if (total <= 100000) {
                options.push(100000);
            }

            var unique = options.filter(function (v, i, self) {
                return self.indexOf(v) === i;
            });
            unique.sort(function (a, b) {
                return a - b;
            });

            if (total <= 100000) {
                var idx100 = unique.indexOf(100000);
                if (idx100 !== -1 && idx100 !== unique.length - 1) {
                    unique.splice(idx100, 1);
                    unique.push(100000);
                }
            }

            if (unique.length > 5) {
                var firstFour = unique.slice(0, 4);
                var last = unique[unique.length - 1];
                unique = firstFour.concat(last);
            }

            return unique;
        },

        get quickPayOptions() {
            return this._buildQuickPayOptions(this.discountedTotal);
        },

        getDraftQuickPayOptions: function (sessionId) {
            var total = this.getSessionTotal(sessionId);
            return this._buildQuickPayOptions(total);
        },

        // ============================================================
        // HISTORY
        // ============================================================

        deleteTransaction: function (id) {
            if (confirm("Delete transaction #" + id + "?")) {
                this.transactionHistory = this.transactionHistory.filter(
                    function (trx) {
                        return trx.id !== id;
                    },
                );
                this.transactionHistory.forEach(function (trx, index) {
                    trx.id = index + 1;
                });
                this.saveTransactionHistory();
                this.showToast("🗑️ Deleted");
            }
        },
        clearAllTransactions: function () {
            if (confirm("⚠️ Clear ALL?")) {
                this.transactionHistory = [];
                this.saveTransactionHistory();
                this.showToast("🗑️ All cleared");
            }
        },

        // ============================================================
        // PRINTER
        // ============================================================

        applyPrinterSize: function () {
            try {
                localStorage.setItem(
                    "defaultPrinterSize",
                    this.defaultPrinterSize,
                );
            } catch (e) { }
            this.showToast("📄 Print size: " + this.defaultPrinterSize);
        },
        setOutlet: function (name, address, id) {
            this.outletName = name || "My Fried Chicken";
            this.outletAddress = address || "Pusat";
            this.outletId = id || 1;
        },

        // ============================================================
        // PRINT FUNCTIONS (Bluetooth / Client-side)
        // ============================================================

        printStrukMobile: function (transaction) {
            if (
                !transaction ||
                !transaction.items ||
                transaction.items.length === 0
            ) {
                this.showToast("❌ No transaction data to print!");
                return;
            }
            var userAgent =
                navigator.userAgent || navigator.vendor || window.opera;
            if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
                this.printStrukWebBluetoothiOS(transaction);
            } else if (/android/i.test(userAgent)) {
                this.printStrukRawBT(transaction);
            } else {
                this.printStrukBrowser(transaction);
            }
        },

        printStrukRawBT: function (transaction) {
            try {
                var is80mm = this.defaultPrinterSize === "80mm";
                var maxWidth = is80mm ? 48 : 32;
                var encoder = new EscPosEncoder();
                var receipt = encoder.initialize();
                receipt
                    .align("center")
                    .bold(true)
                    .text(this.outletName)
                    .newline()
                    .bold(false)
                    .text(this.outletAddress)
                    .newline()
                    .line("-".repeat(maxWidth));
                receipt
                    .align("left")
                    .text("Kasir : " + this.cashierName)
                    .newline()
                    .text(
                        "Waktu : " +
                        this.formatTanggalIndonesia(transaction.timestamp),
                    )
                    .newline()
                    // 👇 PERBAIKAN: gunakan nomor_transaksi
                    .text(
                        "Struk #" +
                        (transaction.nomor_transaksi || transaction.transaction_number || transaction.id),
                    )
                    .newline()
                    .text(
                        "Bayar : " +
                        (transaction.method === "Cash" ? "Tunai" : "QRIS"),
                    )
                    .newline()
                    .line("-".repeat(maxWidth));
                // ... sisanya sama
            } catch (error) {
                this.showToast("⚠️ RawBT gagal, beralih ke print normal");
                this.printStrukBrowser(transaction);
            }
        },

        printStrukWebBluetoothiOS: function (transaction) {
            if (!navigator.bluetooth) {
                alert("⚠️ iOS BLOCKED!\nOpen KitaPOS using 'Bluefy' browser.");
                return;
            }
            try {
                var is80mm = this.defaultPrinterSize === "80mm";
                navigator.bluetooth
                    .requestDevice({
                        acceptAllDevices: true,
                        optionalServices: [
                            "000018f0-0000-1000-8000-00805f9b34fb",
                            "e7810a71-73ae-499d-8c15-faa9aef0c3f2",
                            "49535343-fe7d-4ae5-8fa9-9fafd205e455",
                        ],
                    })
                    .then(function (device) {
                        return device.gatt.connect();
                    })
                    .then(function (server) {
                        return server.getPrimaryServices();
                    })
                    .then(function (services) {
                        return services[0].getCharacteristics();
                    })
                    .then(
                        function (characteristics) {
                            var characteristic = characteristics.find(
                                function (c) {
                                    return (
                                        c.properties.write ||
                                        c.properties.writeWithoutResponse
                                    );
                                },
                            );
                            var encoder = new EscPosEncoder();
                            var receipt = encoder
                                .initialize()
                                .align("center")
                                .bold(true)
                                .text(this.outletName)
                                .newline()
                                .bold(false)
                                .text(this.outletAddress)
                                .newline()
                                .line("-".repeat(is80mm ? 48 : 32))
                                .align("left")
                                .text("Kasir : " + this.cashierName)
                                .newline()
                                .text(
                                    "Waktu : " +
                                    this.formatTanggalIndonesia(
                                        transaction.timestamp,
                                    ),
                                )
                                .newline()
                                .text(
                                    "Struk #" +
                                    (transaction.nomor_transaksi || transaction.transaction_number || transaction.id),
                                )
                                .newline()
                                .text(
                                    "Bayar : " +
                                    (transaction.method === "Cash"
                                        ? "Tunai"
                                        : "QRIS"),
                                )
                                .newline()
                                .line("-".repeat(is80mm ? 48 : 32));
                            receipt
                                .align("center")
                                .bold(true)
                                .text("PAID")
                                .newline()
                                .bold(false)
                                .line("-".repeat(is80mm ? 48 : 32))
                                .align("left")
                                .text(
                                    "Item".padEnd(20) +
                                    "Qty".padStart(6) +
                                    "Total".padStart(14),
                                )
                                .newline()
                                .line("-".repeat(is80mm ? 48 : 32));
                            transaction.items.forEach(
                                function (item) {
                                    var name = item.name.substring(0, 18);
                                    var qtyStr = item.qty.toString();
                                    var subtotalStr =
                                        "Rp" + this.formatRupiah(item.subtotal);
                                    var line =
                                        name.padEnd(20) +
                                        qtyStr.padStart(6) +
                                        subtotalStr.padStart(14);
                                    receipt.text(line).newline();
                                }.bind(this),
                            );
                            receipt
                                .line("-".repeat(is80mm ? 48 : 32))
                                .align("right")
                                .text(
                                    "Subtotal : Rp" +
                                    this.formatRupiah(transaction.subtotal),
                                )
                                .newline();
                            if (
                                transaction.discount &&
                                transaction.discount > 0
                            ) {
                                receipt
                                    .text(
                                        "Diskon : -Rp" +
                                        this.formatRupiah(
                                            transaction.discount,
                                        ),
                                    )
                                    .newline();
                            }
                            var totalQty = transaction.items.reduce(function (
                                sum,
                                item,
                            ) {
                                return sum + item.qty;
                            }, 0);
                            var totalStr =
                                "Rp" + this.formatRupiah(transaction.total);
                            receipt
                                .bold(true)
                                .text("Total (" + totalQty + ") : " + totalStr)
                                .newline()
                                .bold(false)
                                .line("-".repeat(is80mm ? 48 : 32))
                                .text(
                                    "Bayar : Rp" +
                                    this.formatRupiah(transaction.paid),
                                )
                                .newline()
                                .text(
                                    "Kembali : Rp" +
                                    this.formatRupiah(transaction.change),
                                )
                                .newline()
                                .line("-".repeat(is80mm ? 48 : 32))
                                .align("center")
                                .text("Powered by KitaPOS")
                                .newline()
                                .text("Terima kasih")
                                .newline()
                                .newline()
                                .newline()
                                .newline();
                            var resultData = receipt.encode();
                            var chunkSize = 50;
                            var promises = [];
                            for (
                                var i = 0;
                                i < resultData.length;
                                i += chunkSize
                            ) {
                                var chunk = resultData.slice(i, i + chunkSize);
                                promises.push(
                                    characteristic
                                        .writeValue(chunk)
                                        .then(function () {
                                            return new Promise(function (
                                                resolve,
                                            ) {
                                                setTimeout(resolve, 20);
                                            });
                                        }),
                                );
                            }
                            return Promise.all(promises).then(
                                function () {
                                    device.gatt.disconnect();
                                    this.showToast("🖨️ Printed from iPhone!");
                                }.bind(this),
                            );
                        }.bind(this),
                    )
                    .catch(
                        function (error) {
                            this.showToast(
                                "⚠️ Bluetooth failed. Switching to normal print...",
                            );
                            this.printStrukBrowser(transaction);
                        }.bind(this),
                    );
            } catch (error) {
                this.showToast(
                    "⚠️ Bluetooth failed. Switching to normal print...",
                );
                this.printStrukBrowser(transaction);
            }
        },

        printStrukBrowser: function (transaction) {
            if (
                !transaction ||
                !transaction.items ||
                transaction.items.length === 0
            )
                return;
            var style = document.getElementById("printPageStyle");
            if (!style) {
                style = document.createElement("style");
                style.id = "printPageStyle";
                document.head.appendChild(style);
            }
            var paperSize = this.defaultPrinterSize;
            style.innerHTML =
                "\n                @media print {\n                    @page { size: " +
                paperSize +
                " auto; margin: 0; }\n                    * { box-sizing: border-box; }\n                    body { margin: 0 !important; padding: 0 !important; background: #fff !important; }\n                    #strukContainer {\n                        display: block !important;\n                        width: " +
                paperSize +
                " !important;\n                        max-width: " +
                paperSize +
                " !important;\n                        margin: 0 auto !important;\n                        padding: 0 !important;\n                        background: #fff !important;\n                        overflow: hidden !important;\n                    }\n                    .struk-content {\n                        width: " +
                paperSize +
                " !important;\n                        max-width: " +
                paperSize +
                " !important;\n                        margin: 0 auto !important;\n                        padding: 2mm 2mm !important;\n                        background: #fff !important;\n                        font-size: " +
                (paperSize === "58mm" ? "8px" : "12px") +
                " !important;\n                        box-sizing: border-box !important;\n                        page-break-inside: avoid !important;\n                        page-break-after: avoid !important;\n                    }\n                    .struk-content.paper-58mm, .struk-content.paper-80mm {\n                        width: " +
                paperSize +
                " !important;\n                        max-width: " +
                paperSize +
                " !important;\n                    }\n                    html, body { margin: 0 !important; padding: 0 !important; }\n                    body > *:not(#strukContainer) { display: none !important; }\n                }\n            ";
            var totalQty = transaction.items.reduce(function (sum, item) {
                return sum + item.qty;
            }, 0);
            this.strukData = {
                id: transaction.id,
                nomor_transaksi: transaction.nomor_transaksi || transaction.transaction_number || transaction.id,
                timestamp: transaction.timestamp,
                items: transaction.items,
                total: transaction.total,
                totalQty: totalQty,
                paid: transaction.paid,
                change: transaction.change,
                method: transaction.method,
                discount: transaction.discount || 0,
                subtotal:
                    transaction.subtotal ||
                    transaction.total + (transaction.discount || 0),
            };
            var container = document.getElementById("strukContainer");
            container.style.display = "block";
            setTimeout(function () {
                window.print();
            }, 400);
            window.onafterprint = function () {
                container.style.display = "none";
                window.onafterprint = null;
            };
        },

        // ============================================================
        // CALCULATOR
        // ============================================================

        calcAppend: function (val) {
            this.calcExpression += val;
            this.updateCalcDisplay();
        },
        calcClear: function () {
            this.calcExpression = "";
            this.updateCalcDisplay();
        },
        calcBackspace: function () {
            this.calcExpression = this.calcExpression.slice(0, -1);
            this.updateCalcDisplay();
        },
        calcEvaluate: function () {
            try {
                this.calcExpression = eval(
                    this.calcExpression
                        .replace(/×/g, "*")
                        .replace(/÷/g, "/")
                        .replace(/−/g, "-"),
                ).toString();
            } catch (e) {
                this.calcExpression = "Error";
                setTimeout(
                    function () {
                        this.calcClear();
                    }.bind(this),
                    800,
                );
            }
            this.updateCalcDisplay();
        },
        updateCalcDisplay: function () {
            this.calcDisplay = this.calcExpression || "0";
        },

        // ============================================================
        // FILTER
        // ============================================================

        setCategory: function (cat) {
            this.currentCategory = cat;
        },
        filterMenu: function () { },
    });

    // ============================================================
    // UI COMPONENTS
    // ============================================================

    Alpine.data("navbarComponent", function () {
        return {};
    });

    Alpine.data("menuGridComponent", function () {
        return {
            init: function () { },
            getInitials: function (name) {
                if (!name) return "??";
                var words = name.split(" ");
                var initials = "";
                for (var i = 0; i < words.length; i++) {
                    if (words[i].length > 0) {
                        initials += words[i].charAt(0).toUpperCase();
                    }
                }
                if (initials.length < 2) {
                    initials = name.substring(0, 2).toUpperCase();
                }
                return initials;
            },
            deleteMenu: function (id) {
                Alpine.store("pos").deleteMenu(id);
            },
        };
    });

    Alpine.data("draftSessionsComponent", function () {
        return {
            refreshDrafts: function () {
                Alpine.store("pos").refreshDrafts();
            },
        };
    });

    Alpine.data("cartSidebarComponent", function () {
        return {};
    });
    Alpine.data("mobileCartComponent", function () {
        return {};
    });
    Alpine.data("checkoutComponent", function () {
        return {};
    });
    Alpine.data("historyComponent", function () {
        return {};
    });
    Alpine.data("calculatorComponent", function () {
        return {};
    });
    Alpine.data("addEditMenuComponent", function () {
        return {};
    });

    // ============================================================
    // ROOT
    // ============================================================

    Alpine.data("posApp", function () {
        return {
            init: function () {
                var store = Alpine.store("pos");

                var currentHour = new Date().getHours();
                var cashierName = "May";

                if (currentHour >= 8 && currentHour < 16) {
                    cashierName = "Sintia";
                } else if (currentHour >= 16 && currentHour <= 24) {
                    cashierName = "Aprilia";
                } else {
                    cashierName = "Indah";
                }

                store.setCashier(cashierName, true);

                if (window.KitaPOS && window.KitaPOS.user) {
                    store.setCashier(
                        window.KitaPOS.user.name,
                        window.KitaPOS.user.isOnline,
                    );
                } else {
                    store.loadCashier();
                }

                if (window.KitaPOS && window.KitaPOS.outlet) {
                    store.setOutlet(
                        window.KitaPOS.outlet.name,
                        window.KitaPOS.outlet.address,
                        window.KitaPOS.outlet.id,
                    );
                }
                store.init();

                setTimeout(function () {
                    if (typeof $ !== "undefined" && $.fn && $.fn.select2) {
                        $(".select2-custom").select2({
                            theme: "bootstrap-5",
                            width: "100%",
                            dropdownAutoWidth: true,
                        });

                        $("#paymentMethod").on("change", function (e) {
                            var s = Alpine.store("pos");
                            s.paymentMethod = e.target.value;
                            s.handlePaymentMethodChange();
                        });
                        $("#manualCategory").on("change", function (e) {
                            var s = Alpine.store("pos");
                            s.newItem.category = e.target.value;
                            s.onCategoryChange();
                        });
                        $("#manualStatus").on("change", function (e) {
                            var s = Alpine.store("pos");
                            s.newItem.status = e.target.value;
                        });
                    }
                }, 100);
            },
        };
    });
});