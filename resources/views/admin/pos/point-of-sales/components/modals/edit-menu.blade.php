<!-- ===== EDIT MENU MODAL ===== -->
<div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true" x-data="addEditMenuComponent">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: var(--pos-radius); overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.2);">
            <!-- HEADER -->
            <div class="modal-header" style="background: var(--pos-primary); color: #fff; border-bottom: 2px solid var(--pos-primary-dark); padding: 16px 20px;">
                <h5 class="modal-title fw-bold" style="color: #fff;">
                    <i class="bi bi-pencil-square me-2"></i>
                    <span x-text="$store.pos.editItem.category === 'additional' ? 'Edit Additional Menu' : 'Edit Menu'"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: brightness(0) invert(1); opacity: 0.8;"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body" style="background: #fff; padding: 24px 20px;">
                <form @submit.prevent="$store.pos.saveEditItem()">
                    <!-- Nama Menu -->
                    <div class="mb-3">
                        <label class="form-label fw-600" style="color: #334155;">Menu Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Example: Fried Rice"
                            x-model="$store.pos.editItem.name" required
                            style="border-radius: 14px; border-color: #cbd5e1; padding: 0.72rem 0.9rem;" />
                    </div>

                    <!-- Harga -->
                    <div class="mb-3">
                        <label class="form-label fw-600" style="color: #334155;">Price (Rp) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Example: 25.000"
                            x-model="$store.pos.editItem.price" @input="$store.pos.formatPriceInput($event)"
                            style="border-radius: 14px; border-color: #cbd5e1; padding: 0.72rem 0.9rem;" />
                    </div>

                    <!-- Kategori -->
                    <div class="mb-3">
                        <label class="form-label fw-600" style="color: #334155;">Category</label>
                        <select class="form-select select2-custom" id="editCategory"
                            x-model="$store.pos.editItem.category"
                            style="border-radius: 14px; border-color: #cbd5e1; padding: 0.72rem 0.9rem;">
                            <option value="food">🍔 Food</option>
                            <option value="drink">🥤 Drinks</option>
                            <option value="snack">🍿 Snacks</option>
                            <option value="additional">➕ Additional</option>
                        </select>
                    </div>

                    <!-- Field tambahan jika BUKAN additional -->
                    <template x-if="$store.pos.editItem.category !== 'additional'">
                        <div>
                            <div class="mb-3">
                                <label class="form-label fw-600" style="color: #334155;">Stock Status</label>
                                <select class="form-select select2-custom" id="editStatus"
                                    x-model="$store.pos.editItem.status"
                                    style="border-radius: 14px; border-color: #cbd5e1; padding: 0.72rem 0.9rem;">
                                    <option value="available">✅ Available</option>
                                    <option value="low">⚠️ Low Stock</option>
                                    <option value="out">❌ Out of Stock</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-600" style="color: #334155;">Menu Image</label>
                                <input type="file" class="form-control" accept="image/*"
                                    @change="$store.pos.handleEditImageUpload($event)"
                                    style="border-radius: 14px; border-color: #cbd5e1; padding: 0.72rem 0.9rem;" />
                                <div class="mt-2">
                                    <img :src="$store.pos.editItem.imagePreview || '#'" alt="Preview" loading="lazy"
                                        style="max-width:100%; max-height:150px; border-radius:8px; border:1px solid #ddd; padding:4px;"
                                        x-show="$store.pos.editItem.imagePreview" />
                                </div>
                            </div>
                        </div>
                    </template>
                </form>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer" style="border-top: 1px solid #dee2e6; background: #f8f9fa; padding: 16px 20px; border-radius: 0 0 var(--pos-radius) var(--pos-radius);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 50px; padding: 8px 24px; font-weight: 600;">Cancel</button>
                <button type="button" class="btn btn-accent" @click="$store.pos.saveEditItem()" style="border-radius: 50px; padding: 8px 24px; font-weight: 600; background: var(--pos-accent); color: #fff; border: none;">
                    <i class="bi bi-save me-1"></i> Update Menu
                </button>
            </div>
        </div>
    </div>
</div>