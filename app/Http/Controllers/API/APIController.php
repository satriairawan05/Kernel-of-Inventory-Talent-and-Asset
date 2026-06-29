<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class APIController extends Controller
{
    /**
     * API Response helper.
     */
    private function response($data = null, $message = 'Success', $status = 200)
    {
        return response()->json([
            'success' => $status >= 200 && $status < 300,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * Get all companies/outlets.
     * GET /api/companies
     */
    public function getCompanies(Request $request)
    {
        try {
            $companies = Company::select('id', 'company_name as name')
                ->where('use_menu', true)
                ->orderBy('company_name')
                ->get();

            return $this->response($companies);
        } catch (\Exception $e) {
            Log::error('API getCompanies error: ' . $e->getMessage());
            return $this->response(null, 'Failed to fetch companies', 500);
        }
    }

    /**
     * Get shifts by company_id.
     * GET /api/shifts?company_id={id}
     */
    public function getShifts(Request $request)
    {
        try {
            $user = auth()->user();
            $companyId = $request->has('company_id') ? $request->company_id : null;

            // Jika user bukan admin dan tidak ada company_id di request, batasi ke company user
            if ($user && $user->group_id != 1 && !$companyId) {
                $companyId = $user->company_id;
            }

            $query = Shift::select(
                'id',
                'company_id',
                'shift_name as name',
                'start_time as start',
                'end_time as end',
                'shift_code as code',
                'late_tolerance_minutes',
                'early_leave_tolerance_minutes'
            );

            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            // Jika admin dan tidak ada company_id, ambil semua shift
            $shifts = $query->orderBy('start_time')->get();

            return $this->response($shifts);
        } catch (\Exception $e) {
            Log::error('API getShifts error: ' . $e->getMessage());
            return $this->response(null, 'Failed to fetch shifts', 500);
        }
    }

    /**
 * Get menu items for POS.
 * GET /api/menu?company_id={id}&category={cat}
 */
public function getMenu(Request $request)
{
    try {
        $user = auth()->user();
        $companyId = $request->has('company_id') ? $request->company_id : ($user->company_id ?? 1);

        $query = MenuItem::with(['productVariant.stock'])
            ->when($companyId, function ($q) use ($companyId) {
                return $q->where('company_id', $companyId);
            })
            ->where('status', '!=', 'out')
            ->orderBy('name');

        if ($request->has('category') && $request->category != 'all') {
            $query->where('category', $request->category);
        }

        $menu = $query->get();

        // Format data untuk frontend
        $formatted = $menu->map(function ($item) {
            return [
                'id'       => $item->id,
                'name'     => $item->name,
                'price'    => $item->price,
                'category' => $item->category,
                'status'   => $item->status,
                'image'    => $item->image_url, // dari accessor di model
                'stock'    => $item->stock ?? 0,
                'icon'     => $this->getInitials($item->name), // inisial
                'variant'  => $item->productVariant ? [
                    'id'    => $item->productVariant->id,
                    'name'  => $item->productVariant->variant_name,
                    'stock' => $item->productVariant->stock?->current_stock ?? 0,
                ] : null,
            ];
        });

        return $this->response($formatted);
    } catch (\Exception $e) {
        Log::error('API getMenu error: ' . $e->getMessage());
        return $this->response(null, 'Failed to fetch menu', 500);
    }
}

/**
 * Get initials from name (e.g., Ayam Geprek Keju -> AK, Ayam Geprek -> AG)
 */
private function getInitials($name)
{
    $words = explode(' ', $name);
    $initials = '';
    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
    }
    // Jika hanya 1 huruf, ambil 2 huruf pertama
    if (strlen($initials) < 2) {
        $initials = strtoupper(substr($name, 0, 2));
    }
    return $initials;
}
}
