<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SystemSettingStoreRequest;
use App\Http\Requests\SystemSettingUpdateRequest;
use App\Models\Company;
use App\Models\SystemSetting;
use App\Services\SystemSettingService;

class SystemSettingController extends Controller
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
        $this->accessPage = $this->get_access_per_page('System Setting');

        $data = [
            "Create" => (int) $this->accessPage['Create'],
            "Read" => (int) $this->accessPage['Read'],
            "Update" => (int) $this->accessPage['Update'],
            "Delete" => (int) $this->accessPage['Delete'],
        ];

        return $data;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $access = $this->get_access();

        if (!isset($access['Read']) || $access['Read'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        }

        try {
            $settings = SystemSetting::with('company')
                ->paginate(25);
            $companies = Company::all();

            return view('admin.setting.setting.index', [
                'settings' => $settings,
                'access'   => $access,
                'companies' => $companies
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $access = $this->get_access();

        if (!isset($access['Create']) || $access['Create'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        }

        try {
            $companies = Company::pluck('company_name', 'id');
            return view('admin.setting.setting.create', compact('companies'));
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SystemSettingStoreRequest $request, SystemSettingService $systemSettingService)
    {
        $access = $this->get_access();

        if (!isset($access['Create']) || $access['Create'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        }

        try {
            $systemSettingService->store($request->validated());
            return redirect()->route('setting.system_setting.index')->with('success', 'Setting created successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Setting $setting)
    {
        $access = $this->get_access();

        if (!isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        }

        try {
            $companies = Company::pluck('company_name', 'id');
            return view('admin.setting.setting.edit', compact('setting', 'companies'));
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SystemSettingUpdateRequest $request, SystemSetting $systemSetting, SystemSettingService $systemSettingService)
    {
        $access = $this->get_access();

        if (!isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        }

        try {
            $systemSettingService->update($systemSetting, $request->validated());
            return redirect()->route('setting.system_setting.index')->with('success', 'Setting updated successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SystemSetting $systemSetting, SystemSettingService $systemSettingService)
    {
        $access = $this->get_access();

        if (!isset($access['Delete']) || $access['Delete'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        }

        try {
            $systemSettingService->destroy($systemSetting);
            return redirect()->route('setting.system_setting.index')->with('success', 'Setting deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }
}
