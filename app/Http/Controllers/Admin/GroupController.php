<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GroupController extends Controller
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
            return view('admin.setting.role.index', [
                'roles' => Group::paginate(15),
                'access' => $this->get_access_per_page('Roles')
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('failed', 'Gagal memuat data role.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(RoleService $roleService)
    {
        try {
            return view('admin.setting.role.create', [
                'page_distincts' => $roleService->getPageDistincts(),
                'pages'          => \App\Models\Page::all(),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('failed', 'Gagal memuat form tambah role.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, RoleService $roleService)
    {
        $request->validate([
            'group_name' => 'required|string|max:255',
        ]);

        try {
            $roleService->store($request->all());

            return redirect()->route('setting.role.index')->with('success', 'Role berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('failed', 'Gagal menyimpan data role.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        try {
            //
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group, RoleService $roleService)
    {
        try {
            return view('admin.setting.role.edit', [
                'page_distincts' => $roleService->getPageDistincts(),
                'pages'          => \App\Models\GroupPage::leftJoin('pages', 'pages.id', '=', 'group_pages.page_id')
                                        ->where('group_id', '=', $group->id)
                                        ->get(),
                'group'          => $group,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('failed', 'Gagal memuat data edit role.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group, RoleService $roleService)
    {
        // Validasi input sebelum proses update
        $request->validate([
            'group_name' => 'required|string|max:255',
        ]);

        try {
            $roleService->update($group, $request->all());

            return redirect()->route('setting.role.index')->with('success', 'Role berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('failed', 'Gagal memperbarui data role.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group, RoleService $roleService)
    {
        try {
            $roleService->destroy($group);

            return redirect()->route('setting.role.index')->with('success', 'Role berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('failed', 'Gagal menghapus data role.');
        }
    }
}