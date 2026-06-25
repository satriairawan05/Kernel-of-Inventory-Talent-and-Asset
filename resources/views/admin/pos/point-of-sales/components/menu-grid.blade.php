<div x-data="menuGridComponent">
    <div class="menu-grid" id="menuGrid">
        <template x-for="item in $store.pos.filteredMenu" :key="item.id">
            <div class="menu-card" :class="{ 'additional-card': item.category === 'additional' }">
                <template x-if="item.category !== 'additional'">
                    <div class="menu-img">
                        <template x-if="item.image">
                            <img :src="item.image" :alt="item.name" loading="lazy" />
                        </template>
                        <template x-if="!item.image">
                            <span class="no-image" x-text="item.icon || '🍽️'"></span>
                        </template>
                    </div>
                </template>
                <template x-if="item.category === 'additional'">
                    <div class="menu-img additional-placeholder">
                        <span class="no-image">➕</span>
                        <div class="additional-label">Additional</div>
                    </div>
                </template>
                <div class="menu-name" x-text="item.name"></div>
                <div class="menu-price" x-text="'Rp ' + $store.pos.formatRupiah(item.price)"></div>
                <template x-if="item.category !== 'additional'">
                    <span class="menu-status" :class="item.status"
                        x-text="item.status === 'available' ? '✅ Available' : item.status === 'low' ? '⚠️ Low Stock' : '❌ Out of Stock'"></span>
                </template>

                <div class="menu-actions">
                    <div class="qty-control">
                        <button class="btn-qty btn-qty-minus" @click="$store.pos.decrementDraftQty(item.id)"
                            x-show="$store.pos.getDraftQty(item.id) > 0" title="Kurangi dari draft">
                            <i class="bi bi-dash"></i>
                        </button>
                        <input type="number" class="qty-input" :value="$store.pos.getDisplayDraftQty(item.id)"
                            @change="$store.pos.updateDraftQtyFromInput(item.id, $event)"
                            :readonly="item.status === 'out'" min="0" />
                        <button class="btn-qty btn-qty-plus" @click="$store.pos.incrementDraftQty(item.id)"
                            :disabled="item.status === 'out'"
                            :title="item.status === 'out' ? 'Habis' : 'Tambah ke Draft'">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                    <button class="btn-action btn-edit-action" @click="$store.pos.openEditMenu(item.id)"
                        title="Edit menu">
                        <i class="bi bi-pencil"></i>
                    </button>
                </div>
            </div>
        </template>
    </div>
    <div id="menuEmpty" class="text-center py-5" x-show="$store.pos.filteredMenu.length === 0" style="display:none;">
        <i class="bi bi-inbox fs-1 text-muted"></i>
        <p class="text-muted mt-2">No menu found</p>
    </div>
</div>