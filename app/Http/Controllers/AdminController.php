<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Services\AdminService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function deactivateAdmin($id)
    {
        $this->adminService->deactivateAdmin($id);
        return back()->with('success', 'Admin deactivated successfully.');
    }

    public function adminList()
    {
        $admins = $this->adminService->getAdminList();
        return view('super.admin_list', compact('admins'));
    }

    public function viewAdmin($id)
    {
        $admin = $this->adminService->getAdmin($id);
        return view('super.view_admin', compact('admin'));
    }

    public function editAdmin($id)
    {
        $admin = $this->adminService->getAdmin($id);
        return view('super.edit_admin', compact('admin'));
    }

    public function updateAdmin(UpdateAdminRequest $request, $id)
    {
        $this->adminService->updateAdmin($id, $request->validated());
        return redirect()->route('super.admin.list');
    }

    public function deleteAdmin($id)
    {
        $this->adminService->deleteAdmin($id);
        return back();
    }

    public function showAdminCreate()
    {
        return view('super.create_admin');
    }

    public function storeAdmin(StoreAdminRequest $request)
    {
        $this->adminService->storeAdmin($request->validated());
        return redirect()->route('super.dashboard')
            ->with('success','Admin Created Successfully');
    }
}