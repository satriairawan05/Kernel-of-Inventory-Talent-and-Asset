<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Services\CompanyService;
use App\Models\Company;

class CompanyController extends Controller
{
    /**
     * Constructor for Controller.
     */
    public function __construct(private $name = 'Company', private $access = [])
    {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $companies = Company::paginate(5);
    
            return view('admin.setting.company.index', ['companies' => $companies, 'name' => $this->name]);
        } catch(\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('admin.setting.company.create');
        } catch(\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRequest $request, CompanyService $companyService)
    {
        try {
            $companyService->store(
                $request->validated()
            );

            return redirect()
                ->route('setting.company.index')
                ->with(
                    'success',
                    'Company created successfully.'
                );
        } catch(\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        try {
            //
        } catch(\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        try {
            return view('admin.setting.company.edit', ['company' => $company]);
        } catch(\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRequest $request, Company $company, CompanyService $companyService)
    {
        try {
            $companyService->update($company, $request->validated());

            return redirect()->route('setting.company.index')->with('success','Company updated successfully.');
        } catch(\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company, CompanyService $companyService)
    {
        try {
            $companyService->destroy($company);
            return redirect()->route('setting.company.index')->with('success','Company deleted successfully.');
        } catch(\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }
}
