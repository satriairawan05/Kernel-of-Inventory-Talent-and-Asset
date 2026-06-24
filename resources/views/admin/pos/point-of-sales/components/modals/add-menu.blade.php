<!-- ===== ADD MENU MODAL ===== -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true" x-data="addEditMenuComponent">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>
                    <span x-text="$store.pos.newItem.category === 'additional' ? 'Add Additional Menu' : 'Add Menu'"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addItemForm" @submit.prevent="$store.pos.saveNewItem()">
                    <div class="mb-3">
                        <label class="form-label fw-600">Menu Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Example: Fried Rice"
                            x-model="$store.pos.newItem.name" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Price (Rp) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Example: 25.000"
                            x-model="$store.pos.newItem.price" @input="$store.pos.formatPriceInput($event)" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Category</label>
                        <select class="form-select select2-custom" id="manualCategory" x-model="$store.pos.newItem.category"
                            @change="$store.pos.onCategoryChange()">
                            <option value="food">🍔 Food</option>
                            <option value="drink">🥤 Drinks</option>
                            <option value="snack">🍿 Snacks</option>
                            <option value="additional">➕ Additional</option>
                        </select>
                    </div>
                    <template x-if="$store.pos.newItem.category !== 'additional'">
                        <div>
                            <div class="mb-3">
                                <label class="form-label fw-600">Stock Status</label>
                                <select class="form-select select2-custom" id="manualStatus"
                                    x-model="$store.pos.newItem.status">
                                    <option value="available">✅ Available</option>
                                    <option value="low">⚠️ Low Stock</option>
                                    <option value="out">❌ Out of Stock</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-600">Menu Image</label>
                                <input type="file" class="form-control" id="manualImage" accept="image/*"
                                    @change="$store.pos.handleImageUpload($event)" />
                                <div class="mt-2" x-show="$store.pos.newItem.imagePreview">
                                    <img :src="$store.pos.newItem.imagePreview" alt="Preview" loading="lazy"
                                        style="max-width:100%;max-height:150px;border-radius:8px;border:1px solid #ddd;" />
                                </div>
                            </div>
                        </div>
                    </template>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-accent" @click="$store.pos.saveNewItem()">
                    <i class="bi bi-save me-1"></i> Save Menu
                </button>
            </div>
        </div>
    </div>
</div>