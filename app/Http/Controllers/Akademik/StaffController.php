<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    public function index()
    {
        $users = User::role(['Guru Mapel', 'Wali Kelas'])->with('roles')->get();
        return view('akademik.staff.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::whereIn('name', ['Guru Mapel', 'Wali Kelas'])->get();
        return view('akademik.staff.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:guru mapel,wali kelas',
            'nip' => 'required|string|unique:staff,nip',
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

            DB::table('staff')->insert([
                'user_id' => $user->id,
                'nip' => $request->nip,
                'nama' => $request->name,
                'jenis_kelamin' => $request->jenis_kelamin,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return redirect()->route('akademik.staff.index')->with('success', 'Guru berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $staff = DB::table('staff')->where('user_id', $user->id)->first();
        $roles = Role::whereIn('name', ['Guru Mapel', 'Wali Kelas'])->get();
        return view('akademik.staff.edit', compact('user', 'staff', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $staff = DB::table('staff')->where('user_id', $user->id)->first();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            // 'role' => 'required|in:guru mapel,wali kelas',
            // 'nip' => 'required|string|unique:staff,nip,' . $staff->staff_id . ',staff_id',
            'jenis_kelamin' => 'required|in:l,p',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $user->name = $request->name;
            $user->username = $request->username;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();
            // $user->syncRoles([$request->role]);

            DB::table('staff')->where('staff_id', $staff->staff_id)->update([
                // 'nip' => $request->nip,
                'nama' => $request->name,
                'jenis_kelamin' => $request->jenis_kelamin,
                'updated_at' => now(),
            ]);

            DB::commit();
            return redirect()->route('akademik.staff.index')->with('success', 'Data staff berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal update data: ' . $e->getMessage()]);
        }
    }
}
