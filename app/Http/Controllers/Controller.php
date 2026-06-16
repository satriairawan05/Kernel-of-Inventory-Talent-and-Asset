<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

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
}
