<?php

namespace App\Services;

use App\Enums\CashSummaryTypeEnum;
use App\Models\CashSummary;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashSummaryService
{
    /**
     * Get all cash summaries with optional filters.
     */
    public function getAll(?int $companyId = null, array $filters = []): Collection
    {
        $query = CashSummary::query();

        if ($companyId) {
            $query->byCompany($companyId);
        }

        // Perbaikan: Hapus batasan default hari ini agar semua data tampil
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->betweenDates($filters['start_date'], $filters['end_date']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['search'])) {
            $query->where('description', 'LIKE', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('transaction_date', 'desc')
                     ->orderBy('id', 'desc')
                     ->get();
    }

    /**
     * Get paginated cash summaries.
     */
    public function getPaginated(?int $companyId = null, int $perPage = 15, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = CashSummary::query();

        if ($companyId) {
            $query->byCompany($companyId);
        }

        // Perbaikan: Hapus batasan default hari ini
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->betweenDates($filters['start_date'], $filters['end_date']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['search'])) {
            $query->where('description', 'LIKE', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('transaction_date', 'desc')
                     ->orderBy('id', 'desc')
                     ->paginate($perPage);
    }

    public function getById(int $id): ?CashSummary
    {
        return CashSummary::find($id);
    }

    public function getByIdOrFail(int $id): CashSummary
    {
        return CashSummary::findOrFail($id);
    }

    public function store(array $data): CashSummary
    {
        try {
            return DB::transaction(function () use ($data) {
                if (!in_array($data['type'], CashSummaryTypeEnum::values())) {
                    throw new \InvalidArgumentException('Invalid cash summary type.');
                }

                if (empty($data['transaction_date'])) {
                    $data['transaction_date'] = now()->toDateString();
                }

                return CashSummary::create($data);
            });
        } catch (\Exception $e) {
            Log::error('Failed to create cash summary: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update(CashSummary $cashSummary, array $data): CashSummary
    {
        try {
            return DB::transaction(function () use ($cashSummary, $data) {
                if (isset($data['type']) && !in_array($data['type'], CashSummaryTypeEnum::values())) {
                    throw new \InvalidArgumentException('Invalid cash summary type.');
                }

                $cashSummary->update($data);
                return $cashSummary->fresh();
            });
        } catch (\Exception $e) {
            Log::error("Failed to update cash summary (ID: {$cashSummary->id}): " . $e->getMessage());
            throw $e;
        }
    }

    public function destroy(CashSummary $cashSummary)
    {
        try {
            return DB::transaction(function () use ($cashSummary) {
                return $cashSummary->delete();
            });
        } catch (\Exception $e) {
            Log::error("Failed to delete cash summary (ID: {$cashSummary->id}): " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteAll(): void
    {
        try {
            DB::transaction(function () {
                CashSummary::query()->delete();
            });
        } catch (\Exception $e) {
            Log::error('Failed to delete all cash summaries: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getTotalCashIn(int $companyId, ?string $start = null, ?string $end = null): int
    {
        // Gunakan where langsung agar tidak bergantung pada scope Model
        $types = CashSummaryTypeEnum::values();
        $inType = $types[0] ?? 'in';

        $query = CashSummary::byCompany($companyId)->where('type', $inType);

        if (!empty($start) && !empty($end)) {
            $query->betweenDates($start, $end);
        }

        return (int) $query->sum('amount');
    }

    public function getTotalCashOut(int $companyId, ?string $start = null, ?string $end = null): int
    {
        $types = CashSummaryTypeEnum::values();
        $outType = $types[1] ?? 'out';

        $query = CashSummary::byCompany($companyId)->where('type', $outType);

        if (!empty($start) && !empty($end)) {
            $query->betweenDates($start, $end);
        }

        return (int) $query->sum('amount');
    }

    public function getBalance(int $companyId, ?string $start = null, ?string $end = null): int
    {
        return $this->getTotalCashIn($companyId, $start, $end) - $this->getTotalCashOut($companyId, $start, $end);
    }

    public function getDailySummaryPaginated(int $companyId, int $perPage = 15, ?string $start = null, ?string $end = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        if (!$start || !$end) {
            $start = now()->startOfMonth()->toDateString();
            $end = now()->endOfMonth()->toDateString();
        }

        try {
            $startDate = \Carbon\Carbon::parse($start)->startOfDay();
            $endDate = \Carbon\Carbon::parse($end)->endOfDay();
        } catch (\Exception $e) {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        }

        $query = CashSummary::byCompany($companyId)
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        $paginated = $query
            ->select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('SUM(CASE WHEN type = "in" THEN amount ELSE 0 END) as total_in'),
                DB::raw('SUM(CASE WHEN type = "out" THEN amount ELSE 0 END) as total_out'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->paginate($perPage);

        $paginated->getCollection()->transform(function ($item) {
            return [
                'date'              => $item->date,
                'total_in'          => (int) $item->total_in,
                'total_out'         => (int) $item->total_out,
                'balance'           => (int) ($item->total_in - $item->total_out),
                'count'             => (int) $item->count,
                'formatted_in'      => 'Rp ' . number_format($item->total_in, 0, ',', '.'),
                'formatted_out'     => 'Rp ' . number_format($item->total_out, 0, ',', '.'),
                'formatted_balance' => 'Rp ' . number_format($item->total_in - $item->total_out, 0, ',', '.'),
            ];
        });

        return $paginated;
    }

    public function getSummary(int $companyId, ?string $start = null, ?string $end = null): array
    {
        $query = CashSummary::byCompany($companyId);

        // Hanya filter tanggal JIKA user benar-benar mengisi start_date & end_date
        if (!empty($start) && !empty($end)) {
            try {
                $startDate = \Carbon\Carbon::parse($start)->startOfDay();
                $endDate = \Carbon\Carbon::parse($end)->endOfDay();
                $query->whereBetween('transaction_date', [$startDate, $endDate]);
            } catch (\Exception $e) {
                // Abaikan jika format tanggal salah, biarkan tanpa filter
            }
        }

        // Ambil nilai dari Enum (Lebih aman dari typo)
        // Sesuaikan CashSummaryTypeEnum::values() jika strukturnya berbeda
        $types = CashSummaryTypeEnum::values();
        $inType = $types[0] ?? 'in';   // Default fallback 'in'
        $outType = $types[1] ?? 'out'; // Default fallback 'out'

        $totalIn = (clone $query)->where('type', $inType)->sum('amount');
        $totalOut = (clone $query)->where('type', $outType)->sum('amount');
        $count = (clone $query)->count();

        return [
            'total_in'          => (int) $totalIn,
            'total_out'         => (int) $totalOut,
            'balance'           => (int) ($totalIn - $totalOut),
            'count'             => (int) $count,
            'formatted_in'      => 'Rp ' . number_format($totalIn, 0, ',', '.'),
            'formatted_out'     => 'Rp ' . number_format($totalOut, 0, ',', '.'),
            'formatted_balance' => 'Rp ' . number_format($totalIn - $totalOut, 0, ',', '.'),
        ];
    }

    public function getDailySummary(int $companyId, ?string $start = null, ?string $end = null): \Illuminate\Support\Collection
    {
        $query = CashSummary::byCompany($companyId);

        if ($start && $end) {
            $query->betweenDates($start, $end);
        } else {
            // Default 30 hari terakhir agar tidak kosong
            $query->whereDate('transaction_date', '>=', now()->subDays(30)->toDateString());
        }

        return $query
            ->select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('SUM(CASE WHEN type = "in" THEN amount ELSE 0 END) as total_in'),
                DB::raw('SUM(CASE WHEN type = "out" THEN amount ELSE 0 END) as total_out'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'date'              => $item->date,
                    'total_in'          => (int) $item->total_in,
                    'total_out'         => (int) $item->total_out,
                    'balance'           => (int) ($item->total_in - $item->total_out),
                    'count'             => (int) $item->count,
                    'formatted_in'      => 'Rp ' . number_format($item->total_in, 0, ',', '.'),
                    'formatted_out'     => 'Rp ' . number_format($item->total_out, 0, ',', '.'),
                    'formatted_balance' => 'Rp ' . number_format($item->total_in - $item->total_out, 0, ',', '.'),
                ];
            });
    }

    public function getByDate(int $companyId, string $date): Collection
    {
        return CashSummary::byCompany($companyId)
            ->whereDate('transaction_date', $date)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getCashInRecords(int $companyId, ?string $start = null, ?string $end = null): Collection
    {
        $query = CashSummary::byCompany($companyId)->cashIn();

        if ($start && $end) {
            $query->betweenDates($start, $end);
        }

        return $query->orderBy('transaction_date', 'desc')->get();
    }

    public function getCashOutRecords(int $companyId, ?string $start = null, ?string $end = null): Collection
    {
        $query = CashSummary::byCompany($companyId)->cashOut();

        if ($start && $end) {
            $query->betweenDates($start, $end);
        }

        return $query->orderBy('transaction_date', 'desc')->get();
    }

    public function getCount(int $companyId, ?string $start = null, ?string $end = null): int
    {
        $query = CashSummary::byCompany($companyId);

        if ($start && $end) {
            $query->betweenDates($start, $end);
        }

        return $query->count();
    }

    public function getLatest(int $companyId): ?CashSummary
    {
        return CashSummary::byCompany($companyId)
            // Perbaikan: Hapus filter hari ini agar benar-benar mengambil data terakhir di database
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function exists(int $id): bool
    {
        return CashSummary::where('id', $id)->exists();
    }
}