<?php

namespace App\Services;

use App\Models\Group;
use App\Models\Page;
use Illuminate\Support\Facades\DB;

class RoleService
{
    /**
     * Create a new role group and assign page permissions.
     *
     * @param array $data
     * @return Group
     */
    public function store(array $data): Group
    {
        return DB::transaction(function () use ($data) {
            $group = Group::create([
                'group_name' => $data['group_name'],
            ]);

            $pages = Page::all();
            $syncData = [];

            foreach ($pages as $page) {
                $pageId = $page->getKey();
                $hasAccess = isset($data[$pageId]) && $data[$data[$pageId]] === 'on' ? '1' : '0';
                $syncData[$pageId] = [
                    'access' => $hasAccess,
                ];
            }

            $group->pages()->sync($syncData);

            return $group;
        });
    }

    /**
     * Update an existing role group and its page permissions.
     *
     * @param Group $group
     * @param array $data
     * @return Group
     */
    public function update(Group $group, array $data): Group
    {
        return DB::transaction(function () use ($group, $data) {
            $group->update([
                'group_name' => $data['group_name'],
            ]);

            $pages = Page::all();
            $syncData = [];

            foreach ($pages as $page) {
                $pageId = $page->getKey();
                $hasAccess = (isset($data[$pageId]) && $data[$pageId] === 'on') ? '1' : '0';
                $syncData[$pageId] = [
                    'access' => $hasAccess,
                ];
            }

            $group->pages()->sync($syncData);

            return $group->fresh();
        });
    }

    /**
     * Delete a role group and its pivot permissions.
     *
     * @param Group $group
     * @return bool
     */
    public function destroy(Group $group): bool
    {
        return DB::transaction(function () use ($group) {
            $group->pages()->detach();

            return (bool) $group->delete();
        });
    }

    /**
     * Get distinct modules and page names for role management.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPageDistincts()
    {
        return \App\Models\Page::select('module', 'page_name')->distinct()->get();
    }

    /**
     * Get Access Per Page by Group Id (Rekomendasi Terbaik)
     *
     * @param string $pageName
     * @param int $groupId
     * @return array
     */
    public function getAccessPage(string $pageName, int $groupId): array
    {
        return DB::table('group_pages')
            ->join('pages', 'pages.id', '=', 'group_pages.page_id')
            ->where('group_pages.group_id', $groupId) // Langsung kunci pakai group_id dari user
            ->where('pages.page_name', $pageName)
            ->pluck('group_pages.access', 'pages.action')
            ->toArray();
    }

    /**
     * Get Access Permission dynamically by Page or Module (Safe from Null).
     *
     * @param string $type ('page' atau 'module')
     * @param string $name (Nama halaman atau Nama modul)
     * @param int|null $groupId (Boleh null agar tidak error)
     * @return array
     */
    public function getAccess(string $type, string $name, int $groupId): array
    {
        $query = \Illuminate\Support\Facades\DB::table('group_pages')
            ->join('pages', 'pages.id', '=', 'group_pages.page_id')
            ->where('group_pages.group_id', $groupId);

        if ($type === 'page') {
            return $query->where('pages.page_name', $name)
                ->pluck('group_pages.access', 'pages.action')
                ->toArray();
        }

        if ($type === 'module') {
            return $query->where('pages.module', $name)
                ->where('pages.action', 'Read')
                ->where('group_pages.access', '1')
                ->exists();
        }
    }
}
