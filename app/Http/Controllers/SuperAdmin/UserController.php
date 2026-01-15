<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        return view('super_admin.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::whereIn('name', [
            'Kepala Sekolah',
            'Guru Mapel',
            'Wali Kelas',
            'Bagian Akademik'
        ])->get();

        $provinsi = Http::get('https://wilayah.id/api/provinces.json')->json()['data'];

        return view('super_admin.create', compact('roles', 'provinsi'));
    }

    public function store(Request $request)
    {
        $allowedRoles = Role::whereIn('name', ['Kepala Sekolah', 'Guru Mapel', 'Wali Kelas', 'Bagian Akademik'])
            ->pluck('name')
            ->toArray();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => ['required', Rule::in($allowedRoles)],
            'jenis_kelamin' => 'required|in:l,p',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'password' => Hash::make($request->password),
            ]);
            $user->assignRole($request->role);

            // Insert ke staff
            DB::table('staff')->insert([
                'user_id' => $user->id,
                'nip' => $user->username,
                'nama' => $user->name,
                'jenis_kelamin' => $request->jenis_kelamin,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return redirect()->route('superadmin.users.index')->with('success', 'User & staff berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }
}
