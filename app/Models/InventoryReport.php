<?php

namespace App\Models;

use App\Models\InventoryReportItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryReport extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inventory_reports';


     /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'period_id',
        'location',
        'reported_by',
        'report_date',
        'opened_at',
        'closed_at',
        'cashier_name',
        'total_products_sold',
        'notes',
        'created_by',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'report_date' => 'date',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'total_products_sold' => 'decimal:2',
    ];

    // ==================== RELASI ====================

    /**
     * Relasi Many-to-One ke ReportPeriod.
     * Laporan ini terkait dengan satu periode (shift).
     *
     * @return BelongsTo
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(ReportPeriod::class);
    }

    /**
     * Relasi Many-to-One ke User (created_by).
     * Laporan ini dibuat oleh satu pengguna.
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi One-to-Many ke InventoryReportItem.
     * Satu laporan memiliki banyak item produk.
     *
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(InventoryReportItem::class);
    }
}
