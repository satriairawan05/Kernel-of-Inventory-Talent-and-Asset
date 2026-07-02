<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CashierService;
use App\Services\SystemSettingService;
use Illuminate\Http\Request;

class PointOfSalesController extends Controller
{
    /*
    * Global Variable for Access Page
    */
    public $accessPage = [];

    /*
    * Get Access for Controller
    */
    public function get_access()
    {
        $this->accessPage = $this->get_access_per_page('POS');

        $data = [
            "Create" => (int) $this->accessPage['Create'],
            "Read"   => (int) $this->accessPage['Read'],
            "Update" => (int) $this->accessPage['Update'],
            "Delete" => (int) $this->accessPage['Delete'],
        ];

        return $data;
    }

    /**
     * Display POS main page.
     * Only accessible if cashier session is open.
     */
    public function posView()
    {
        $access = $this->get_access();

        if (!isset($access['Read']) || $access['Read'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        }

        // Cek apakah ada sesi kasir terbuka
        $openSession = \App\Models\CashierSession::open()->first();
        if (!$openSession) {
            return redirect()->route('pos.open')->with('warning', 'Please open cashier first.');
        }

        return view('admin.pos.point-of-sales.home');
    }

    /**
     * Display cashier opening form.
     */
    public function openCashierView(CashierService $cashierService)
    {
        $access = $this->get_access();

        if (!isset($access['Read']) || $access['Read'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        }

        // If already open, redirect to POS
        if (\App\Models\CashierSession::open()->exists()) {
            return redirect()->route('pos.point-of-sales');
        }

        try {
            $user = auth()->user();
            $companyId = $user->company_id ?? 1;
            
            // Get opening balance from CashierService
            $openingBalance = $cashierService->getOpeningBalanceForCompany($companyId);
            
            return view('admin.pos.open', [
                'openingBalance' => $openingBalance,
                'companyId'      => $companyId,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Open cashier view error: ' . $e->getMessage());
            return redirect()->back()->with('failed', 'Failed to load opening balance: ' . $e->getMessage());
        }
    }

    /**
     * Process cashier opening.
     */
    public function storeCashierOpen(Request $request, CashierService $cashierService)
    {
        $access = $this->get_access();

        if (!isset($access['Create']) || $access['Create'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        }

        try {
            $session = $cashierService->openCashier();
            
            return redirect()->route('pos.point-of-sales')
                ->with('success', 'Cashier opened successfully. Opening balance: Rp ' . number_format($session->opening_balance, 0, ',', '.'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Open cashier error: ' . $e->getMessage());
            return redirect()->back()->with('failed', 'Failed to open cashier: ' . $e->getMessage());
        }
    }
}