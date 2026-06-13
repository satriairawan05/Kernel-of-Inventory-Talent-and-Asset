<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UnitStoreRequest;
use App\Models\Unit;
use App\Services\UnitService;

class UnitController extends Controller
{
    /**
     * Constructor for Controller.
     */
    public function __construct(private $access = [])
    {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $units = Unit::paginate(25);

            return view('admin.setting.unit.index',[
                'units' => $units
            ]);
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
            return view('admin.setting.unit.create');
        } catch(\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UnitStoreRequest $request, UnitService $unitService)
    {
        try {
        $unitService->store($request->validated());

        return redirect()->route('setting.unit.index')->with('success','Unit created successfully.');
        } catch(\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
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
    public function edit(Unit $unit)
    {
        try {
            return view('admin.setting.unit.edit',[
                'unit' => $unit
            ]);
        } catch(\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UnitUpdateRequest $request, Unit $unit, UnitService $unitService)
    {
        try {
        $unitService->update($unit,$request->validated());
        
        return redirect()->route('setting.unit.index')->with('success','Unit updated successfully.');

        } catch(\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit, UnitService $unitService)
    {
        try {
        $unitService->destroy($unit);

        return redirect()->route('setting.unit.index')->with('success','Unit deleted successfully.');
        
        } catch(\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }
}
