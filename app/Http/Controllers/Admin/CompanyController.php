<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Services\CompanyService;
use App\Models\Company;

class CompanyController extends Controller
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
        $this->accessPage = $this->get_access_per_page('Outlet');

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
        } else {
            try {
                $companies = Company::paginate(5);
                return view('admin.setting.company.index', ['companies' => $companies, 'name' => 'Company','access' => $this->get_access_per_page('Outlet')]);
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
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
        } else {
            try {
                return view('admin.setting.company.create');
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRequest $request, CompanyService $companyService)
    {
        $access = $this->get_access();

        if (!isset($access['Create']) || $access['Create'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $validated = $request->validated();
    
                // otomatis nilainya menjadi false dan tetap tersimpan di database.
                $validated['use_menu'] = $request->boolean('use_menu');
                $validated['use_service'] = $request->boolean('use_service');
                $validated['use_inventory'] = $request->boolean('use_inventory');
    
                $companyService->store($validated);
    
                return redirect()
                    ->route('setting.company.index')
                    ->with('success', 'Company created successfully.');
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
    
                // Tangani error unik constraint
                if ($e->errorInfo[1] == 1062) { // Duplicate entry
                    return redirect()->back()
                        ->withInput()
                        ->with('failed', 'Company email or name already exists.');
                }
    
                return redirect()->back()
                    ->withInput()
                    ->with('failed', 'Database error: ' . $e->getMessage());
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('General error: ' . $e->getMessage());
    
                return redirect()->back()
                    ->withInput()
                    ->with('failed', 'An error occurred: ' . $e->getMessage());
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        $access = $this->get_access();

        if (!isset($access['Read']) || $access['Read'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                //
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        $access = $this->get_access();

        if (!isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                return view('admin.setting.company.edit', ['company' => $company]);
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRequest $request, Company $company, CompanyService $companyService)
    {
        $access = $this->get_access();

        if (!isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $validated = $request->validated();
    
                // PERBAIKAN: Tambahkan proses konversi boolean yang sebelumnya tidak ada di method update
                $validated['use_menu'] = $request->boolean('use_menu');
                $validated['use_service'] = $request->boolean('use_service');
                $validated['use_inventory'] = $request->boolean('use_inventory');
    
                $companyService->update($company, $validated);
    
                return redirect()->route('setting.company.index')->with('success', 'Company updated successfully.');
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                // PERBAIKAN: Tambahkan ->withInput() agar ketikan user tidak hilang saat error
                return redirect()->back()->withInput()->with('failed', 'Database error: ' . $e->getMessage());
            } catch (\Exception $e) {
                // PERBAIKAN: Tambahkan catch Exception agar tidak error 500 jika gagal di service
                \Illuminate\Support\Facades\Log::error('General error: ' . $e->getMessage());
                return redirect()->back()->withInput()->with('failed', 'An error occurred: ' . $e->getMessage());
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company, CompanyService $companyService)
    {
        $access = $this->get_access();

        if (!isset($access['Delete']) || $access['Delete'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            try {
                $companyService->destroy($company);
                return redirect()->route('setting.company.index')->with('success', 'Company deleted successfully.');
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
                return redirect()->back()->with('failed', $e->getMessage());
            }
        }
    }
}
