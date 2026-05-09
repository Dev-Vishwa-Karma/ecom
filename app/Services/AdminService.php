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

        public function getAdminList($request)
        {
            $query = User::where('role', 'admin');

            //  SEARCH (name/email/mobile)
            if ($request->filled('search')) {

                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
                });
            }

            // STATUS FILTER
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            return $query->latest()->paginate(5);
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