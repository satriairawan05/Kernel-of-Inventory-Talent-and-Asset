<!-- ===== RECEIPT PRINT TEMPLATE ===== -->
<div id="strukContainer" style="display:none;">
    <div class="struk-content" :class="'paper-' + $store.pos.defaultPrinterSize">
        <div class="struk-header" style="text-align:center;">
            <h3 style="margin:0;font-size:1.1rem;">My Fried Chicken</h3>
            <p style="margin:0;font-size:0.8rem;">Pusat</p>
            <hr style="margin:4px 0;">
        </div>

        <div class="struk-info" style="font-size:0.75rem;">
            <p style="margin:2px 0;display:flex;justify-content:space-between;">
                <strong>Kasir</strong> <span x-text="$store.pos.cashierName"></span>
            </p>
            <p style="margin:2px 0;display:flex;justify-content:space-between;">
                <strong>Waktu</strong> <span x-text="$store.pos.strukData.timestamp"></span>
            </p>
            <p style="margin:2px 0;display:flex;justify-content:space-between;">
                <strong>No. Struk</strong> <span x-text="'#' + $store.pos.strukData.id"></span>
            </p>
            <p style="margin:2px 0;display:flex;justify-content:space-between;">
                <strong>Bayar</strong> <span x-text="$store.pos.strukData.method === 'Cash' ? 'Tunai' : 'QRIS'"></span>
            </p>
            <hr style="margin:4px 0;">
        </div>

        <div class="struk-status" style="text-align:center;font-weight:700;font-size:0.9rem;">
            <p style="margin:2px 0;color:#28a745;">LUNAS</p>
            <hr style="margin:4px 0;">
        </div>

        <div class="struk-items" style="font-size:0.7rem;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left;">Item</th>
                        <th style="text-align:center;">Qty</th>
                        <th style="text-align:right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="item in $store.pos.strukData.items" :key="item.name">
                        <tr>
                            <td style="text-align:left;" x-text="item.name"></td>
                            <td style="text-align:center;" x-text="$store.pos.formatRupiah(item.price) + ' x ' + item.qty"></td>
                            <td style="text-align:right;" x-text="$store.pos.formatRupiah(item.subtotal)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="struk-subtotal" style="font-size:0.75rem;margin-top:4px;">
            <hr style="margin:4px 0;">
            <p style="margin:2px 0;display:flex;justify-content:space-between;">
                <strong>Subtotal</strong> <span x-text="'Rp' + $store.pos.formatRupiah($store.pos.strukData.subtotal)"></span>
            </p>
        </div>

        <div class="struk-discount" style="font-size:0.75rem;"
            x-show="$store.pos.strukData.discount && $store.pos.strukData.discount > 0">
            <p style="margin:2px 0;display:flex;justify-content:space-between;color:#ff0000;">
                <strong>Diskon</strong> <span x-text="'-Rp ' + $store.pos.formatRupiah($store.pos.strukData.discount)"></span>
            </p>
        </div>

        <div class="struk-total" style="font-size:0.75rem;margin-top:2px;">
            <hr style="margin:4px 0;">
            <p style="margin:2px 0;display:flex;justify-content:space-between;font-weight:700;">
                <strong>Total (<span x-text="$store.pos.strukData.totalQty"></span>)</strong> <span
                    x-text="'Rp' + $store.pos.formatRupiah($store.pos.strukData.total)"></span>
            </p>
            <hr style="margin:4px 0;">
        </div>

        <div class="struk-payment" style="font-size:0.75rem;">
            <p style="margin:2px 0;display:flex;justify-content:space-between;">
                <strong>Bayar</strong> <span x-text="'Rp' + $store.pos.formatRupiah($store.pos.strukData.paid)"></span>
            </p>
            <p style="margin:2px 0;display:flex;justify-content:space-between;">
                <strong>Kembali</strong> <span x-text="'Rp' + $store.pos.formatRupiah($store.pos.strukData.change)"></span>
            </p>
        </div>

        <div class="struk-footer" style="text-align:center;font-size:0.7rem;margin-top:4px;">
            <hr style="margin:4px 0;">
            <p style="margin:2px 0;">Powered by KitaPOS</p>
            <p style="margin:2px 0;font-size:0.6rem;color:#888;">Terima kasih</p>
        </div>
    </div>
</div>