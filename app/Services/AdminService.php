<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    public function deactivateAdmin($id)
    {
        $admin = User::findOrFail($id);
        $admin->update(['status' => 'deactivated']);
        return $admin;
    }

    public function getAdminList()
    {
        return User::where('role', 'admin')->paginate(5);
    }

    public function getAdmin($id)
    {
        return User::findOrFail($id);
    }

    public function updateAdmin($id, array $data)
    {
        $admin = User::findOrFail($id);
        $admin->update($data);
        return $admin;
    }

    public function deleteAdmin($id)
    {
        User::findOrFail($id)->delete();
    }

    public function storeAdmin(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'admin';
        $data['status'] = 'active';
        return User::create($data);
    }
}