<?php

namespace App\Models;

use App\Models\InventoryReport;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryReportItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inventory_report_items';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'inventory_report_id',
        'product_variant_id',
        'first_stock',
        'stock_in',
        'selling',
        'remain',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'first_stock' => 'decimal:2',
        'stock_in' => 'decimal:2',
        'selling' => 'decimal:2',
        'remain' => 'decimal:2',
    ];

    // ==================== RELASI ====================

    /**
     * Relasi Many-to-One ke InventoryReport.
     * Item ini milik satu laporan inventory.
     *
     * @return BelongsTo
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(InventoryReport::class);
    }

    /**
     * Relasi Many-to-One ke ProductVariant.
     * Item ini terkait dengan satu varian produk.
     *
     * @return BelongsTo
     */
    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
