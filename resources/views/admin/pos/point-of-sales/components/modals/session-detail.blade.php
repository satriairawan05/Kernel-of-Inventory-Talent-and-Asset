<div class="modal fade" id="sessionDetailModal" tabindex="-1" aria-hidden="true" x-data="{}">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--pos-primary);color:#fff;">
                <h5 class="modal-title">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    <span
                        x-text="$store.pos.selectedSession ? $store.pos.selectedSession.name : 'Session Detail'"></span>
                    <span class="badge bg-light text-dark ms-2"
                        x-text="$store.pos.selectedSession ? $store.pos.selectedSession.typeLabel : ''"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    style="filter:brightness(0) invert(1);"></button>
            </div>
            <div class="modal-body">
                <template x-if="$store.pos.selectedSession && $store.pos.selectedSession.items.length > 0">
                    <div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(item, index) in $store.pos.selectedSession.items" :key="item.id">
                                        <tr>
                                            <td x-text="index + 1"></td>
                                            <td>
                                                <span x-text="item.icon || '🍽️'"></span>
                                                <span class="fw-semibold" x-text="item.name"></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary" x-text="item.qty + 'x'"></span>
                                            </td>
                                            <td class="text-end"
                                                x-text="'Rp ' + $store.pos.formatRupiah(item.price)"></td>
                                            <td class="text-end fw-bold"
                                                x-text="'Rp ' + $store.pos.formatRupiah(item.price * item.qty)">
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot>
                                    <tr class="table-active">
                                        <td colspan="4" class="text-end fw-bold">Subtotal</td>
                                        <td class="text-end fw-bold"
                                            x-text="'Rp ' + $store.pos.formatRupiah($store.pos.getSessionTotal($store.pos.selectedSession.id))">
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="mt-2 text-muted small">
                            <i class="bi bi-info-circle"></i>
                            <span x-text="$store.pos.selectedSession.items.length + ' item(s) total'"></span>
                        </div>
                    </div>
                </template>
                <template x-if="!$store.pos.selectedSession || $store.pos.selectedSession.items.length === 0">
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        <p>No items in this session.</p>
                    </div>
                </template>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success"
                    @click="$store.pos.confirmSessionToCart($store.pos.selectedSession.id); bootstrap.Modal.getInstance(document.getElementById('sessionDetailModal')).hide();"
                    :disabled="!$store.pos.selectedSession || $store.pos.selectedSession.items.length === 0">
                    <i class="bi bi-arrow-right me-1"></i> Go To Cart
                </button>
            </div>
        </div>
    </div>
</div>