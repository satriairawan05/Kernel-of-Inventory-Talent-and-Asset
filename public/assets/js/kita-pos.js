// ================================================================
// assets/js/kita-pos.js - KitaPOS with Alpine.js
// All data from API (jQuery AJAX), no hardcoded menus.
// Multiple Draft Sessions (Dine In / Take Away) - Database persisted
// ================================================================

console.log('🚀 KitaPOS script.js loaded successfully!');

document.addEventListener('alpine:init', function () {
    console.log('⚡ Alpine.js initialized!');

    Alpine.store('pos', {
        // ---- STATE ----
        menuItems: [],
        nextId: 1,
        openingBalance: 0,
        sessions: [],           // Drafts loaded from API
        activeSessionId: null,
        selectedSession: null,
        cart: [],
        transactionHistory: [],
        currentCategory: 'all',
        searchQuery: '',
        mobileCartOpen: false,
        toastMessage: 'Notification',
        draftLoading: false,

        // Cashier
        cashierName: 'Guest',
        isCashierOnline: false,

        // Outlet
        outletName: 'My Fried Chicken',
        outletAddress: 'Pusat',
        outletId: 1,

        // Calculator
        calcExpression: '',
        calcDisplay: '0',

        // New/Edit item
        newItem: { name: '', price: '', category: 'food', status: 'available', icon: '🍽️', imagePreview: null, imageData: null },
        editItemId: null,
        editItem: { name: '', price: '', category: 'food', status: 'available', icon: '🍽️', imagePreview: null, imageData: null },
        editOpeningBalance: '',

        // Checkout
        paymentMethod: 'cash',
        paymentAmount: '',
        paymentAmountRaw: 0,
        changeAmount: 0,
        discountType: 'rp',
        discountValue: 0,
        discountDisplay: '0',

        // Printer
        defaultPrinterSize: '58mm',
        strukData: { id: '', timestamp: '', items: [], total: 0, totalQty: 0, paid: 0, change: 0, method: 'Cash', discount: 0, subtotal: 0 },

        toast: null,

        // New session modal
        newSessionType: 'dinein',
        newSessionTable: '',
        loading: false,
        apiError: false,

        // ---- COMPUTED ----
        get filteredMenu() {
            var items = this.menuItems;
            if (this.currentCategory !== 'all') {
                items = items.filter(function (item) { return item.category === this.currentCategory; }.bind(this));
            }
            if (this.searchQuery.trim()) {
                var q = this.searchQuery.trim().toLowerCase();
                items = items.filter(function (item) { return item.name.toLowerCase().indexOf(q) !== -1; });
            }
            return items;
        },

        // ---- DRAFT (sessions) helpers ----
        getTotalSessionsCount: function () {
            return this.sessions.reduce(function (sum, s) {
                return sum + s.items.reduce(function (acc, i) { return acc + i.qty; }, 0);
            }, 0);
        },
        getTotalSessionsTotal: function () {
            return this.sessions.reduce(function (sum, s) {
                return sum + this.getSessionTotal(s.id);
            }.bind(this), 0);
        },
        getSessionTotal: function (sessionId) {
            var session = this.sessions.find(function (s) { return s.id === sessionId; });
            if (!session) return 0;
            if (session.subtotal !== undefined && session.subtotal !== null) {
                return this.toNumber(session.subtotal);
            }
            return session.items.reduce(function (sum, item) {
                return sum + (this.toNumber(item.price) * this.toNumber(item.qty));
            }.bind(this), 0);
        },
        getDraftQty: function (id) {
            var session = this.sessions.find(function (s) { return s.id === this.activeSessionId; }.bind(this));
            if (!session) return 0;
            var item = session.items.find(function (i) { return i.menu_item_id === id; });
            return item ? item.qty : 0;
        },
        getDisplayDraftQty: function (id) {
            var qty = this.getDraftQty(id);
            return qty > 0 ? qty : 1;
        },

        getCartQty: function (id) {
            var item = this.cart.find(function (c) { return c.id === id; });
            return item ? item.qty : 0;
        },
        getDisplayQty: function (id) {
            var qty = this.getCartQty(id);
            return qty > 0 ? qty : 1;
        },

        // ---- HISTORY COMPUTED ----
        get totalRevenue() {
            return this.transactionHistory.reduce((sum, trx) => sum + (trx.total || 0), 0);
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
            this.cashierName = name || 'Guest';
            this.isCashierOnline = online;
            try {
                localStorage.setItem('cashierName', this.cashierName);
                localStorage.setItem('isCashierOnline', JSON.stringify(this.isCashierOnline));
            } catch (e) { }
        },
        loadCashier: function () {
            try {
                var name = localStorage.getItem('cashierName');
                var online = localStorage.getItem('isCashierOnline');
                if (name) this.cashierName = name;
                if (online !== null) this.isCashierOnline = JSON.parse(online);
            } catch (e) { }
        },

        // ---- GET CSRF TOKEN HELPER ----
        getCsrfToken: function () {
            var token = document.querySelector('meta[name="csrf-token"]');
            if (token) {
                return token.getAttribute('content');
            }
            console.warn('CSRF token meta tag not found!');
            return '';
        },

        // ---- HELPER TO NUMBER ----
        toNumber: function (value) {
            if (value === undefined || value === null) return 0;
            if (typeof value === 'number') return value;
            if (typeof value === 'string') {
                // Remove commas (thousand separators in some locales)
                var str = value.replace(/,/g, '');
                // Check if it has a dot and the part after the last dot is exactly 2 digits (decimal)
                var lastDotIndex = str.lastIndexOf('.');
                if (lastDotIndex !== -1) {
                    var afterDot = str.substring(lastDotIndex + 1);
                    if (afterDot.length === 2) {
                        // It's a decimal (e.g., 55000.00), parse as float and round
                        var num = parseFloat(str);
                        return Math.round(num);
                    }
                }
                // Otherwise, remove all dots (thousand separators) and parse as integer
                var cleaned = str.replace(/\./g, '');
                return parseInt(cleaned, 10) || 0;
            }
            return 0;
        },

        // ---- FORMAT RUPIAH ----
        formatRupiah: function (angka) {
            if (angka === undefined || angka === null) return '0';
            var num = this.toNumber(angka);
            if (isNaN(num) || num === 0) return '0';
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        },

        // ---- INIT ----
        init: function () {
            try {
                this.loadCashier();
                var storedOB = null;
                try {
                    storedOB = localStorage.getItem('openingBalance');
                } catch (e) { }
                this.openingBalance = storedOB !== null ? parseInt(storedOB, 10) || 0 : 150000;
                try {
                    localStorage.setItem('openingBalance', this.openingBalance.toString());
                } catch (e) { }

                // ===== LOAD MENU FROM API =====
                this._loadMenuFromAPI();

                // ===== LOAD HISTORY & PRINTER SIZE =====
                try {
                    var storedHistory = localStorage.getItem('transactionHistory');
                    if (storedHistory) {
                        this.transactionHistory = JSON.parse(storedHistory);
                        console.log('📜 History loaded:', this.transactionHistory.length, 'transactions');
                    }
                } catch (e) { }

                try {
                    var savedSize = localStorage.getItem('defaultPrinterSize');
                    this.defaultPrinterSize = savedSize || '58mm';
                    localStorage.setItem('defaultPrinterSize', this.defaultPrinterSize);
                } catch (e) { }

                var toastEl = document.getElementById('liveToast');
                if (toastEl && typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                    this.toast = new bootstrap.Toast(toastEl, { delay: 2500 });
                }

                // ===== LOAD DRAFTS FROM API =====
                this.loadDraftsFromAPI();

                console.log('✅ KitaPOS Store ready!');
                console.log('👤 Cashier:', this.cashierName, '| Online:', this.isCashierOnline);
                console.log('💰 Opening Balance:', this.openingBalance);
                console.log('📊 Total Revenue:', this.totalRevenue);
                console.log('💵 Grand Total:', this.grandTotal);
            } catch (error) {
                console.error('❌ Error during initialization:', error);
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
            var url = '/api/menu?company_id=' + companyId;

            this.loading = true;
            this.apiError = false;

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                headers: { 'Accept': 'application/json' },
                success: function (response) {
                    if (response.success && response.data) {
                        self.menuItems = response.data.sort(function (a, b) {
                            if (a.category === 'additional' && b.category !== 'additional') return 1;
                            if (a.category !== 'additional' && b.category === 'additional') return -1;
                            return a.name.localeCompare(b.name);
                        });
                        var maxId = 0;
                        self.menuItems.forEach(function (item) {
                            if (item.id > maxId) maxId = item.id;
                        });
                        self.nextId = maxId + 1;
                        console.log('✅ Menu loaded from API:', self.menuItems.length, 'items');
                    } else {
                        console.warn('API response not successful, using empty menu.');
                        self.menuItems = [];
                        self.nextId = 1;
                        self.apiError = true;
                    }
                    self.loading = false;
                },
                error: function (xhr, status, error) {
                    console.error('❌ Failed to load menu from API:', error);
                    self.menuItems = [];
                    self.nextId = 1;
                    self.apiError = true;
                    self.loading = false;
                    self.showToast('⚠️ Gagal memuat menu dari server. Silakan refresh halaman.');
                }
            });
        },

        // ============================================================
        // DRAFT API METHODS
        // ============================================================

        /**
         * Refresh draft list from API (called manually by user).
         */
        refreshDrafts: function () {
            this.loadDraftsFromAPI();
        },

        /**
         * Load drafts from API and populate sessions.
         */
        loadDraftsFromAPI: function () {
            var self = this;
            var companyId = window.KitaPOS?.outlet?.id || 1;
            var url = '/api/drafts?company_id=' + companyId;

            this.draftLoading = true;
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                headers: { 'Accept': 'application/json' },
                success: function (response) {
                    if (response.success && response.data) {
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
                                _persisted: true
                            };
                        });
                        if (self.sessions.length > 0 && !self.activeSessionId) {
                            self.activeSessionId = self.sessions[0].id;
                        }
                        console.log('✅ Drafts loaded from API:', self.sessions.length);
                        if (self.sessions.length > 0) {
                            self.showToast('📋 ' + self.sessions.length + ' draft(s) dimuat');
                        }
                    } else {
                        self.sessions = [];
                        console.warn('No drafts or API response not successful');
                    }
                    self.draftLoading = false;
                },
                error: function (xhr, status, error) {
                    console.error('❌ Failed to load drafts:', error);
                    self.sessions = [];
                    self.draftLoading = false;
                    if (xhr.status >= 500) {
                        self.showToast('⚠️ Gagal memuat draft dari server. (Error ' + xhr.status + ')');
                    } else if (xhr.status === 403) {
                        self.showToast('⚠️ Anda tidak memiliki akses ke draft.');
                    } else if (xhr.status !== 404 && xhr.status !== 0) {
                        self.showToast('⚠️ Gagal memuat draft.');
                    }
                }
            });
        },

        /**
         * Create a new draft (persisted to DB).
         */
        createNewSession: function () {
            var type = this.newSessionType;
            var table = this.newSessionTable ? parseInt(this.newSessionTable, 10) : null;
            var name = '';
            if (type === 'dinein') {
                if (!table || table < 1) {
                    this.showToast('❌ Masukkan nomor meja yang valid');
                    return;
                }
                name = 'Meja ' + table;
            } else {
                name = 'Take Away';
            }

            var self = this;
            var companyId = window.KitaPOS?.outlet?.id || 1;
            var csrfToken = this.getCsrfToken();
            if (!csrfToken) {
                this.showToast('❌ CSRF token tidak ditemukan. Refresh halaman.');
                return;
            }

            this.showToast('⏳ Membuat pesanan...');

            $.ajax({
                url: '/api/drafts',
                type: 'POST',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                data: {
                    company_id: companyId,
                    type: type,
                    table_number: table,
                    name: name,
                    items: []
                },
                success: function (response) {
                    if (response.success) {
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
                            _persisted: true
                        });
                        self.activeSessionId = draft.id;
                        self.showToast('✅ Pesanan baru dibuat: ' + draft.name);
                        console.log('📦 Session created:', draft);
                    } else {
                        self.showToast('❌ Gagal membuat pesanan: ' + (response.message || 'Unknown error'));
                        console.error('Create draft failed:', response);
                    }
                },
                error: function (xhr) {
                    console.error('Error creating draft:', xhr);
                    var errorMsg = '❌ Gagal membuat pesanan. ';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg += xhr.responseJSON.message;
                    } else if (xhr.status === 422) {
                        errorMsg += 'Validasi gagal. Periksa data input.';
                    } else if (xhr.status === 419) {
                        errorMsg += 'Session expired. Refresh halaman.';
                    } else {
                        errorMsg += 'Coba lagi.';
                    }
                    self.showToast(errorMsg);
                }
            });

            var el = document.getElementById('newSessionModal');
            if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                var modal = bootstrap.Modal.getInstance(el);
                if (modal) modal.hide();
            }
        },

        /**
         * Delete a draft (only if active/processing).
         */
        removeSession: function (id) {
            if (!confirm('Hapus session ini?')) return;

            var self = this;
            var csrfToken = this.getCsrfToken();

            $.ajax({
                url: '/api/drafts/' + id,
                type: 'DELETE',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (response) {
                    if (response.success) {
                        self.sessions = self.sessions.filter(function (s) { return s.id !== id; });
                        if (self.activeSessionId === id) {
                            self.activeSessionId = self.sessions.length > 0 ? self.sessions[0].id : null;
                        }
                        self.showToast('🗑️ Session dihapus');
                        console.log('🗑️ Session removed:', id);
                    } else {
                        self.showToast('❌ Gagal hapus: ' + response.message);
                    }
                },
                error: function (xhr) {
                    console.error('Error deleting draft:', xhr);
                    self.showToast('❌ Gagal hapus session.');
                }
            });
        },

        /**
         * Move draft to cart (change status to processing and return items).
         */
        confirmSessionToCart: function (sessionId) {
            var self = this;
            var session = this.sessions.find(function (s) { return s.id === sessionId; });
            if (!session) {
                this.showToast('❌ Session tidak ditemukan');
                return;
            }
            if (session.items.length === 0) {
                this.showToast('❌ Session kosong!');
                return;
            }

            var csrfToken = this.getCsrfToken();

            $.ajax({
                url: '/api/drafts/' + sessionId + '/to-cart',
                type: 'POST',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (response) {
                    if (response.success) {
                        var data = response.data;
                        data.items.forEach(function (item) {
                            var existing = self.cart.find(function (c) { return c.id === item.id; });
                            if (existing) {
                                existing.qty += item.qty;
                            } else {
                                var menuItem = self.menuItems.find(function (mi) { return mi.id === item.id; });
                                self.cart.push({
                                    id: item.id,
                                    name: item.name,
                                    price: self.toNumber(item.price),
                                    qty: item.qty,
                                    icon: menuItem ? menuItem.icon : '🍽️'
                                });
                            }
                        });

                        self.sessions = self.sessions.filter(function (s) { return s.id !== sessionId; });
                        if (self.activeSessionId === sessionId) {
                            self.activeSessionId = self.sessions.length > 0 ? self.sessions[0].id : null;
                        }

                        var el = document.getElementById('sessionDetailModal');
                        if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            var modal = bootstrap.Modal.getInstance(el);
                            if (modal) modal.hide();
                        }

                        self.showToast('🛒 ' + data.name + ' dilanjutkan ke Keranjang!');
                        console.log('🛒 Draft moved to cart:', data);
                    } else {
                        self.showToast('❌ Gagal pindahkan ke cart: ' + response.message);
                    }
                },
                error: function (xhr) {
                    console.error('Error moving draft to cart:', xhr);
                    self.showToast('❌ Gagal pindahkan ke cart.');
                }
            });
        },

        /**
         * Activate a draft (change from processing to active).
         */
        activateDraft: function (id) {
            var self = this;
            var csrfToken = this.getCsrfToken();

            $.ajax({
                url: '/api/drafts/' + id + '/activate',
                type: 'POST',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (response) {
                    if (response.success) {
                        var session = self.sessions.find(function (s) { return s.id === id; });
                        if (session) {
                            session.status = 'active';
                        }
                        self.showToast('✅ Draft diaktifkan kembali');
                    } else {
                        self.showToast('❌ Gagal aktivasi: ' + response.message);
                    }
                },
                error: function (xhr) {
                    console.error('Error activating draft:', xhr);
                    self.showToast('❌ Gagal aktivasi draft.');
                }
            });
        },

        /**
         * Set active session (local, no API call).
         */
        setActiveSession: function (id) {
            this.activeSessionId = id;
            var session = this.sessions.find(function (s) { return s.id === id; });
            this.showToast('🔁 Session aktif: ' + (session ? session.name : 'unknown'));
        },

        /**
         * Open session detail modal.
         */
        openSessionDetailModal: function (sessionId) {
            console.log('🔍 Opening detail for session:', sessionId);
            var session = this.sessions.find(function (s) { return s.id === sessionId; });
            if (!session) {
                this.showToast('❌ Session tidak ditemukan');
                console.error('Session not found:', sessionId);
                return;
            }
            this.selectedSession = session;
            console.log('📋 Selected session:', this.selectedSession);
            var el = document.getElementById('sessionDetailModal');
            if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                new bootstrap.Modal(el).show();
            } else {
                console.error('Modal element #sessionDetailModal not found');
                this.showToast('❌ Modal tidak ditemukan');
            }
        },

        // ============================================================
        // DRAFT ITEM MUTATIONS (Sync with API)
        // ============================================================

        /**
         * Add item to draft (API) or increment qty.
         */
        incrementDraftQty: function (id) {
            if (!this.activeSessionId) {
                this.showToast('❌ Buat pesanan baru terlebih dahulu!');
                this.openNewSessionModal();
                return;
            }
            var session = this.sessions.find(function (s) { return s.id === this.activeSessionId; }.bind(this));
            if (!session) {
                this.showToast('❌ Session tidak ditemukan');
                return;
            }
            if (session.status === 'processing') {
                this.showToast('⚠️ Draft sedang diproses. Aktifkan terlebih dahulu dengan tombol "Active".');
                return;
            }

            var menuItem = this.menuItems.find(function (i) { return i.id === id; });
            if (!menuItem) {
                this.showToast('❌ Menu tidak ditemukan');
                return;
            }
            if (menuItem.status === 'out') {
                this.showToast('❌ ' + menuItem.name + ' habis!');
                return;
            }

            var self = this;
            var csrfToken = this.getCsrfToken();

            // Cek apakah item sudah ada di draft (berdasarkan menu_item_id)
            var existing = session.items.find(function (i) { return i.menu_item_id === id; });
            if (existing) {
                // Update qty +1
                var newQty = existing.qty + 1;
                var url = '/api/drafts/' + session.id + '/items/' + existing.id;
                $.ajax({
                    url: url,
                    type: 'PUT',
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: { qty: newQty },
                    success: function (response) {
                        if (response.success) {
                            self._updateSessionFromResponse(session.id, response.data.draft);
                            self.showToast('📝 ' + menuItem.name + ' qty ditambah');
                        } else {
                            self.showToast('❌ Gagal update item: ' + response.message);
                        }
                    },
                    error: function (xhr) {
                        console.error('Error updating draft item:', xhr);
                        var msg = '❌ Gagal update item. ';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg += xhr.responseJSON.message;
                        }
                        self.showToast(msg);
                    }
                });
            } else {
                // Tambah item baru
                var url = '/api/drafts/' + session.id + '/items';
                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        menu_item_id: menuItem.id,
                        name: menuItem.name,
                        price: this.toNumber(menuItem.price),
                        qty: 1,
                    },
                    success: function (response) {
                        if (response.success) {
                            self._updateSessionFromResponse(session.id, response.data.draft);
                            self.showToast('📝 ' + menuItem.name + ' ditambahkan ke ' + session.name);
                        } else {
                            self.showToast('❌ Gagal tambah item: ' + response.message);
                        }
                    },
                    error: function (xhr) {
                        console.error('Error adding draft item:', xhr);
                        var msg = '❌ Gagal tambah item. ';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg += xhr.responseJSON.message;
                        }
                        self.showToast(msg);
                    }
                });
            }
        },

        /**
         * Decrement item qty from draft (API).
         */
        decrementDraftQty: function (id) {
            var session = this.sessions.find(function (s) { return s.id === this.activeSessionId; }.bind(this));
            if (!session) return;
            if (session.status === 'processing') {
                this.showToast('⚠️ Draft sedang diproses. Aktifkan terlebih dahulu dengan tombol "Active".');
                return;
            }

            var item = session.items.find(function (i) { return i.menu_item_id === id; });
            if (!item) return;

            var self = this;
            var csrfToken = this.getCsrfToken();
            var newQty = item.qty - 1;
            var url = '/api/drafts/' + session.id + '/items/' + item.id;

            if (newQty <= 0) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function (response) {
                        if (response.success) {
                            self._updateSessionFromResponse(session.id, response.data.draft);
                            self.showToast('🗑️ Item dihapus dari draft');
                        } else {
                            self.showToast('❌ Gagal hapus item: ' + response.message);
                        }
                    },
                    error: function (xhr) {
                        console.error('Error deleting draft item:', xhr);
                        var msg = '❌ Gagal hapus item. ';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg += xhr.responseJSON.message;
                        }
                        self.showToast(msg);
                    }
                });
            } else {
                $.ajax({
                    url: url,
                    type: 'PUT',
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: { qty: newQty },
                    success: function (response) {
                        if (response.success) {
                            self._updateSessionFromResponse(session.id, response.data.draft);
                            self.showToast('📝 Qty dikurangi');
                        } else {
                            self.showToast('❌ Gagal update item: ' + response.message);
                        }
                    },
                    error: function (xhr) {
                        console.error('Error updating draft item:', xhr);
                        var msg = '❌ Gagal update item. ';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg += xhr.responseJSON.message;
                        }
                        self.showToast(msg);
                    }
                });
            }
        },

        /**
         * Update draft item qty from input (API).
         */
        updateDraftQtyFromInput: function (id, event) {
            var val = parseInt(event.target.value, 10);
            if (isNaN(val) || val < 0) {
                event.target.value = this.getDisplayDraftQty(id);
                return;
            }

            var session = this.sessions.find(function (s) { return s.id === this.activeSessionId; }.bind(this));
            if (!session) {
                event.target.value = 1;
                return;
            }
            if (session.status === 'processing') {
                this.showToast('⚠️ Draft sedang diproses. Aktifkan terlebih dahulu dengan tombol "Active".');
                event.target.value = this.getDisplayDraftQty(id);
                return;
            }

            var item = session.items.find(function (i) { return i.menu_item_id === id; });
            if (!item && val > 0) {
                var menuItem = this.menuItems.find(function (i) { return i.id === id; });
                if (menuItem && menuItem.status !== 'out') {
                    this.incrementDraftQty(id);
                } else {
                    this.showToast('❌ Item tidak tersedia');
                    event.target.value = this.getDisplayDraftQty(id);
                }
                return;
            }

            if (!item) {
                event.target.value = this.getDisplayDraftQty(id);
                return;
            }

            var self = this;
            var csrfToken = this.getCsrfToken();
            var url = '/api/drafts/' + session.id + '/items/' + item.id;

            if (val === 0) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function (response) {
                        if (response.success) {
                            self._updateSessionFromResponse(session.id, response.data.draft);
                            self.showToast('🗑️ Item dihapus');
                        } else {
                            self.showToast('❌ Gagal hapus item: ' + response.message);
                        }
                    },
                    error: function (xhr) {
                        console.error('Error deleting draft item:', xhr);
                        self.showToast('❌ Gagal hapus item.');
                    }
                });
            } else {
                $.ajax({
                    url: url,
                    type: 'PUT',
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: { qty: val },
                    success: function (response) {
                        if (response.success) {
                            self._updateSessionFromResponse(session.id, response.data.draft);
                            self.showToast('📝 Qty diperbarui');
                        } else {
                            self.showToast('❌ Gagal update item: ' + response.message);
                        }
                    },
                    error: function (xhr) {
                        console.error('Error updating draft item:', xhr);
                        self.showToast('❌ Gagal update item.');
                    }
                });
            }
        },

        /**
         * Helper: Update session data from API response.
         * Subtotal diambil dari response data draft.subtotal (sudah dihitung backend).
         */
        _updateSessionFromResponse: function (sessionId, draftData) {
            var sessionIndex = this.sessions.findIndex(function (s) { return s.id === sessionId; });
            if (sessionIndex === -1) return;

            var items = draftData.items.map(function (item) {
                return {
                    id: item.id,
                    menu_item_id: item.menu_item_id,
                    name: item.name,
                    price: this.toNumber(item.price),
                    qty: item.qty,
                    total: this.toNumber(item.total),
                };
            }.bind(this));

            var updatedSession = {
                id: draftData.id,
                name: draftData.name,
                type: draftData.type,
                table: draftData.table,
                typeLabel: draftData.type === 'dinein' ? '🍽️ Dine In' : '🛍️ Take Away',
                items: items,
                subtotal: this.toNumber(draftData.subtotal),
                createdAt: draftData.createdAt,
                status: draftData.status,
                _persisted: true
            };

            console.log('🔄 Updated session:', updatedSession);
            console.log('📊 Subtotal:', updatedSession.subtotal);

            this.sessions.splice(sessionIndex, 1, updatedSession);
            if (this.selectedSession && this.selectedSession.id === sessionId) {
                this.selectedSession = updatedSession;
            }
        },

        // ============================================================
        // SESSION MANAGEMENT
        // ============================================================

        openNewSessionModal: function () {
            this.newSessionType = 'dinein';
            this.newSessionTable = '';
            var el = document.getElementById('newSessionModal');
            if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                new bootstrap.Modal(el).show();
            }
        },

        // ============================================================
        // SAVE / LOAD
        // ============================================================

        saveOpeningBalance: function (value) {
            this.openingBalance = value;
            try {
                localStorage.setItem('openingBalance', value.toString());
            } catch (e) { }
        },
        saveTransactionHistory: function () {
            try {
                localStorage.setItem('transactionHistory', JSON.stringify(this.transactionHistory));
            } catch (e) { }
        },
        saveTransaction: function (method, total, paid, change, items, discountAmt, discountType, discountValue, subtotal) {
            var now = new Date();
            var timestamp = this.formatTanggalIndonesia(now);
            var transaction = {
                id: this.transactionHistory.length + 1,
                timestamp: timestamp,
                items: items,
                total: total,
                subtotal: subtotal,
                discount: discountAmt,
                discountType: discountType,
                discountValue: discountValue,
                method: method,
                paid: paid,
                change: change
            };
            this.transactionHistory.push(transaction);
            this.saveTransactionHistory();
            console.log('💾 Transaction saved:', transaction);
            console.log('📊 Updated totalRevenue:', this.totalRevenue);
            console.log('💵 Updated grandTotal:', this.grandTotal);
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

        // ============================================================
        // HELPERS
        // ============================================================

        formatPriceInput: function (event) {
            var value = event.target.value.replace(/\D/g, '');
            if (value === '') {
                event.target.value = '';
                return;
            }
            var number = parseInt(value, 10);
            if (isNaN(number)) {
                event.target.value = '';
                return;
            }
            event.target.value = this.formatRupiah(number);
        },
        parseRupiah: function (str) {
            if (!str) return 0;
            return parseInt(str.replace(/\D/g, ''), 10) || 0;
        },
        formatTanggalIndonesia: function (date) {
            var month = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            var hour = String(date.getHours()).padStart(2, '0');
            var minute = String(date.getMinutes()).padStart(2, '0');
            return date.getDate() + ' ' + month[date.getMonth()] + ' ' + date.getFullYear() + ' ' + hour + ':' + minute;
        },
        formatReceiptLine: function (leftText, rightText, is80mm) {
            if (is80mm === undefined) is80mm = false;
            var lineLength = is80mm ? 48 : 32;
            var left = leftText.toString();
            var right = rightText.toString();
            var spaceLength = lineLength - left.length - right.length;
            if (spaceLength < 1) {
                left = left.substring(0, lineLength - right.length - 2) + '..';
                spaceLength = 0;
            }
            return left + ' '.repeat(spaceLength) + right;
        },
        getInitials: function (name) {
            if (!name) return '??';
            var words = name.split(' ');
            var initials = '';
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
            var el = document.getElementById('calcModal');
            if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                new bootstrap.Modal(el).show();
            }
        },
        openHistory: function () {
            var el = document.getElementById('historyModal');
            if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
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
            var existing = this.cart.find(function (c) { return c.id === id; });
            if (existing) {
                var menuItem = this.menuItems.find(function (i) { return i.id === id; });
                if (menuItem && menuItem.status === 'out') {
                    this.showToast('❌ ' + menuItem.name + ' habis!');
                    return;
                }
                existing.qty += 1;
            } else {
                this.showToast('❌ Item tidak ada di keranjang.');
            }
        },
        decrementQty: function (id) {
            var idx = -1;
            for (var i = 0; i < this.cart.length; i++) {
                if (this.cart[i].id === id) { idx = i; break; }
            }
            if (idx === -1) return;
            if (this.cart[idx].qty > 1) {
                this.cart[idx].qty -= 1;
            } else {
                this.cart.splice(idx, 1);
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
                if (this.cart[i].id === id) { idx = i; break; }
            }
            if (idx === -1) {
                event.target.value = this.getDisplayQty(id);
                this.showToast('❌ Item tidak ada di keranjang.');
                return;
            }
            if (val === 0) {
                this.cart.splice(idx, 1);
            } else {
                var menuItem = this.menuItems.find(function (i) { return i.id === id; });
                if (menuItem && menuItem.status === 'out') {
                    this.showToast('❌ ' + menuItem.name + ' habis!');
                    event.target.value = this.getDisplayQty(id);
                    return;
                }
                this.cart[idx].qty = val;
            }
        },
        resetTo: function (id, targetQty) {
            var item = this.cart.find(function (c) { return c.id === id; });
            if (item && item.qty > targetQty) {
                item.qty = targetQty;
                this.showToast('✅ Qty reset to ' + targetQty);
            }
        },

        // ============================================================
        // MENU MANAGEMENT
        // ============================================================

        openAddMenu: function (category) {
            if (category === undefined) category = 'food';
            this.newItem = { name: '', price: '', category: category, status: 'available', icon: category === 'additional' ? '➕' : '🍽️', imagePreview: null, imageData: null };
            setTimeout(function () {
                if (typeof $ !== 'undefined' && $.fn && $.fn.select2) {
                    $('#manualCategory').val(category).trigger('change.select2');
                    $('#manualStatus').val('available').trigger('change.select2');
                }
            }, 50);
            var el = document.getElementById('addItemModal');
            if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                new bootstrap.Modal(el).show();
            }
        },
        onCategoryChange: function () {
            if (this.newItem.category === 'additional') {
                this.newItem.status = 'available';
                this.newItem.icon = '➕';
            } else if (!this.newItem.icon || this.newItem.icon === '➕') {
                this.newItem.icon = '🍽️';
            }
        },
        saveNewItem: function () {
            var item = {
                id: this.nextId++,
                name: this.newItem.name.trim(),
                price: parseInt(this.newItem.price.replace(/\D/g, ''), 10) || 0,
                category: this.newItem.category,
                status: this.newItem.status,
                icon: this.newItem.icon || '🍽️'
            };
            this.menuItems.push(item);
            var el = document.getElementById('addItemModal');
            if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                var modal = bootstrap.Modal.getInstance(el);
                if (modal) modal.hide();
            }
            this.showToast('✅ Menu "' + item.name + '" added successfully!');
        },
        handleImageUpload: function (event) {
            var file = event.target.files[0];
            if (!file) { this.newItem.imagePreview = null; this.newItem.imageData = null; return; }
            var reader = new FileReader();
            reader.onload = function (e) {
                this.newItem.imagePreview = e.target.result;
                this.newItem.imageData = e.target.result;
            }.bind(this);
            reader.readAsDataURL(file);
        },
        handleEditImageUpload: function (event) {
            var file = event.target.files[0];
            if (!file) { this.editItem.imagePreview = null; this.editItem.imageData = null; return; }
            var reader = new FileReader();
            reader.onload = function (e) {
                this.editItem.imagePreview = e.target.result;
                this.editItem.imageData = e.target.result;
            }.bind(this);
            reader.readAsDataURL(file);
        },
        openEditMenu: function (id) {
            var item = this.menuItems.find(function (i) { return i.id === id; });
            if (!item) { this.showToast('❌ Menu not found!'); return; }
            this.editItemId = id;
            this.editItem = {
                id: item.id,
                name: item.name,
                price: item.price,
                category: item.category,
                status: item.status,
                icon: item.icon || '🍽️',
                imagePreview: item.image || null,
                imageData: null
            };
            var fileInput = document.getElementById('editImage');
            if (fileInput) fileInput.value = '';
            var el = document.getElementById('editItemModal');
            if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                var modal = new bootstrap.Modal(el);
                modal.show();
            }
            setTimeout(function () {
                if (typeof $ !== 'undefined' && $.fn && $.fn.select2) {
                    $('#editCategory, #editStatus').select2('destroy');
                    $('#editCategory, #editStatus').select2({
                        theme: 'default',
                        width: '100%',
                        dropdownParent: $('#editItemModal'),
                        dropdownAutoWidth: true,
                        placeholder: 'Select...',
                        allowClear: false
                    });
                    $('#editCategory').on('change', function (e) { this.editItem.category = e.target.value; }.bind(this));
                    $('#editStatus').on('change', function (e) { this.editItem.status = e.target.value; }.bind(this));
                    $('#editCategory').val(this.editItem.category).trigger('change.select2');
                    $('#editStatus').val(this.editItem.status).trigger('change.select2');
                }
            }.bind(this), 100);
        },
        saveEditItem: function () {
            var id = this.editItemId;
            if (id === null || id === undefined) { this.showToast('❌ No item selected to edit!'); return; }
            var index = -1;
            for (var i = 0; i < this.menuItems.length; i++) {
                if (this.menuItems[i].id === id) { index = i; break; }
            }
            if (index === -1) { this.showToast('❌ Menu not found!'); return; }
            var name = this.editItem.name.trim();
            var rawPrice = this.editItem.price.replace(/\D/g, '');
            var price = parseInt(rawPrice, 10) || 0;
            if (!name) { this.showToast('❌ Menu name is required!'); return; }
            if (price <= 0) { this.showToast('❌ Price must be a positive number!'); return; }
            this.menuItems[index] = {
                id: this.menuItems[index].id,
                name: name,
                price: price,
                category: this.editItem.category,
                status: this.editItem.status,
                icon: this.editItem.icon || '🍽️',
                image: this.editItem.imageData || this.menuItems[index].image
            };
            // Update sessions & cart
            this.sessions.forEach(function (session) {
                session.items.forEach(function (item) {
                    if (item.menu_item_id === id) {
                        item.name = name;
                        item.price = price;
                    }
                }.bind(this));
            }.bind(this));
            this.cart.forEach(function (item) {
                if (item.id === id) {
                    item.name = name;
                    item.price = price;
                    item.icon = this.editItem.icon || '🍽️';
                }
            }.bind(this));
            var el = document.getElementById('editItemModal');
            if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                var modal = bootstrap.Modal.getInstance(el);
                if (modal) modal.hide();
            }
            this.editItemId = null;
            this.editItem = { name: '', price: '', category: 'food', status: 'available', icon: '🍽️', imagePreview: null, imageData: null };
            this.showToast('✅ Menu "' + name + '" updated successfully!');
        },

        // ============================================================
        // OPENING BALANCE
        // ============================================================

        openEditOpeningBalance: function () {
            this.editOpeningBalance = this.formatRupiah(this.openingBalance);
            var el = document.getElementById('editOpeningBalanceModal');
            if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                new bootstrap.Modal(el).show();
            }
        },
        saveOpeningBalance: function () {
            this.openingBalance = parseInt(this.editOpeningBalance.replace(/\D/g, ''), 10) || 0;
            this.saveOpeningBalance(this.openingBalance);
            var el = document.getElementById('editOpeningBalanceModal');
            if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                var modal = bootstrap.Modal.getInstance(el);
                if (modal) modal.hide();
            }
        },

        // ============================================================
        // CHECKOUT
        // ============================================================

        openCheckout: function () {
            if (this.cart.length === 0) return;
            this.paymentMethod = 'cash';
            this.paymentAmount = '';
            this.paymentAmountRaw = 0;
            this.changeAmount = 0;
            this.discountType = 'rp';
            this.discountValue = 0;
            this.discountDisplay = '0';
            setTimeout(function () {
                if (typeof $ !== 'undefined' && $.fn && $.fn.select2) {
                    $('#paymentMethod').val('cash').trigger('change.select2');
                }
            }, 50);
            this.handlePaymentMethodChange();
            var el = document.getElementById('checkoutModal');
            if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
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
                if (this.paymentMethod === 'cash') {
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
                console.error('Error in updateChange:', error);
            }
        },
        handlePaymentMethodChange: function () {
            try {
                if (this.paymentMethod === 'qris') {
                    var total = this.discountedTotal;
                    this.paymentAmount = this.formatRupiah(total);
                    this.paymentAmountRaw = total;
                    this.changeAmount = 0;
                } else {
                    this.paymentAmount = '';
                    this.paymentAmountRaw = 0;
                    this.changeAmount = 0;
                }
            } catch (error) {
                console.error('Error in handlePaymentMethodChange:', error);
            }
        },
        confirmCheckout: function () {
            try {
                var total = this.discountedTotal;
                var method = this.paymentMethod;
                var paid = this.paymentAmountRaw;
                if (method === 'cash') {
                    if (paid < total) {
                        this.showToast('❌ Payment insufficient!');
                        return;
                    }
                    var change = paid - total;
                    var items = this.cart.map(function (item) {
                        return { name: item.name, qty: item.qty, price: item.price, subtotal: item.price * item.qty };
                    });
                    var transaction = this.saveTransaction('Cash', total, paid, change, items, this.discountAmount, this.discountType, this.discountValue, this.cartTotal);
                    this.showToast('✅ Checkout successful!');
                    this.printStrukMobile(transaction);
                } else {
                    paid = total;
                    this.paymentAmount = this.formatRupiah(paid);
                    this.paymentAmountRaw = paid;
                    var items = this.cart.map(function (item) {
                        return { name: item.name, qty: item.qty, price: item.price, subtotal: item.price * item.qty };
                    });
                    var transaction = this.saveTransaction('QRIS', total, paid, 0, items, this.discountAmount, this.discountType, this.discountValue, this.cartTotal);
                    this.showToast('✅ Checkout successful! Method: QRIS.');
                    this.printStrukMobile(transaction);
                }
                this.cart = [];
                this.mobileCartOpen = false;
                var el = document.getElementById('checkoutModal');
                if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    var modal = bootstrap.Modal.getInstance(el);
                    if (modal) modal.hide();
                }
            } catch (error) {
                console.error('Error in confirmCheckout:', error);
                this.showToast('❌ Checkout failed!');
            }
        },
        updateDiscount: function (event) {
            var raw = event.target.value.replace(/\D/g, '');
            if (this.discountType === 'rp') {
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
            this.updateChange();
        },
        reformatDiscountDisplay: function () {
            if (this.discountType === 'rp') {
                this.discountDisplay = this.formatRupiah(this.discountValue);
            } else {
                this.discountDisplay = this.discountValue.toString();
            }
            this.updateChange();
        },

        // ---- COMPUTED for cart/discount ----
        get cartTotal() {
            return this.cart.reduce(function (sum, item) { return sum + (item.price * item.qty); }, 0);
        },
        get cartCount() {
            return this.cart.reduce(function (sum, item) { return sum + item.qty; }, 0);
        },
        get discountAmount() {
            var total = this.cartTotal;
            if (this.discountType === 'rp') {
                var val = this.discountValue || 0;
                return Math.min(val, total);
            } else if (this.discountType === 'percent') {
                var pct = Math.min(this.discountValue || 0, 100);
                return total * pct / 100;
            }
            return 0;
        },
        get discountedTotal() {
            return Math.max(this.cartTotal - this.discountAmount, 0);
        },
        get quickPayOptions() {
            var total = this.discountedTotal;
            if (total <= 0) return [0];
            var end = 100000;
            if (total > 100000) {
                end = Math.ceil(total / 100000) * 100000;
                if (end <= total) end += 100000;
            }
            var down = Math.floor(total / 10000) * 10000;
            if (down === total) down = Math.max(0, down - 10000);
            if (total < 50000) down = 50000;
            if (down <= 0) down = 10000;
            var up = Math.ceil(total / 10000) * 10000;
            if (up === total) up = up + 10000;
            if (total < 50000) up = Math.max(down + 10000, 60000);
            if (up <= down) up = down + 10000;
            if (total <= 100000 && up >= end) up = Math.min(end - 10000, Math.ceil((total + end) / 2) / 10000 * 10000);
            if (up <= down) up = down + 10000;
            var others = [down, up, end].filter(function (v) { return v > 0 && v !== total; });
            others = Array.from(new Set(others)).sort(function (a, b) { return a - b; });
            var options = [total].concat(others);
            var endIndex = options.indexOf(end);
            if (endIndex !== -1 && endIndex !== options.length - 1) {
                options.splice(endIndex, 1);
                options.push(end);
            }
            return options;
        },

        // ============================================================
        // HISTORY
        // ============================================================

        deleteTransaction: function (id) {
            if (confirm('Delete transaction #' + id + '?')) {
                this.transactionHistory = this.transactionHistory.filter(function (trx) { return trx.id !== id; });
                this.transactionHistory.forEach(function (trx, index) { trx.id = index + 1; });
                this.saveTransactionHistory();
                this.showToast('🗑️ Deleted');
            }
        },
        clearAllTransactions: function () {
            if (confirm('⚠️ Clear ALL?')) {
                this.transactionHistory = [];
                this.saveTransactionHistory();
                this.showToast('🗑️ All cleared');
            }
        },

        // ============================================================
        // PRINTER
        // ============================================================

        applyPrinterSize: function () {
            try {
                localStorage.setItem('defaultPrinterSize', this.defaultPrinterSize);
            } catch (e) { }
            this.showToast('⚙️ Printer setting: ' + this.defaultPrinterSize);
        },
        setOutle: function (name, address, id) {
            this.outletName = name || 'My Fried Chicken';
            this.outletAddress = address || 'Pusat';
            this.outletId = id || 1;
        },

        // ============================================================
        // PRINT
        // ============================================================

        printStrukMobile: function (transaction) {
            if (!transaction || !transaction.items || transaction.items.length === 0) {
                this.showToast('❌ No transaction data to print!');
                return;
            }
            var userAgent = navigator.userAgent || navigator.vendor || window.opera;
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
                var is80mm = this.defaultPrinterSize === '80mm';
                var maxWidth = is80mm ? 48 : 32;
                var encoder = new EscPosEncoder();
                var receipt = encoder.initialize();
                receipt.align('center')
                    .bold(true).text(this.outletName).newline().bold(false)
                    .text(this.outletAddress).newline()
                    .line('-'.repeat(maxWidth));
                receipt.align('left')
                    .text('Kasir : ' + this.cashierName).newline()
                    .text('Waktu : ' + this.formatTanggalIndonesia(transaction.timestamp)).newline()
                    .text('No. Struk : #' + transaction.id).newline()
                    .text('Bayar : ' + (transaction.method === 'Cash' ? 'Tunai' : 'QRIS')).newline()
                    .line('-'.repeat(maxWidth));
                receipt.align('center')
                    .bold(true).text('LUNAS').newline().bold(false)
                    .line('-'.repeat(maxWidth));
                receipt.align('left')
                    .text('Item'.padEnd(20) + 'Qty'.padStart(6) + 'Total'.padStart(14)).newline()
                    .line('-'.repeat(maxWidth));
                transaction.items.forEach(function (item) {
                    var name = item.name.substring(0, 20);
                    var qtyStr = item.qty.toString();
                    var subtotalStr = 'Rp' + this.formatRupiah(item.subtotal);
                    var line = name.padEnd(20) + qtyStr.padStart(6) + subtotalStr.padStart(14);
                    receipt.text(line).newline();
                }.bind(this));
                receipt.line('-'.repeat(maxWidth));
                var subtotalStr = 'Rp' + this.formatRupiah(transaction.subtotal);
                receipt.align('right')
                    .text('Subtotal : ' + subtotalStr).newline();
                if (transaction.discount && transaction.discount > 0) {
                    var diskonStr = '-Rp' + this.formatRupiah(transaction.discount);
                    receipt.text('Diskon : ' + diskonStr).newline();
                }
                var totalQty = transaction.items.reduce(function (sum, item) { return sum + item.qty; }, 0);
                var totalStr = 'Rp' + this.formatRupiah(transaction.total);
                receipt.bold(true)
                    .text('Total (' + totalQty + ') : ' + totalStr).newline()
                    .bold(false)
                    .line('-'.repeat(maxWidth));
                receipt.text('Bayar : Rp' + this.formatRupiah(transaction.paid)).newline()
                    .text('Kembali : Rp' + this.formatRupiah(transaction.change)).newline()
                    .line('-'.repeat(maxWidth));
                receipt.align('center')
                    .text('Powered by KitaPOS').newline()
                    .text('Terima kasih').newline()
                    .newline().newline().newline();
                var resultData = receipt.encode();
                var binary = '';
                resultData.forEach(function (b) { binary += String.fromCharCode(b); });
                window.location.href = 'rawbt:base64,' + btoa(binary);
            } catch (error) {
                this.showToast('⚠️ RawBT failed, switching to normal print');
                this.printStrukBrowser(transaction);
            }
        },
        printStrukWebBluetoothiOS: function (transaction) {
            if (!navigator.bluetooth) {
                alert("⚠️ iOS BLOCKED!\nOpen KitaPOS using 'Bluefy' browser.");
                return;
            }
            try {
                var is80mm = this.defaultPrinterSize === '80mm';
                navigator.bluetooth.requestDevice({
                    acceptAllDevices: true,
                    optionalServices: ['000018f0-0000-1000-8000-00805f9b34fb', 'e7810a71-73ae-499d-8c15-faa9aef0c3f2', '49535343-fe7d-4ae5-8fa9-9fafd205e455']
                }).then(function (device) {
                    return device.gatt.connect();
                }).then(function (server) {
                    return server.getPrimaryServices();
                }).then(function (services) {
                    return services[0].getCharacteristics();
                }).then(function (characteristics) {
                    var characteristic = characteristics.find(function (c) {
                        return c.properties.write || c.properties.writeWithoutResponse;
                    });
                    var encoder = new EscPosEncoder();
                    var receipt = encoder.initialize()
                        .align('center')
                        .bold(true).text('KITA POS - PUSAT').newline().bold(false)
                        .line('-'.repeat(is80mm ? 48 : 32))
                        .align('left');
                    transaction.items.forEach(function (item) {
                        var leftStr = '  ' + item.qty + ' x ' + this.formatRupiah(item.price);
                        var rightStr = this.formatRupiah(item.subtotal);
                        receipt.text(item.name).newline();
                        receipt.text(this.formatReceiptLine(leftStr, rightStr, is80mm)).newline();
                    }.bind(this));
                    if (transaction.discount && transaction.discount > 0) {
                        receipt.line('-'.repeat(is80mm ? 48 : 32))
                            .text(this.formatReceiptLine('Diskon', '-Rp ' + this.formatRupiah(transaction.discount), is80mm)).newline();
                    }
                    receipt.line('-'.repeat(is80mm ? 48 : 32))
                        .text(this.formatReceiptLine('TOTAL', 'Rp ' + this.formatRupiah(transaction.total), is80mm)).newline()
                        .newline().newline().newline();
                    var resultData = receipt.encode();
                    var chunkSize = 50;
                    var promises = [];
                    for (var i = 0; i < resultData.length; i += chunkSize) {
                        var chunk = resultData.slice(i, i + chunkSize);
                        promises.push(characteristic.writeValue(chunk).then(function () {
                            return new Promise(function (resolve) {
                                setTimeout(resolve, 20);
                            });
                        }));
                    }
                    return Promise.all(promises).then(function () {
                        device.gatt.disconnect();
                        this.showToast('🖨️ Printed from iPhone!');
                    }.bind(this));
                }.bind(this)).catch(function (error) {
                    this.showToast('⚠️ Bluetooth failed. Switching to normal print...');
                    this.printStrukBrowser(transaction);
                }.bind(this));
            } catch (error) {
                this.showToast('⚠️ Bluetooth failed. Switching to normal print...');
                this.printStrukBrowser(transaction);
            }
        },
        printStrukBrowser: function (transaction) {
            if (!transaction || !transaction.items || transaction.items.length === 0) return;
            var style = document.getElementById('printPageStyle');
            if (!style) {
                style = document.createElement('style');
                style.id = 'printPageStyle';
                document.head.appendChild(style);
            }
            var paperSize = this.defaultPrinterSize;
            style.innerHTML = '\n                @media print {\n                    @page { size: ' + paperSize + ' auto; margin: 0; }\n                    * { box-sizing: border-box; }\n                    body { margin: 0 !important; padding: 0 !important; background: #fff !important; }\n                    #strukContainer {\n                        display: block !important;\n                        width: ' + paperSize + ' !important;\n                        max-width: ' + paperSize + ' !important;\n                        margin: 0 auto !important;\n                        padding: 0 !important;\n                        background: #fff !important;\n                        overflow: hidden !important;\n                    }\n                    .struk-content {\n                        width: ' + paperSize + ' !important;\n                        max-width: ' + paperSize + ' !important;\n                        margin: 0 auto !important;\n                        padding: 2mm 2mm !important;\n                        background: #fff !important;\n                        font-size: ' + (paperSize === '58mm' ? '8px' : '12px') + ' !important;\n                        box-sizing: border-box !important;\n                        page-break-inside: avoid !important;\n                        page-break-after: avoid !important;\n                    }\n                    .struk-content.paper-58mm, .struk-content.paper-80mm {\n                        width: ' + paperSize + ' !important;\n                        max-width: ' + paperSize + ' !important;\n                    }\n                    html, body { margin: 0 !important; padding: 0 !important; }\n                    body > *:not(#strukContainer) { display: none !important; }\n                }\n            ';
            var totalQty = transaction.items.reduce(function (sum, item) { return sum + item.qty; }, 0);
            this.strukData = {
                id: transaction.id,
                timestamp: transaction.timestamp,
                items: transaction.items,
                total: transaction.total,
                totalQty: totalQty,
                paid: transaction.paid,
                change: transaction.change,
                method: transaction.method,
                discount: transaction.discount || 0,
                subtotal: transaction.subtotal || transaction.total + (transaction.discount || 0)
            };
            var container = document.getElementById('strukContainer');
            container.style.display = 'block';
            setTimeout(function () {
                window.print();
            }, 400);
            window.onafterprint = function () {
                container.style.display = 'none';
                window.onafterprint = null;
            };
        },

        // ============================================================
        // CALCULATOR
        // ============================================================

        calcAppend: function (val) { this.calcExpression += val; this.updateCalcDisplay(); },
        calcClear: function () { this.calcExpression = ''; this.updateCalcDisplay(); },
        calcBackspace: function () { this.calcExpression = this.calcExpression.slice(0, -1); this.updateCalcDisplay(); },
        calcEvaluate: function () {
            try {
                this.calcExpression = eval(this.calcExpression.replace(/×/g, '*').replace(/÷/g, '/').replace(/−/g, '-')).toString();
            } catch (e) {
                this.calcExpression = 'Error';
                setTimeout(function () { this.calcClear(); }.bind(this), 800);
            }
            this.updateCalcDisplay();
        },
        updateCalcDisplay: function () { this.calcDisplay = this.calcExpression || '0'; },

        // ============================================================
        // FILTER
        // ============================================================

        setCategory: function (cat) { this.currentCategory = cat; },
        filterMenu: function () { }
    });

    // ============================================================
    // UI COMPONENTS
    // ============================================================

    Alpine.data('navbarComponent', function () { return {}; });

    Alpine.data('menuGridComponent', function () {
        return {
            init: function () { },
            getInitials: function (name) {
                if (!name) return '??';
                var words = name.split(' ');
                var initials = '';
                for (var i = 0; i < words.length; i++) {
                    if (words[i].length > 0) {
                        initials += words[i].charAt(0).toUpperCase();
                    }
                }
                if (initials.length < 2) {
                    initials = name.substring(0, 2).toUpperCase();
                }
                return initials;
            }
        };
    });

    Alpine.data('draftSessionsComponent', function () {
        return {
            refreshDrafts: function () {
                Alpine.store('pos').refreshDrafts();
            }
        };
    });

    Alpine.data('cartSidebarComponent', function () { return {}; });
    Alpine.data('mobileCartComponent', function () { return {}; });
    Alpine.data('checkoutComponent', function () { return {}; });
    Alpine.data('historyComponent', function () { return {}; });
    Alpine.data('calculatorComponent', function () { return {}; });
    Alpine.data('addEditMenuComponent', function () { return {}; });

    // ============================================================
    // ROOT
    // ============================================================

    Alpine.data('posApp', function () {
        return {
            init: function () {
                var store = Alpine.store('pos');

                var currentHour = new Date().getHours();
                var cashierName = 'May';

                if (currentHour >= 8 && currentHour < 16) {
                    cashierName = 'Sintia';
                } else if (currentHour >= 16 && currentHour <= 24) {
                    cashierName = 'Aprilia';
                } else {
                    cashierName = 'Indah';
                }

                store.setCashier(cashierName, true);

                if (window.KitaPOS && window.KitaPOS.user) {
                    store.setCashier(window.KitaPOS.user.name, window.KitaPOS.user.isOnline);
                } else {
                    store.loadCashier();
                }

                if (window.KitaPOS && window.KitaPOS.outlet) {
                    store.setOutle(
                        window.KitaPOS.outlet.name,
                        window.KitaPOS.outlet.address,
                        window.KitaPOS.outlet.id
                    );
                }
                store.init();

                setTimeout(function () {
                    if (typeof $ !== 'undefined' && $.fn && $.fn.select2) {
                        $('.select2-custom').select2({ theme: 'bootstrap-5', width: '100%', dropdownAutoWidth: true });

                        $('#paymentMethod').on('change', function (e) {
                            var s = Alpine.store('pos');
                            s.paymentMethod = e.target.value;
                            s.handlePaymentMethodChange();
                        });
                        $('#manualCategory').on('change', function (e) {
                            var s = Alpine.store('pos');
                            s.newItem.category = e.target.value;
                            s.onCategoryChange();
                        });
                        $('#manualStatus').on('change', function (e) {
                            var s = Alpine.store('pos');
                            s.newItem.status = e.target.value;
                        });
                    }
                }, 100);
            }
        };
    });

    console.log('✅ KitaPOS with Alpine.js ready! (Draft tetap ada setelah refresh)');
});