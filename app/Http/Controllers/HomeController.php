<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountCompanyRequest;
use App\Http\Requests\AccountRoleRequest;
use App\Http\Requests\ProfileUpdatePasswordRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\ReportPeriod;
use App\Services\AccountService;
use App\Services\InventoryPreviewService;
use App\Services\InventoryPrintService;
use App\Services\InventoryReportService;
use App\Services\ModuleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin.home');
    }

    /*
    * Global Variable for Access Page
    */
    public $accessPage = [];

    /*
    * Get Access for Controller
    */
    public function get_access()
    {
        $this->accessPage = $this->get_access_per_page('Profile');

        $data = [
            "Read" => (int) $this->accessPage['Read'],
            "Update" => (int) $this->accessPage['Update'],
        ];

        return $data;
    }

    /**
     * Display the profile of the currently authenticated user.
     *
     * This method retrieves the authenticated user's data from the `users` table
     * using the logged-in user's ID. The retrieved data is then passed to the view
     * for display. If no user is authenticated, it will automatically redirect
     * to the login page due to the auth middleware.
     *
     * @return \Illuminate\View\View
     */
    public function profile(ModuleService $moduleService): View
    {
        $access = $this->get_access();

        if (!isset($access['Read']) || $access['Read'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            $groups = \App\Models\Group::when(auth()->user()->group_id != 1, function ($query) {
                return $query->where('id', '!=', 1);
            })->latest()->get();

            return view('admin.setting.account.profile', ['profile' => $moduleService->getProfile(), 'companies' => \App\Models\Company::latest()->get(), 'groups' => $groups, 'access' => $moduleService->getAccessByModule('Personal', auth()->user()->group_id)]);
        }
    }

    /**
     * Update profile (name, email, avatar)
     */
    public function updateProfile(ProfileUpdateRequest $request, AccountService $accountService): RedirectResponse
    {
        $access = $this->get_access();

        if (!isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            $user = Auth::user();
            $avatarFile = $request->file('avatar');

            $accountService->updateProfile($user, $request->validated(), $avatarFile);

            return redirect()->back()->with('profile_success', 'Profil berhasil diperbarui.');
        }
    }

    /**
     * Update password
     */
    public function updatePassword(ProfileUpdatePasswordRequest $request, AccountService $accountService): RedirectResponse
    {
        $access = $this->get_access();

        if (!isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            $user = Auth::user();
            $accountService->updatePassword($user, $request->password);

            return redirect()->back()->with('password_success', 'Password berhasil diubah.');
        }
    }

    /**
     * Update role
     */
    public function updateGroup(AccountRoleRequest $request, AccountService $accountService)
    {
        $access = $this->get_access();

        if (!isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            $user = Auth::user();
            $accountService->updateGroup($user, $request->group_id);

            return redirect()->back()->with('group_success', 'Role berhasil diperbarui.');
        }
    }

    /**
     * Update outlet
     */
    public function updateCompany(AccountCompanyRequest $request, AccountService $accountService)
    {
        $access = $this->get_access();

        if (!isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            $user = Auth::user();
            $accountService->updateCompany($user, $request->company_id);

            return redirect()->back()->with('company_success', 'Perusahaan berhasil diperbarui.');
        }
    }

    /**
     * Display the dashboard view.
     *
     * This method retrieves all necessary data for the dashboard,
     * such as statistics, summaries, or recent records, and passes
     * them to the admin dashboard view for display.
     *
     * @return \Illuminate\View\View
     */
    public function getDashboard(\App\Services\ModuleService $moduleService): View
    {
        return view('admin.dashboard.home', [
            'access' => $moduleService->getAccessByModule('Dashboard', auth()->user()->id)
        ]);
    }

    /*
    * Get Access for Controller
    */
    public function get_report_access()
    {
        $this->accessPage = $this->get_access_per_page('Report');

        $data = [
            "Create" => (int) $this->accessPage['Create'],
            "Read" => (int) $this->accessPage['Read'],
            "Update" => (int) $this->accessPage['Update'],
            "Delete" => (int) $this->accessPage['Delete'],
        ];

        return $data;
    }

    /**
     * Halaman utama laporan (dengan filter daily/weekly/monthly)
     */
    public function indexReport(Request $request, InventoryReportService $inventoryReportService): View
    {
        $access = $this->get_report_access();

        if (!isset($access['Read']) || $access['Read'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            $type = $request->input('type', 'daily');
            $date = $request->input('date', now()->toDateString());

            $reports = collect();
            $data = [];

            if ($type === 'daily') {
                $reports = $inventoryReportService->getReports('daily', $date);
            } else {
                $data = $inventoryReportService->getAggregatedReport($type, $date);
            }

            return view('admin.inventory.report.index', [
                'type'    => $type,
                'date'    => $date,
                'reports' => $reports,
                'data'    => $data,
            ]);
        }
    }

    /**
     * Form generate laporan harian
     */
    public function generateForm(): View
    {
        $periods = ReportPeriod::with('shift')->where('is_active', true)->get();
        return view('admin.inventory.report.generate', ['periods' => $periods]);
    }

    /**
     * Proses generate laporan harian
     */
    public function generate(Request $request, InventoryReportService $inventoryReportService): RedirectResponse
    {
        $access = $this->get_report_access();

        if (!isset($access['Create']) || $access['Create'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            $request->validate([
                'date'         => 'required|date',
                'period_id'    => 'required|exists:report_periods,id',
                'location'     => 'required|string|max:255',
                'reported_by'  => 'required|string|max:255',
                'cashier_name' => 'required|string|max:255',
                'opened_at'    => 'nullable|date',
                'closed_at'    => 'nullable|date|after:opened_at',
            ]);
    
            try {
                $report = $inventoryReportService->generateDailyReport(
                    $request->date,
                    $request->period_id,
                    $request->location,
                    $request->reported_by,
                    $request->cashier_name,
                    $request->opened_at,
                    $request->closed_at
                );
    
                return redirect()
                    ->route('inventory.report.index', ['type' => 'daily', 'date' => $request->date])
                    ->with('success', "Laporan harian {$request->date} berhasil dibuat.");
            } catch (\Exception $e) {
                return back()
                    ->withInput()
                    ->with('failed', 'Gagal membuat laporan: ' . $e->getMessage());
            }
        }

    }

    /**
     * Preview laporan harian (struk thermal)
     */
    public function preview(int $id, InventoryPreviewService $inventoryPreviewService): View
    {
        $access = $this->get_report_access();

        if (!isset($access['Read']) || $access['Read'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                return $inventoryPreviewService->previewDailyReport($id);
            } catch (\Exception $e) {
                abort(404, $e->getMessage());
            }
        }
    }

    /**
     * Cetak fisik laporan harian
     */
    public function printReport(Request $request, int $id, InventoryPrintService $inventoryPrintService): RedirectResponse
    {
        $access = $this->get_report_access();

        if (!isset($access['Read']) || $access['Read'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            $request->validate([
                'connection_type' => 'required|in:windows,network,file',
                'target'          => 'required|string',
            ]);
    
            try {
                $inventoryPrintService->connect($request->connection_type, $request->target);
                $result = $inventoryPrintService->printDailyReport($id);
    
                if ($result['success']) {
                    return back()->with('success', $result['message']);
                }
                return back()->with('failed', $result['message']);
            } catch (\Exception $e) {
                return back()->with('failed', 'Gagal cetak: ' . $e->getMessage());
            }
        }
    }

    /**
     * Cetak fisik laporan agregat (weekly/monthly)
     */
    public function printAggregated(Request $request, InventoryReportService $inventoryReportService, InventoryPrintService $inventoryPrintService): RedirectResponse
    {
        $access = $this->get_report_access();

        if (!isset($access['Read']) || $access['Read'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            $request->validate([
                'type'            => 'required|in:weekly,monthly',
                'date'            => 'nullable|date',
                'connection_type' => 'required|in:windows,network,file',
                'target'          => 'required|string',
            ]);
    
            try {
                $data = $inventoryReportService->getAggregatedReport($request->type, $request->date);
                if (empty($data)) {
                    return back()->with('failed', 'Tidak ada data untuk dicetak.');
                }
    
                $inventoryPrintService->connect($request->connection_type, $request->target);
                $result = $inventoryPrintService->printAggregatedReport($data, $request->type, $request->date);
    
                if ($result['success']) {
                    return back()->with('success', $result['message']);
                }
                return back()->with('failed', $result['message']);
            } catch (\Exception $e) {
                return back()->with('failed', 'Gagal cetak agregat: ' . $e->getMessage());
            }
        }
    }

    /**
     * Hapus laporan harian
     */
    public function destroyReport(int $id, InventoryReportService $inventoryReportService): RedirectResponse
    {
        $access = $this->get_report_access();

        if (!isset($access['Delete']) || $access['Delete'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $inventoryReportService->deleteReport($id);
                return back()->with('success', 'Laporan berhasil dihapus.');
            } catch (\Exception $e) {
                return back()->with('failed', 'Gagal hapus laporan: ' . $e->getMessage());
            }
        }
    }
}
