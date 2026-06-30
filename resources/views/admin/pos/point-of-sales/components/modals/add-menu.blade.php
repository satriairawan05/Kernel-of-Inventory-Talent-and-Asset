<!-- ===== ADD MENU MODAL ===== -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true" x-data="addEditMenuComponent">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: var(--pos-radius); overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.2);">
            <!-- HEADER -->
            <div class="modal-header" style="background: var(--pos-primary); color: #fff; border-bottom: 2px solid var(--pos-primary-dark); padding: 16px 20px;">
                <h5 class="modal-title fw-bold" style="color: #fff;">
                    <i class="bi bi-plus-circle me-2"></i>
                    <span x-text="$store.pos.newItem.category === 'additional' ? 'Add Additional Menu' : 'Add Menu'"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: brightness(0) invert(1); opacity: 0.8;"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body" style="background: #fff; padding: 24px 20px;">
                <form id="addItemForm" @submit.prevent="$store.pos.saveNewItem()">
                    <!-- Nama Menu -->
                    <div class="mb-3">
                        <label class="form-label fw-600" style="color: #334155;">Menu Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Example: Fried Rice"
                            x-model="$store.pos.newItem.name" required
                            style="border-radius: 14px; border-color: #cbd5e1; padding: 0.72rem 0.9rem;" />
                    </div>

                    <!-- Harga -->
                    <div class="mb-3">
                        <label class="form-label fw-600" style="color: #334155;">Price (Rp) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Example: 25.000"
                            x-model="$store.pos.newItem.price" @input="$store.pos.formatPriceInput($event)"
                            style="border-radius: 14px; border-color: #cbd5e1; padding: 0.72rem 0.9rem;" />
                    </div>

                    <!-- Kategori -->
                    <div class="mb-3">
                        <label class="form-label fw-600" style="color: #334155;">Category</label>
                        <select class="form-select select2-custom" id="manualCategory"
                            x-model="$store.pos.newItem.category" @change="$store.pos.onCategoryChange()"
                            style="border-radius: 14px; border-color: #cbd5e1; padding: 0.72rem 0.9rem;">
                            <option value="food">🍔 Food</option>
                            <option value="drink">🥤 Drinks</option>
                            <option value="snack">🍿 Snacks</option>
                            <option value="additional">➕ Additional</option>
                        </select>
                    </div>

                    <!-- Field tambahan jika BUKAN additional -->
                    <template x-if="$store.pos.newItem.category !== 'additional'">
                        <div>
                            <div class="mb-3">
                                <label class="form-label fw-600" style="color: #334155;">Stock Status</label>
                                <select class="form-select select2-custom" id="manualStatus"
                                    x-model="$store.pos.newItem.status"
                                    style="border-radius: 14px; border-color: #cbd5e1; padding: 0.72rem 0.9rem;">
                                    <option value="available">✅ Available</option>
                                    <option value="low">⚠️ Low Stock</option>
                                    <option value="out">❌ Out of Stock</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-600" style="color: #334155;">Menu Image</label>
                                <input type="file" class="form-control" id="manualImage" accept="image/*"
                                    @change="$store.pos.handleImageUpload($event)"
                                    style="border-radius: 14px; border-color: #cbd5e1; padding: 0.72rem 0.9rem;" />
                                <div class="mt-2" x-show="$store.pos.newItem.imagePreview">
                                    <img :src="$store.pos.newItem.imagePreview" alt="Preview" loading="lazy"
                                        style="max-width:100%; max-height:150px; border-radius:8px; border:1px solid #ddd; padding:4px;" />
                                </div>
                            </div>
                        </div>
                    </template>
                </form>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer" style="border-top: 1px solid #dee2e6; background: #f8f9fa; padding: 16px 20px; border-radius: 0 0 var(--pos-radius) var(--pos-radius);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 50px; padding: 8px 24px; font-weight: 600;">Cancel</button>
                <button type="button" class="btn btn-accent" @click="$store.pos.saveNewItem()" style="border-radius: 50px; padding: 8px 24px; font-weight: 600; background: var(--pos-accent); color: #fff; border: none;">
                    <i class="bi bi-save me-1"></i> Save Menu
                </button>
            </div>
        </div>
    </div>
</div>