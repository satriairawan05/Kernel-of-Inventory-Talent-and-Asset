<?php

namespace App\Http\Controllers;

use App\Services\ModuleService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin.home');
    }

    /**
     * Display the profile of the currently authenticated user.
     *
     * This method retrieves the authenticated user's data from the `users` table
     * using the logged-in user's ID. The retrieved data is then passed to the view
     * for display. If no user is authenticated, it will automatically redirect
     * to the login page due to the auth middleware.
     *
     * @return \Illuminate\View\View
     */
    public function profile(ModuleService $moduleService)
    { 
        return view('admin.setting.account.profile',['profile' => $moduleService->getProfile()]);
    }
}
