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
     * The CashSummary model instance.
     *
     * @var CashSummary
     */
    protected CashSummary $model;

    /**
     * Constructor.
     *
     * @param CashSummary $model
     */
    public function __construct(CashSummary $model)
    {
        $this->model = $model;
    }

    /**
     * Get all cash summaries with optional filters.
     *
     * @param int|null $companyId
     * @param array $filters
     * @return Collection
     */
    public function getAll(?int $companyId = null, array $filters = []): Collection
    {
        $query = $this->model->query();

        if ($companyId) {
            $query->byCompany($companyId);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->betweenDates($filters['start_date'], $filters['end_date']);
        }

        if (!empty($filters['search'])) {
            $query->where('description', 'LIKE', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('transaction_date', 'desc')->get();
    }

    /**
     * Get paginated cash summaries.
     *
     * @param int|null $companyId
     * @param int $perPage
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginated(?int $companyId = null, int $perPage = 15, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $this->model->query();

        if ($companyId) {
            $query->byCompany($companyId);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->betweenDates($filters['start_date'], $filters['end_date']);
        }

        if (!empty($filters['search'])) {
            $query->where('description', 'LIKE', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('transaction_date', 'desc')->paginate($perPage);
    }

    /**
     * Get a single cash summary by ID.
     *
     * @param int $id
     * @return CashSummary|null
     */
    public function getById(int $id): ?CashSummary
    {
        return $this->model->find($id);
    }

    /**
     * Get a single cash summary by ID or throw exception.
     *
     * @param int $id
     * @return CashSummary
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getByIdOrFail(int $id): CashSummary
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create a new cash summary record.
     *
     * @param array $data
     * @return CashSummary
     * @throws \Exception
     */
    public function store(array $data): CashSummary
    {
        try {
            return DB::transaction(function () use ($data) {
                // Ensure type is valid
                if (!in_array($data['type'], CashSummaryTypeEnum::values())) {
                    throw new \InvalidArgumentException('Invalid cash summary type.');
                }

                // Set default transaction_date if not provided
                if (empty($data['transaction_date'])) {
                    $data['transaction_date'] = now()->toDateString();
                }

                return $this->model->create($data);
            });
        } catch (\Exception $e) {
            Log::error('Failed to create cash summary: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update an existing cash summary record.
     *
     * @param CashSummary $cashSummary
     * @param array $data
     * @return CashSummary
     */
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

    /**
     * Delete a cash summary record.
     *
     * @param CashSummary $cashSummary
     * @return bool|null
     */
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

    /**
     * Get total cash in for a company within optional date range.
     *
     * @param int $companyId
     * @param string|null $start
     * @param string|null $end
     * @return int
     */
    public function getTotalCashIn(int $companyId, ?string $start = null, ?string $end = null): int
    {
        $query = $this->model->byCompany($companyId)->cashIn();

        if ($start && $end) {
            $query->betweenDates($start, $end);
        }

        return (int) $query->sum('amount');
    }

    /**
     * Get total cash out for a company within optional date range.
     *
     * @param int $companyId
     * @param string|null $start
     * @param string|null $end
     * @return int
     */
    public function getTotalCashOut(int $companyId, ?string $start = null, ?string $end = null): int
    {
        $query = $this->model->byCompany($companyId)->cashOut();

        if ($start && $end) {
            $query->betweenDates($start, $end);
        }

        return (int) $query->sum('amount');
    }

    /**
     * Get net balance for a company within optional date range.
     *
     * @param int $companyId
     * @param string|null $start
     * @param string|null $end
     * @return int
     */
    public function getBalance(int $companyId, ?string $start = null, ?string $end = null): int
    {
        $totalIn = $this->getTotalCashIn($companyId, $start, $end);
        $totalOut = $this->getTotalCashOut($companyId, $start, $end);

        return $totalIn - $totalOut;
    }

    /**
     * Get cash summary statistics for a company.
     *
     * @param int $companyId
     * @param string|null $start
     * @param string|null $end
     * @return array
     */
    public function getSummary(int $companyId, ?string $start = null, ?string $end = null): array
    {
        $query = $this->model->byCompany($companyId);

        if ($start && $end) {
            $query->betweenDates($start, $end);
        }

        $totalIn = (clone $query)->cashIn()->sum('amount');
        $totalOut = (clone $query)->cashOut()->sum('amount');
        $count = $query->count();

        return [
            'total_in'       => (int) $totalIn,
            'total_out'      => (int) $totalOut,
            'balance'        => (int) ($totalIn - $totalOut),
            'count'          => (int) $count,
            'formatted_in'   => 'Rp ' . number_format($totalIn, 0, ',', '.'),
            'formatted_out'  => 'Rp ' . number_format($totalOut, 0, ',', '.'),
            'formatted_balance' => 'Rp ' . number_format($totalIn - $totalOut, 0, ',', '.'),
        ];
    }

    /**
     * Get all cash in records for a company.
     *
     * @param int $companyId
     * @param string|null $start
     * @param string|null $end
     * @return Collection
     */
    public function getCashInRecords(int $companyId, ?string $start = null, ?string $end = null): Collection
    {
        $query = $this->model->byCompany($companyId)->cashIn();

        if ($start && $end) {
            $query->betweenDates($start, $end);
        }

        return $query->orderBy('transaction_date', 'desc')->get();
    }

    /**
     * Get all cash out records for a company.
     *
     * @param int $companyId
     * @param string|null $start
     * @param string|null $end
     * @return Collection
     */
    public function getCashOutRecords(int $companyId, ?string $start = null, ?string $end = null): Collection
    {
        $query = $this->model->byCompany($companyId)->cashOut();

        if ($start && $end) {
            $query->betweenDates($start, $end);
        }

        return $query->orderBy('transaction_date', 'desc')->get();
    }

    /**
     * Get total count of records for a company.
     *
     * @param int $companyId
     * @param string|null $start
     * @param string|null $end
     * @return int
     */
    public function getCount(int $companyId, ?string $start = null, ?string $end = null): int
    {
        $query = $this->model->byCompany($companyId);

        if ($start && $end) {
            $query->betweenDates($start, $end);
        }

        return $query->count();
    }

    /**
     * Get the latest record for a company.
     *
     * @param int $companyId
     * @return CashSummary|null
     */
    public function getLatest(int $companyId): ?CashSummary
    {
        return $this->model->byCompany($companyId)
            ->orderBy('transaction_date', 'desc')
            ->first();
    }

    /**
     * Check if a cash summary exists.
     *
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }
}
