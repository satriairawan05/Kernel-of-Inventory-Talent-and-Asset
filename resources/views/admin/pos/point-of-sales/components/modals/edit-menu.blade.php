<!-- ===== EDIT MENU MODAL ===== -->
<div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true" x-data="addEditMenuComponent">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--pos-primary);color:#fff;">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    style="filter:brightness(0) invert(1);"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="$store.pos.saveEditItem()">
                    <div class="mb-3">
                        <label class="form-label fw-600">Menu Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Example: Fried Rice"
                            x-model="$store.pos.editItem.name" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Price (Rp) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Example: 25.000"
                            x-model="$store.pos.editItem.price" @input="$store.pos.formatPriceInput($event)" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Category</label>
                        <select class="form-select select2-custom" id="editCategory" x-model="$store.pos.editItem.category">
                            <option value="food">🍔 Food</option>
                            <option value="drink">🥤 Drinks</option>
                            <option value="snack">🍿 Snacks</option>
                            <option value="additional">➕ Additional</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Stock Status</label>
                        <select class="form-select select2-custom" id="editStatus" x-model="$store.pos.editItem.status">
                            <option value="available">✅ Available</option>
                            <option value="low">⚠️ Low Stock</option>
                            <option value="out">❌ Out of Stock</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Menu Image</label>
                        <input type="file" class="form-control" accept="image/*"
                            @change="$store.pos.handleEditImageUpload($event)" />
                        <div class="mt-2">
                            <img :src="$store.pos.editItem.imagePreview || '#'" alt="Preview" loading="lazy"
                                style="max-width:100%;max-height:150px;border-radius:8px;border:1px solid #ddd;display:block;"
                                x-show="$store.pos.editItem.imagePreview" />
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-accent" @click="$store.pos.saveEditItem()">
                    <i class="bi bi-save me-1"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>