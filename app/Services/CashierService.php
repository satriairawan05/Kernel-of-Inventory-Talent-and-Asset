<?php

namespace App\Services;

use App\Enums\CashierSessionStatusEnum;
use App\Enums\CashSummaryTypeEnum;
use App\Models\CashierSession;
use App\Models\CashSummary;
use App\Models\SystemSetting;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CashierService
{
    /**
     * Open a new cashier session for the currently authenticated user.
     *
     * @return CashierSession
     * @throws Exception
     */
    public function openCashier(): CashierSession
    {
        $user = Auth::user();

        // Ensure the user has a company assigned
        if (!$user || !$user->company_id) {
            throw new Exception('User does not have a company.');
        }

        // Check if there is already an open session
        $activeSession = CashierSession::open()->first();
        if ($activeSession) {
            throw new Exception('There is already an open cashier session. Please close it first.');
        }

        // Retrieve opening balance from system settings based on company_id
        $openingBalance = $this->getOpeningBalanceForCompany($user->company_id);

        // Create new session
        $session = CashierSession::create([
            'user_id'         => $user->id,
            'opening_balance' => $openingBalance,
            'opened_at'       => now(),
            'status'          => CashierSessionStatusEnum::OPEN,
        ]);

        // Store session_id in session (or cache) for subsequent transactions
        Session::put('cashier_session_id', $session->id);

        Log::info('Cashier session opened', [
            'session_id'      => $session->id,
            'user_id'         => $user->id,
            'company_id'      => $user->company_id,
            'opening_balance' => $openingBalance,
        ]);

        return $session;
    }

    /**
     * Close the currently open cashier session.
     *
     * @param float $actualBalance The actual physical cash count
     * @return CashierSession
     * @throws Exception
     */
    public function closeCashier(float $actualBalance): CashierSession
    {
        $session = CashierSession::open()->first();
        if (!$session) {
            throw new Exception('No open cashier session found.');
        }

        // Get all completed transactions for this session
        $transactions = $session->transactions()
            ->where('status', 'completed')
            ->get();

        $totalSales = $transactions->sum('total');

        // Begin database transaction
        DB::transaction(function () use ($session, $transactions, $totalSales, $actualBalance) {
            // 1. Create cash summary records for each transaction (cash_in)
            if ($transactions->isNotEmpty()) {
                foreach ($transactions as $transaction) {
                    CashSummary::create([
                        'company_id'       => $session->user->company_id,
                        'session_id'       => $session->id,
                        'type'             => CashSummaryTypeEnum::CASH_IN->value,
                        'amount'           => $transaction->total,
                        'description'      => 'Transaction #' . $transaction->transaction_number . ' - Shift ' . $session->id,
                        'transaction_date' => $transaction->transaction_date ? $transaction->transaction_date->toDateString() : now()->toDateString(),
                    ]);
                }
            }

            // 2. Calculate total cash out from CashSummary records for this company and today
            //    (e.g., refunds, petty cash expenses)
            $today = now()->toDateString();
            $totalCashOut = CashSummary::where('company_id', $session->user->company_id)
                ->where('type', CashSummaryTypeEnum::CASH_OUT->value)
                ->whereDate('transaction_date', $today)
                ->sum('amount');

            // 3. Update session
            $session->update([
                'closing_balance' => $actualBalance,
                'total_sales'     => $totalSales,
                'total_cash_in'   => $totalSales,
                'total_cash_out'  => $totalCashOut,
                'closed_at'       => now(),
                'status'          => CashierSessionStatusEnum::CLOSED,
            ]);

            // 4. Clear session_id from Laravel session
            Session::forget('cashier_session_id');
        });

        Log::info('Cashier session closed', [
            'session_id'      => $session->id,
            'user_id'         => $session->user_id,
            'total_sales'     => $totalSales,
            'total_cash_in'   => $totalSales,
            'total_cash_out'  => $totalCashOut ?? 0,
            'closing_balance' => $actualBalance,
            'transactions_count' => $transactions->count(),
        ]);

        return $session;
    }

    /**
     * Get the summary of transactions during the current shift (open session).
     *
     * @return array
     * @throws Exception
     */
    public function getShiftTransactionsSummary(): array
    {
        $session = CashierSession::open()->first();
        if (!$session) {
            throw new Exception('No open cashier session found.');
        }

        $transactions = $session->transactions()
            ->where('status', 'completed')
            ->get();

        $totalTransactions = $transactions->count();
        $totalSales = $transactions->sum('total');

        // Optional: group by payment method
        $paymentSummary = $transactions->groupBy('payment_method')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total'),
            ];
        });

        return [
            'session_id'          => $session->id,
            'opening_balance'     => $session->opening_balance,
            'total_transactions'  => $totalTransactions,
            'total_sales'         => $totalSales,
            'payment_summary'     => $paymentSummary,
            'transactions'        => $transactions,
        ];
    }

    /**
     * Get the total sales for a given session ID (can be used for reporting).
     *
     * @param int $sessionId
     * @return array
     */
    public function getSessionSalesSummary(int $sessionId): array
    {
        $session = CashierSession::with('transactions')->findOrFail($sessionId);
        $transactions = $session->transactions->where('status', 'completed');

        return [
            'session_id'         => $session->id,
            'opening_balance'    => $session->opening_balance,
            'closing_balance'    => $session->closing_balance,
            'total_transactions' => $transactions->count(),
            'total_sales'        => $transactions->sum('total'),
            'total_cash_in'      => $session->total_cash_in,
            'total_cash_out'     => $session->total_cash_out,
        ];
    }

    /**
     * Get the opening balance from system_settings based on company_id.
     *
     * @param int $companyId
     * @return float
     * @throws Exception
     */
    public function getOpeningBalanceForCompany(int $companyId): float
    {
        // Cari setting berdasarkan company_id dan key 'opening_balance'
        $setting = SystemSetting::where('company_id', $companyId)
            ->where('key', 'opening_balance')
            ->first();

        if (!$setting) {
            Log::error('Opening balance not found', [
                'company_id' => $companyId,
                'key' => 'opening_balance'
            ]);
            throw new Exception("Opening balance for company ID {$companyId} is not configured.");
        }

        $amount = (float) $setting->value;

        if ($amount <= 0) {
            Log::error('Opening balance is zero or negative', [
                'company_id' => $companyId,
                'value' => $setting->value
            ]);
            throw new Exception('Opening balance must be greater than 0.');
        }

        Log::info('Opening balance retrieved', [
            'company_id' => $companyId,
            'amount' => $amount
        ]);

        return $amount;
    }
}
