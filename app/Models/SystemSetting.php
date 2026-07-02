<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemSetting extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'system_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['company_id','key','value'];

    /**
     * Get the company that owns the system setting.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }


     /**
     * Ambil nilai setting berdasarkan key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Ambil nilai setting dalam bentuk array (khusus untuk JSON).
     *
     * @param string $key
     * @param array $default
     * @return array
     */
    public static function getArrayValue(string $key, array $default = []): array
    {
        $value = static::getValue($key);
        return $value ? json_decode($value, true) : $default;
    }
}
