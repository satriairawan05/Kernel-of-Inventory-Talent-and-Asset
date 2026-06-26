<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuItemStoreRequest;
use App\Http\Requests\MenuItemUpdateRequest;
use App\Models\MenuItem;
use App\Services\MenuItemService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
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
        $this->accessPage = $this->get_access_per_page('Menu');

        $data = [
            'Create' => (int) $this->accessPage['Create'],
            'Read' => (int) $this->accessPage['Read'],
            'Update' => (int) $this->accessPage['Update'],
            'Delete' => (int) $this->accessPage['Delete'],
        ];

        return $data;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companyId = auth()->user()->company_id;
        $menuItems = MenuItem::forCompany($companyId)
            ->orderBy('name')
            ->paginate(25);

        // Category & status options (untuk dropdown di modal)
        $access = $this->get_access();
        $categories = [
            'food' => 'Food',
            'drink' => 'Drink',
            'snack' => 'Snack',
            'additional' => 'Additional',
        ];
        $statuses = [
            'available' => 'Available',
            'low' => 'Low Stock',
            'out' => 'Out of Stock',
        ];

        return view('admin.pos.menu.index', compact(
            'menuItems',
            'access',
            'categories',
            'statuses'
        ));
        try {

        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('failed', $e->getMessage());
        }
        $access = $this->get_access();

        if (! isset($access['Read']) || $access['Read'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MenuItemStoreRequest $request, MenuItemService $service)
    {
        $access = $this->get_access();

        if (! isset($access['Create']) || $access['Create'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        }

        try {
            $service->store($request->validated());

            return redirect()->route('pos.menu.index')
                ->with('success', 'Menu item created successfully.');
        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MenuItem $menuItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MenuItem $menuItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MenuItemUpdateRequest $request, MenuItem $menuItem, MenuItemService $service)
    {
        $access = $this->get_access();

        if (! isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        }

        // Verify ownership
        if ($menuItem->company_id !== auth()->user()->company_id) {
            return redirect()->back()->with('failed', "You don't have authority for this menu.");
        }

        try {
            $service->update($menuItem, $request->validated());

            return redirect()->route('pos.menu.index')
                ->with('success', 'Menu item updated successfully.');
        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MenuItem $menuItem, MenuItemService $service)
    {
        $access = $this->get_access();

        if (! isset($access['Delete']) || $access['Delete'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        }

        if ($menuItem->company_id !== auth()->user()->company_id) {
            return redirect()->back()->with('failed', "You don't have authority for this menu.");
        }

        try {
            $service->destroy($menuItem);

            return redirect()->route('pos.menu.index')
                ->with('success', 'Menu item deleted successfully.');
        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('failed', $e->getMessage());
        }
    }
}
