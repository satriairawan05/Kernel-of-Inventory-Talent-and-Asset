<!-- ===== HISTORY MODAL ===== -->
<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true" x-data="historyComponent">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: var(--pos-radius);">
            <div class="modal-header bg-dark">
                <h5 class="modal-title fw-bold" id="historyModalLabel">
                    <i class="bi bi-clock-history me-2 text-danger"></i>History Transaction
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-4 border-0 bg-light-subtle shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-sliders text-danger me-2 fs-5"></i>
                                <span class="fw-bold text-dark">System Setting (Receipt Printer)</span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="printerSizeSetting"
                                        id="size58" value="58mm" x-model="$store.pos.defaultPrinterSize"
                                        @change="$store.pos.applyPrinterSize()">
                                    <label class="form-check-label fw-semibold text-secondary" for="size58">
                                        🖨️ Thermal 58mm
                                    </label>
                                </div>
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="printerSizeSetting"
                                        id="size80" value="80mm" x-model="$store.pos.defaultPrinterSize"
                                        @change="$store.pos.applyPrinterSize()">
                                    <label class="form-check-label fw-semibold text-secondary" for="size80">
                                        🖨️ Thermal 80mm
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="history-opening-balance">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="label"><i class="bi bi-wallet2 me-2"></i>Opening Balance</span>
                            <span class="total" x-text="'Rp ' + $store.pos.formatRupiah($store.pos.openingBalance)"></span>
                        </div>
                    </div>
                    <div class="history-total-transactions">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="label"><i class="bi bi-receipt me-2"></i>Total Transactions</span>
                            <span class="total" x-text="'Rp ' + $store.pos.formatRupiah($store.pos.totalTransactions)"></span>
                        </div>
                    </div>
                    <div class="history-grand-total">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="label"><i class="bi bi-cash-stack me-2"></i>Grand Total</span>
                            <span class="total" x-text="'Rp ' + $store.pos.formatRupiah($store.pos.grandTotal)"></span>
                        </div>
                    </div>
                    <hr />
                    <template x-if="$store.pos.transactionHistory.length === 0">
                        <div class="history-empty">
                            <i class="bi bi-inbox"></i>
                            <p>No transactions yet</p>
                        </div>
                    </template>
                    <template x-for="trx in $store.pos.transactionHistory.slice().reverse()" :key="trx.id">
                        <div class="history-item">
                            <div class="header">
                                <span x-text="'#' + trx.id + ' - ' + trx.timestamp"></span>
                                <div class="history-actions">
                                    <span class="text-accent" x-text="'Rp ' + $store.pos.formatRupiah(trx.total)"></span>
                                    <button class="print-history-btn" @click="$store.pos.printStrukMobile(trx)"
                                        title="Print receipt">
                                        <i class="bi bi-printer"></i>
                                    </button>
                                    <button class="delete-history-btn" @click="$store.pos.deleteTransaction(trx.id)"
                                        title="Delete transaction">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="detail">
                                <span><i class="bi bi-tag"></i> <span x-text="trx.method"></span></span>
                                <span><i class="bi bi-cash-stack"></i> Paid: <span
                                        x-text="'Rp ' + $store.pos.formatRupiah(trx.paid)"></span></span>
                                <template x-if="trx.discount && trx.discount > 0">
                                    <span><i class="bi bi-percent"></i> Discount: <span
                                            x-text="'-Rp ' + $store.pos.formatRupiah(trx.discount)"></span></span>
                                </template>
                                <template x-if="trx.method === 'Cash'">
                                    <span><i class="bi bi-arrow-return-left"></i> Change: <span
                                            x-text="'Rp ' + $store.pos.formatRupiah(trx.change)"></span></span>
                                </template>
                            </div>
                            <div class="detail" style="font-size:0.8rem;color:#888;">
                                <i class="bi bi-list-ul"></i>
                                <span
                                    x-text="trx.items.map(item => item.name + ' (' + item.qty + '×Rp' + $store.pos.formatRupiah(item.price) + ')').join(', ')"></span>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" @click="$store.pos.clearAllTransactions()">Clear All</button>
                </div>
            </div>
        </div>
    </div>
</div>