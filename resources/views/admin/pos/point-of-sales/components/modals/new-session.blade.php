<div class="modal fade" id="newSessionModal" tabindex="-1" aria-hidden="true" x-data="{}">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--pos-primary);color:#fff;">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Create New Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    style="filter:brightness(0) invert(1);"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-600">Order Type</label>
                    <select class="form-select" x-model="$store.pos.newSessionType">
                        <option value="dinein">Dine In</option>
                        <option value="takeaway">Take Away</option>
                    </select>
                </div>
                <div class="mb-3" x-show="$store.pos.newSessionType === 'dinein'">
                    <label class="form-label fw-600">Table Number</label>
                    <input type="number" class="form-control" placeholder="Example: 1" min="1"
                        x-model="$store.pos.newSessionTable" />
                </div>
                <div class="mb-0 text-muted small">
                    <i class="bi bi-info-circle"></i> After Create, Item add to this session.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" @click="$store.pos.createNewSession()">
                    <i class="bi bi-check-circle me-1"></i> Create
                </button>
            </div>
        </div>
    </div>
</div>