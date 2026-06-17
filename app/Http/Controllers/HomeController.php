<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountCompanyRequest;
use App\Http\Requests\AccountRoleRequest;
use App\Http\Requests\ProfileUpdatePasswordRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Services\AccountService;
use App\Services\ModuleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /*
    * Global Variable for Access Page
    */
    public $accessPage = [];

    /*
    * Get Access for Controller
    */
    public function get_access()
    {
        $this->accessPage = $this->get_access_per_page('Profile');

        $data = [
            "Read" => (int) $this->accessPage['Read'],
            "Update" => (int) $this->accessPage['Update'],
        ];

        return $data;
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
        $access = $this->get_access();

        if (!isset($access['Read']) || $access['Read'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            return view('admin.setting.account.profile', ['profile' => $moduleService->getProfile(),'companies' => \App\Models\Company::latest()->get(),'groups' => \App\Models\Group::latest()->get(),'access' => $this->get_access_per_page('Profile')]);
        }
    }

    /**
     * Update profile (name, email, avatar)
     */
    public function updateProfile(ProfileUpdateRequest $request, AccountService $accountService): RedirectResponse
    {
        $access = $this->get_access();

        if (!isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            $user = Auth::user();
            $avatarFile = $request->file('avatar');
    
            $accountService->updateProfile($user, $request->validated(), $avatarFile);
    
            return redirect()->back()->with('profile_success', 'Profil berhasil diperbarui.');
        }
    }

    /**
     * Update password
     */
    public function updatePassword(ProfileUpdatePasswordRequest $request, AccountService $accountService): RedirectResponse
    {
        $access = $this->get_access();

        if (!isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            $user = Auth::user();
            $accountService->updatePassword($user, $request->password);
            
            return redirect()->back()->with('password_success', 'Password berhasil diubah.');
        }
    }

    /**
     * Update role
     */
    public function updateGroup(AccountRoleRequest $request, AccountService $accountService)
    {
        $access = $this->get_access();

        if (!isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            $user = Auth::user();
            $accountService->updateGroup($user, $request->group_id);
    
            return redirect()->back()->with('group_success', 'Role berhasil diperbarui.');
        }

    }

    /**
     * Update outlet
     */
    public function updateCompany(AccountCompanyRequest $request, AccountService $accountService)
    {
        $access = $this->get_access();

        if (!isset($access['Update']) || $access['Update'] != 1) {
            return redirect()->back()->with('failed', "You don't have authority");
        } else {
            $user = Auth::user();
            $accountService->updateCompany($user, $request->company_id);
    
            return redirect()->back()->with('company_success', 'Perusahaan berhasil diperbarui.');
        }
    }
}
