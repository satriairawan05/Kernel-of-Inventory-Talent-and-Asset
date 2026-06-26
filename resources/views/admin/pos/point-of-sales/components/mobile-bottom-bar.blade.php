<div class="fixed-bottom d-lg-none bg-white border-top p-3 shadow-lg"
    x-show="$store.pos.cartCount > 0 || ($store.pos.getTotalSessionsCount() > 0)"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="transform translate-y-full"
    x-transition:enter-end="transform translate-y-0" style="z-index: 1050;">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <div class="d-flex gap-1 mb-1">
                <span class="badge bg-danger text-white" x-show="$store.pos.getTotalSessionsCount() > 0"
                    x-text="'Draft: ' + $store.pos.getTotalSessionsCount()"></span>
                <span class="badge bg-primary text-white" x-show="$store.pos.cartCount > 0"
                    x-text="'Cart: ' + $store.pos.cartCount"></span>
            </div>
            <div class="fw-bold text-dark" style="font-size: 16px;"
                x-text="'Total: Rp ' + $store.pos.formatRupiah($store.pos.cartTotal + $store.pos.getTotalSessionsTotal())">
            </div>
        </div>
        <button class="btn btn-dark fw-semibold px-4" @click="$store.pos.toggleMobileCart()">
            See Order <i class="bi bi-chevron-up ms-1"></i>
        </button>
    </div>
</div>