<?php

namespace App\Models;

use App\Enums\CashierSessionStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * Model untuk mengelola sesi buka/tutup kasir.
 * Satu sesi aktif (status = 'open') hanya boleh ada satu dalam satu waktu.
 *
 * @property int $id
 * @property int $user_id ID user/kasir yang membuka sesi
 * @property string $opening_balance Saldo awal (desimal)
 * @property string|null $closing_balance Saldo akhir saat tutup (desimal)
 * @property string $total_sales Total penjualan selama sesi
 * @property string $total_cash_in Total uang masuk lainnya
 * @property string $total_cash_out Total uang keluar lainnya
 * @property Carbon $opened_at Waktu buka sesi
 * @property Carbon|null $closed_at Waktu tutup sesi (null jika masih buka)
 * @property string $status Status sesi: 'open' atau 'closed'
 * @property string|null $note Catatan tambahan
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read User $user User/kasir yang bertanggung jawab
 * @property-read \Illuminate\Database\Eloquent\Collection|Transaction[] $transactions Daftar transaksi dalam sesi ini
 * @property-read CashSummary|null $cashSummary Rekap kas terkait (jika ada)
 */
class CashierSession extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'cashier_sessions';

    /**
     * Atribut yang boleh diisi secara mass-assignment.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'opening_balance',
        'closing_balance',
        'total_sales',
        'total_cash_in',
        'total_cash_out',
        'opened_at',
        'closed_at',
        'status',
        'note',
    ];

    /**
     * Casting tipe data atribut.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'total_sales'     => 'decimal:2',
        'total_cash_in'   => 'decimal:2',
        'total_cash_out'  => 'decimal:2',
        'opened_at'       => 'datetime',
        'closed_at'       => 'datetime',
        'status'          => CashierSessionStatusEnum::class,
    ];

    // ==================== RELASI ====================

    /**
     * Relasi ke tabel users (kasir yang membuka sesi).
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke tabel transactions (transaksi penjualan dalam sesi ini).
     *
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'session_id');
    }

    /**
     * Relasi ke tabel cash_summary (rekap kas per sesi).
     *
     * @return HasOne
     */
    public function cashSummary(): HasOne
    {
        return $this->hasOne(CashSummary::class, 'session_id');
    }

    // ==================== SCOPE ====================

    /**
     * Scope untuk mengambil sesi yang masih terbuka (status = 'open').
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOpen($query)
    {
        return $query->where('status', CashierSessionStatusEnum::OPEN->value);
    }

    /**
     * Scope untuk mengambil sesi yang sudah ditutup (status = 'closed').
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeClosed($query)
    {
        return $query->where('status', CashierSessionStatusEnum::CLOSED->value);
    }

    // ==================== HELPER / ACCESSOR ====================

    /**
     * Cek apakah sesi ini masih terbuka.
     *
     * @return bool
     */
    public function isOpen(): bool
    {
        return $this->status === CashierSessionStatusEnum::OPEN;
    }

    /**
     * Hitung saldo teoritis berdasarkan data transaksi.
     * Rumus: opening_balance + total_sales + total_cash_in - total_cash_out.
     *
     * @return float
     */
    public function getTheoreticalBalanceAttribute(): float
    {
        return (float) (
            $this->opening_balance +
            $this->total_sales +
            $this->total_cash_in -
            $this->total_cash_out
        );
    }

    /**
     * Mendapatkan sesi aktif yang sedang terbuka (singleton helper).
     *
     * @return CashierSession|null
     */
    public static function getActiveSession(): ?self
    {
        return self::open()->first();
    }
}