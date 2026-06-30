<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public array $access = [];
    protected RoleService $roleService;

    /**
     * Constructor untuk Controller.
     * Hanya inject RoleService saja, $access tidak perlu masuk parameter container.
     */
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Get Access For All Controller
     */
    public function get_access_per_page($pageName)
    {
        if (!auth()->check()) {
            return [];
        }

        $this->access = $this->roleService->getAccessPage($pageName, auth()->user()->group_id);

        return $this->access;
    }

    /**
     * Get sidebar access list for a given user group.
     *
     * This method retrieves all pages that have 'Read' access for a specific group.
     * The result is used to build the sidebar menu.
     *
     * @param int $groupId
     * @return \Illuminate\Support\Collection
     */
    public static function getSidebarAccess(int $groupId): \Illuminate\Support\Collection
    {
        return \Illuminate\Support\Facades\DB::table('group_pages')
            ->join('pages', 'pages.id', '=', 'group_pages.page_id')
            ->where('group_pages.group_id', $groupId)
            ->where('group_pages.access', 1)
            ->where('pages.action', 'Read')
            ->select('pages.module', 'pages.page_name', 'pages.action')
            ->get();
    }

    /**
     * Get modules that have more than one page with 'Read' access, along with a sample page.
     *
     * @param int $groupId
     * @return \Illuminate\Support\Collection
     */
    public static function getHomeAccessModules(int $groupId): \Illuminate\Support\Collection
    {
        $modules = \Illuminate\Support\Facades\DB::table('group_pages')
            ->join('pages', 'pages.id', '=', 'group_pages.page_id')
            ->where('group_pages.group_id', $groupId)
            ->where('pages.action', 'Read')
            ->where('group_pages.access', 1)
            ->select('pages.module', DB::raw('COUNT(DISTINCT pages.page_name) as total'))
            ->groupBy('pages.module')
            ->having('total', '>', 1)
            ->pluck('module');

        if ($modules->isEmpty()) {
            return collect();
        }

        $result = collect();
        foreach ($modules as $module) {
            $page = \Illuminate\Support\Facades\DB::table('group_pages')
                ->join('pages', 'pages.id', '=', 'group_pages.page_id')
                ->where('group_pages.group_id', $groupId)
                ->where('pages.module', $module)
                ->where('pages.action', 'Read')
                ->where('group_pages.access', 1)
                ->select('pages.page_name', 'pages.route')
                ->first();

            if ($page) {
                $result->push((object) [
                    'module' => $module,
                    'page_name' => $page->page_name,
                    'route' => $page->route,
                ]);
            }
        }

        return $result;
    }
}
