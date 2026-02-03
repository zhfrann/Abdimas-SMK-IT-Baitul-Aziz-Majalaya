<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    public function index()
    {
        $users = User::role(['Kepala Sekolah', 'Guru Mapel', 'Wali Kelas', 'Bagian Akademik'])->with('roles')->get();
        return view('akademik.staff.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::whereIn('name', ['Kepala Sekolah', 'Guru Mapel', 'Wali Kelas', 'Bagian Akademik'])->get();
        return view('akademik.staff.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $allowedRoles = Role::whereIn('name', ['Kepala Sekolah', 'Guru Mapel', 'Wali Kelas', 'Bagian Akademik'])
            ->pluck('name')
            ->toArray();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
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

            DB::table('staff')->insert([
                'user_id' => $user->id,
                'nuptk' => $user->username,
                'nama' => $user->name,
                'jenis_kelamin' => $request->jenis_kelamin,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return redirect()->route('akademik.staff.index')->with('success', 'Guru berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan. Gagal menyimpan data.']);
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
            // 'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            // 'role' => 'required|in:guru mapel,wali kelas',
            // 'nuptk' => 'required|string|unique:staff,nuptk,' . $staff->staff_id . ',staff_id',
            'jenis_kelamin' => 'required|in:l,p',
            'password' => 'nullable|string|min:6|confirmed',
        ]);


        DB::beginTransaction();
        try {
            $user->name = $request->name;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();
            // $user->syncRoles([$request->role]);  //karena role tidak bisa diupdate

            DB::table('staff')->where('staff_id', $staff->staff_id)->update([
                // 'nuptk' => $request->nuptk,
                'nama' => $request->name,
                'jenis_kelamin' => $request->jenis_kelamin,
                'updated_at' => now(),
            ]);

            DB::commit();
            return redirect()->route('akademik.staff.index')->with('success', 'Data staff berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan. Gagal update data.']);
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        DB::beginTransaction();
        try {
            // Hapus data staff di tabel staff
            DB::table('staff')->where('user_id', $user->id)->delete();

            // Hapus user
            $user->delete();
            DB::commit();
            return redirect()->route('akademik.staff.index')->with('success', 'Staff berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Tidak dapat menghapus staff. Pastikan tidak ada data terkait.');
        }
    }
}
