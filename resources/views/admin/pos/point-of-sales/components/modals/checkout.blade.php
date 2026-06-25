<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true" x-data="checkoutComponent">
    <div class="modal-dialog modal-dialog-centered modal-full-screen">
        <div class="modal-content">
            <div class="modal-header" style="background:#28a745;color:#fff;border-radius:0;">
                <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Confirm Checkout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    style="filter:brightness(0) invert(1);"></button>
            </div>
            <div class="modal-body">
                <div class="checkout-summary" id="checkoutSummary">
                    <p class="fw-600 mb-2">Ordered items:</p>
                    <template x-for="item in $store.pos.cart" :key="item.id">
                        <div class="item-row">
                            <span x-text="(item.icon || '🍽️') + ' ' + item.name + ' × ' + item.qty"></span>
                            <span x-text="'Rp ' + $store.pos.formatRupiah(item.price * item.qty)"></span>
                        </div>
                    </template>
                    <div class="total-row" style="border-top: 1px solid #ddd; margin-top: 4px; padding-top: 8px;">
                        <span>Subtotal</span>
                        <span x-text="'Rp ' + $store.pos.formatRupiah($store.pos.cartTotal)"></span>
                    </div>
                    <div class="row mt-2 align-items-center"
                        style="background: #f8f9fa; border-radius: 6px; padding: 6px 0;">
                        <div class="col-4">
                            <span class="fw-600">Discount</span>
                        </div>
                        <div class="col-8 d-flex align-items-center gap-1">
                            <input type="text" class="form-control form-control-sm"
                                :value="$store.pos.discountDisplay" @input="$store.pos.updateDiscount($event)"
                                placeholder="0" style="min-width: 80px; text-align: right;" />
                            <select class="form-select form-select-sm" style="width: auto;"
                                x-model="$store.pos.discountType" @change="$store.pos.reformatDiscountDisplay()">
                                <option value="rp">Rp</option>
                                <option value="percent">%</option>
                            </select>
                        </div>
                    </div>
                    <div x-show="$store.pos.discountAmount > 0"
                        class="d-flex justify-content-between text-danger fw-600 mt-1">
                        <span>Discount Amount</span>
                        <span x-text="'- Rp ' + $store.pos.formatRupiah($store.pos.discountAmount)"></span>
                    </div>
                    <div class="total-row"
                        style="border-top: 2px solid var(--pos-accent); margin-top: 6px; padding-top: 8px; font-size: 1.1rem;">
                        <span class="fw-700">Total</span>
                        <span class="fw-700 text-accent"
                            x-text="'Rp ' + $store.pos.formatRupiah($store.pos.discountedTotal)"></span>
                    </div>
                </div>
                <hr />
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-700 fs-5">Grand Total</span>
                    <span class="fw-700 fs-4 text-accent"
                        x-text="'Rp ' + $store.pos.formatRupiah($store.pos.discountedTotal)"></span>
                </div>
                <div class="mt-3">
                    <label class="form-label fw-600">Payment Method</label>
                    <select class="form-select select2-custom" id="paymentMethod" x-model="$store.pos.paymentMethod"
                        @change="$store.pos.handlePaymentMethodChange()">
                        <option value="cash">💵 Cash</option>
                        <option value="qris">📱 QRIS</option>
                    </select>
                </div>

                <div class="mt-3" id="paymentInputSection" x-show="$store.pos.paymentMethod === 'cash'">
                    <label class="form-label fw-600">Pay (Rp)</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control" placeholder="Enter amount"
                            x-model="$store.pos.paymentAmount" @input="$store.pos.updateChange()" />
                    </div>
                    <div class="mt-2">
                        <template x-for="(val, idx) in $store.pos.quickPayOptions" :key="idx">
                            <button class="quick-pay-btn"
                                :class="{'btn-exact': val === $store.pos.discountedTotal, 'active-btn': parseInt($store.pos.paymentAmountRaw) === val}"
                                @click="$store.pos.setQuickPay(val)">
                                <span
                                    x-text="val === $store.pos.discountedTotal ? 'Exact' : 'Rp ' + $store.pos.formatRupiah(val)"></span>
                            </button>
                        </template>
                    </div>
                    <div class="mt-2 text-end" style="font-weight:700;font-size:1.1rem;">
                        Change: <span :style="{color: $store.pos.changeAmount >= 0 ? 'var(--pos-accent)' : '#e74c3c'}"
                            x-text="'Rp ' + $store.pos.formatRupiah(Math.abs($store.pos.changeAmount)) + ($store.pos.changeAmount < 0 ? ' (insufficient)' : '')"></span>
                    </div>
                </div>

                <div class="mt-2 text-center text-success fw-600" x-show="$store.pos.paymentMethod === 'qris'">
                    <i class="bi bi-qr-code me-1"></i> Please scan QR Code to complete payment.
                    <div class="mt-1 small">
                        Total: Rp <span x-text="$store.pos.formatRupiah($store.pos.discountedTotal)"></span>
                    </div>
                </div>

                <div class="mt-2"
                    x-show="$store.pos.paymentMethod === 'cash' && $store.pos.paymentAmountRaw < $store.pos.discountedTotal">
                    <div class="text-danger small">
                        <i class="bi bi-exclamation-circle"></i>
                        <span
                            x-text="'Payment amount (Rp ' + $store.pos.formatRupiah($store.pos.paymentAmountRaw) + ') is less than total (Rp ' + $store.pos.formatRupiah($store.pos.discountedTotal) + ')'"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-radius:0;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" @click="$store.pos.confirmCheckout()"
                    :disabled="$store.pos.paymentMethod === 'cash' && $store.pos.paymentAmountRaw < $store.pos.discountedTotal">
                    <i class="bi bi-check-lg me-1"></i> Confirm & Finish
                </button>
            </div>
        </div>
    </div>
</div>