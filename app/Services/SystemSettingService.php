<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class SystemSettingService
{
    /**
     * Store a newly created setting.
     * @param array $data Must contain 'company_id', 'key', 'value'
     * @return SystemSetting
     */
    public function store(array $data): SystemSetting
    {
        return SystemSetting::create($data);
    }

    /**
     * Update an existing setting.
     * @param SystemSetting $systemSetting
     * @param array $data
     * @return SystemSetting
     */
    public function update(SystemSetting $systemSetting, array $data): SystemSetting
    {
        $systemSetting->update($data);
        return $systemSetting;
    }

    /**
     * Delete a setting.
     * @param SystemSetting $systemSetting
     * @return bool|null
     */
    public function destroy(SystemSetting $systemSetting)
    {
        return $systemSetting->delete();
    }

    /**
     * Get a specific setting value for a company.
     * @param int $companyId
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getSetting(int $companyId, string $key, $default = null)
    {
        $setting = SystemSetting::where('company_id', $companyId)
                                ->where('key', $key)
                                ->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Get all settings for a company as key-value array.
     * @param int $companyId
     * @return array
     */
    public function getAllSettings(int $companyId): array
    {
        $settings = SystemSetting::where('company_id', $companyId)->get();
        return $settings->pluck('value', 'key')->toArray();
    }

    /**
     * Update or create a setting for a company.
     * @param int $companyId
     * @param string $key
     * @param mixed $value
     * @return SystemSetting
     */
    public function updateOrCreateSetting(int $companyId, string $key, $value): SystemSetting
    {
        return SystemSetting::updateOrCreate(
            ['company_id' => $companyId, 'key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get opening balance for a specific company.
     * @param int $companyId
     * @return int|null
     */
    public function getOpeningBalance(int $companyId): ?int
    {
        $setting = SystemSetting::where('company_id', $companyId)
            ->where('key', 'opening_balance')
            ->first();

        return $setting ? (int) $setting->value : 0;
    }

    /**
     * Get opening balance for the company of the given user.
     * @param \App\Models\User $user
     * @return int|null
     */
    public function getOpeningBalanceForUser(\App\Models\User $user): ?int
    {
        if (!$user->company_id) {
            return null;
        }
        return $this->getOpeningBalance($user->company_id);
    }

    /**
     * Get printer size for a specific company.
     * @param int $companyId
     * @return int|null
     */
    public function getPrinterSize(int $companyId): ?int
    {
        $setting = SystemSetting::where('company_id',$companyId)
            ->where('key','print_size')
            ->first();
        
        return $setting ? (int) $setting->value : 0;
    }

    /**
     * Get printer size for the company of the given user.
     * @param \App\Models\User $user
     * @return int|null
     */
    public function getPrinterSizeForUser(\App\Models\User $user): ?int
    {
        if (!$user->company_id) {
            return null;
        }
        return $this->getPrinterSize($user->company_id);
    }

    /**
     * Get opening balance with caching (optional).
     * @param int $companyId
     * @param int $ttl (minutes)
     * @return int|null
     */
    public function getOpeningBalanceCached(int $companyId, int $ttl = 60): ?int
    {
        $cacheKey = "opening_balance_{$companyId}";
        return Cache::remember($cacheKey, $ttl, function () use ($companyId) {
            return $this->getOpeningBalance($companyId);
        });
    }
}