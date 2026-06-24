<!-- ===== EDIT OPENING BALANCE MODAL ===== -->
<div class="modal fade" id="editOpeningBalanceModal" tabindex="-1" aria-hidden="true" x-data="{}">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--pos-primary);color:#fff;">
                <h5 class="modal-title"><i class="bi bi-wallet2 me-2"></i>Edit Opening Balance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    style="filter:brightness(0) invert(1);"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-600">Opening Balance (Rp)</label>
                    <input type="text" class="form-control" placeholder="Example: 150.000"
                        x-model="$store.pos.editOpeningBalance" @input="$store.pos.formatPriceInput($event)" />
                </div>
                <div class="mb-0 text-muted small">
                    <i class="bi bi-info-circle"></i> This balance will be added to Grand Total in History.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-accent" @click="$store.pos.saveOpeningBalance()">
                    <i class="bi bi-save me-1"></i> Save Balance
                </button>
            </div>
        </div>
    </div>
</div>