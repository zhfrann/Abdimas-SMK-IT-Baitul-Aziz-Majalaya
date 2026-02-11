<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        // filter
        $role = $request->get('role', 'all');        // all / nama role
        $status = $request->get('status', 'all');    // all / active / inactive

        // list role untuk dropdown (semua role)
        $roles = Role::query()->orderBy('name')->get(['id', 'name']);

        $users = User::query()
            ->with('roles')
            ->when($role !== 'all', function ($query) use ($role) {
                $query->whereHas('roles', fn($r) => $r->where('name', $role));
            })
            ->when($status === 'active', fn($query) => $query->where('is_active', 1))
            ->when($status === 'inactive', fn($query) => $query->where('is_active', 0))
            ->orderBy('name')
            ->get();

        return view('akademik.account.index', [
            'users' => $users,
            'roles' => $roles,
            'role' => $role,
            'status' => $status,
            'currentUserId' => Auth::id(),
        ]);
    }

    public function toggleActive(User $user)
    {
        // Tidak boleh nonaktifkan diri sendiri
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak bisa menonaktifkan akun sendiri.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', 'Status akun berhasil diperbarui.');
    }
}
