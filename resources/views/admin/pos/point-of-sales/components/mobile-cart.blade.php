<div class="cart-sidebar d-lg-none" id="mobileCartSidebar" :class="{ 'open': $store.pos.mobileCartOpen }"
    x-data="mobileCartComponent">
    <button class="cart-close" @click="$store.pos.closeMobileCart()"><i class="bi bi-x-lg"></i></button>

    <!-- DRAFT SESSIONS PANEL (Mobile) -->
    @include('admin.pos.point-of-sales.components.draft-sessions-panel', ['variant' => 'mobile'])

    <!-- CART PANEL MOBILE -->
    <div class="opening-balance mt-3">
        <div class="d-flex justify-content-between align-items-center">
            <span><i class="bi bi-wallet2"></i> Opening Balance</span>
            <span>
                <span x-text="'Rp ' + $store.pos.formatRupiah($store.pos.openingBalance)"></span>
                <button class="btn btn-sm btn-link p-0 ms-1" @click="$store.pos.openEditOpeningBalance()"
                    title="Edit Opening Balance">
                    <i class="bi bi-pencil"></i>
                </button>
            </span>
        </div>
    </div>
    <div class="cart-title">
        <span><i class="bi bi-cart3 me-1"></i> Cart</span>
        <span class="badge" x-text="$store.pos.cartCount">0</span>
    </div>
    <div id="mobileCartItems">
        <template x-if="$store.pos.cart.length === 0">
            <div class="cart-empty"><i class="bi bi-basket"></i> Empty Cart</div>
        </template>
        <template x-for="item in $store.pos.cart" :key="item.id">
            <div class="cart-item">
                <span x-text="(item.icon || '🍽️') + ' ' + item.name + ' ×' + item.qty"></span>
                <span>
                    <span x-text="'Rp ' + $store.pos.formatRupiah(item.price * item.qty)"></span>
                    <button class="remove-btn" @click="$store.pos.decrementQty(item.id)">
                        <i class="bi bi-dash-circle"></i>
                    </button>
                    <button class="clear-btn" x-show="item.qty > 2" @click="$store.pos.resetTo(item.id, 2)"
                        title="Reset to 2">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </span>
            </div>
        </template>
    </div>
    <div class="cart-total">
        <span>Total</span>
        <span x-text="'Rp ' + $store.pos.formatRupiah($store.pos.cartTotal)"></span>
    </div>

    <button class="btn-checkout" :disabled="$store.pos.cartCount === 0" @click="$store.pos.openCheckout()">Checkout
        <i class="bi bi-arrow-right"></i></button>
    <button class="btn-add-manual additional-btn" @click="$store.pos.openAddMenu('additional')">
        <i class="bi bi-plus-circle me-1"></i> Add Additional Menu
    </button>
    <!-- <button class="btn-add-manual" @click="$store.pos.openAddMenu()"><i class="bi bi-plus-circle me-1"></i> Add
        Manual Menu</button> -->
</div>