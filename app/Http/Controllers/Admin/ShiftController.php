<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShiftStoreRequest;
use App\Http\Requests\ShiftUpdateRequest;
use App\Models\Company;
use App\Models\Shift;
use App\Services\ShiftService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $shifts = Shift::paginate(5);

            return view('admin.setting.shift.index', ['shifts' => $shifts]);
        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $companies = Company::all();

            return view('admin.setting.shift.create', ['companies' => $companies]);
        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShiftStoreRequest $request, ShiftService $shiftService)
    {
        try {
            $shiftService->store($request->validated());

            return redirect()->route('setting.shift.index')->with('success', 'Shift created successfully.');
        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Shift $shift)
    {
        try {
            //
        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shift $shift)
    {
        try {
            $companies = Company::all();

            return view('admin.setting.shift.edit', ['shift' => $shift, 'companies' => $companies]);
        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShiftUpdateRequest $request, Shift $shift, ShiftService $shiftService)
    {
        try {
            $shiftService->update($shift, $request->validated());

            return redirect()->route('setting.shift.index')->with('success', 'Shift updated successfully.');

        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shift $shift, ShiftService $shiftService)
    {
        try {
            $shiftService->destroy($shift->find(request()->segment(3)));

            return redirect()->route('setting.shift.index')->with('success', 'Shift deleted successfully.');
        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('failed', $e->getMessage());
        }
    }
}
