<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KitaPOS - Point of Sales</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    <link href="{{ asset('assets/css/kita-pos.css') }}" rel="stylesheet" />
    <!-- ===== Inject PEXELS API KEY ===== -->
    <script type="text/javascript">
        window._env = {
            PEXELS_API_KEY: '{{ env('PEXELS_API_KEY') }}'
        };
    </script>
</head>

<body x-data="posApp">
    <!-- ===== Inject User Data ===== -->
    <script type="text/javascript">
        window.KitaPOS = {
            user: {
                name: '{!! auth()->check() ? addslashes(auth()->user()->name) : 'Guest' !!}',
                isOnline: {{ auth()->check() ? 'true' : 'false' }}
            },
            outlet: {
                id: {{ auth()->user()->company_id ?? 1 }}, 
                name: "My Fried Chicken",
                address: "Pusat",
                openingBalance: "{{ $openingBalance ?? '150000' }}",
            },
            routes: {
                home: "{{ route('pos.home') }}",
            }
        };
    </script>

    <!-- ===== NAVBAR ===== -->
    @include('admin.pos.point-of-sales.components.navbar')

    <!-- ===== MAIN CONTENT ===== -->
    <div class="container-fluid px-2 px-sm-3 px-lg-4 py-3 pb-5" id="mainContent">
        <div class="row g-3">
            <div class="col-lg-8">
                <!-- CATEGORY FILTER -->
                <div class="cat-filter" id="categoryFilter">
                    <template x-for="cat in ['all', 'food', 'drink', 'snack', 'additional']" :key="cat">
                        <button class="btn-cat" :class="{ 'active': $store.pos.currentCategory === cat }"
                            @click="$store.pos.setCategory(cat)">
                            <span
                                x-text="cat === 'all' ? 'All' : cat === 'food' ? '🍔 Food' : cat === 'drink' ? '🥤 Drinks' : cat === 'snack' ? '🍿 Snacks' : '➕ Additional'"></span>
                        </button>
                    </template>
                </div>

                <!-- MENU GRID -->
                @include('admin.pos.point-of-sales.components.menu-grid')
            </div>

            <!-- ===== SIDEBAR (Desktop) ===== -->
            <div class="col-lg-4 d-none d-lg-block" x-data="cartSidebarComponent">
                <!-- DRAFT SESSIONS PANEL (Desktop) -->
                @include('admin.pos.point-of-sales.components.draft-sessions-panel')

                <!-- CART SIDEBAR -->
                @include('admin.pos.point-of-sales.components.cart-sidebar')
            </div>
        </div>
    </div>

    <!-- ===== MOBILE BOTTOM BAR ===== -->
    @include('admin.pos.point-of-sales.components.mobile-bottom-bar')

    <!-- ===== MOBILE DRAWER ===== -->
    @include('admin.pos.point-of-sales.components.mobile-cart')

    <!-- ===== FLOATING BUTTONS ===== -->
    @include('admin.pos.point-of-sales.components.floating-buttons')

    <!-- ================================================================ -->
    <!-- MODALS                                                          -->
    <!-- ================================================================ -->
    @include('admin.pos.point-of-sales.components.modals.new-session')
    @include('admin.pos.point-of-sales.components.modals.session-detail')
    @include('admin.pos.point-of-sales.components.modals.calculator')
    @include('admin.pos.point-of-sales.components.modals.add-menu')
    @include('admin.pos.point-of-sales.components.modals.edit-menu')
    @include('admin.pos.point-of-sales.components.modals.edit-opening-balance')
    @include('admin.pos.point-of-sales.components.modals.checkout')
    @include('admin.pos.point-of-sales.components.modals.history')

    <!-- ===== RECEIPT PRINT TEMPLATE ===== -->
    @include('admin.pos.point-of-sales.components.receipt')

    <!-- ===== TOAST ===== -->
    <div class="toast-container position-fixed start-50 translate-middle-x top-0 p-3 top-md-0 start-md-auto translate-middle-x-md-none end-md-0"
        style="z-index: 1080;">
        <div id="liveToast" class="toast align-items-center text-white bg-dark border-0 shadow" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" x-text="$store.pos.toastMessage">Notification message.</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- ===== FOOTER ===== -->
    @include('admin.pos.point-of-sales.components.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/escpos-encoder@1.2.0/dist/escpos-encoder.min.js"></script>
    <script src="{{ asset('assets/js/kita-pos.js') }}"></script>
</body>

</html>
