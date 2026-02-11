<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class SekolahController extends Controller
{
    public function index()
    {
        $sekolah = DB::table('sekolah as sc')
            ->leftJoin('kelurahan as kel', 'kel.kelurahan_id', '=', 'sc.kelurahan_id')
            ->select([
                'sc.*',
                'kel.nama as kelurahan_nama',
            ])
            ->first();

        return view('akademik.sekolah.index', compact('sekolah'));
    }

    public function edit()
    {
        $sekolah = DB::table('sekolah')->first();

        $kelurahanLabel = null;
        if ($sekolah && $sekolah->kelurahan_id) {
            $kelurahanLabel = DB::table('kelurahan')
                ->where('kelurahan_id', $sekolah->kelurahan_id)
                ->value('nama');
        }

        return view('akademik.sekolah.edit', [
            'sekolah' => $sekolah,
            'kelurahanLabel' => $kelurahanLabel,
        ]);
    }

    public function update(Request $request)
    {
        $sekolah = DB::table('sekolah')->first();
        if (! $sekolah) {
            return redirect()->route('akademik.sekolah.index')
                ->with('error', 'Data sekolah belum tersedia.');
        }

        $data = $request->validate([
            'nama_sekolah' => ['required', 'string', 'max:255'],
            'nss' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'kelurahan_id' => ['nullable', 'string', Rule::exists('kelurahan', 'kelurahan_id')],
            'website' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telp' => ['nullable', 'string', 'max:50'],

            // tetap disimpan di sekolah
            'nama_kepala_sekolah' => ['nullable', 'string', 'max:255'],
            'nip_kepala_sekolah' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($sekolah, $data) {

            DB::table('sekolah')
                ->where('npsn', $sekolah->npsn)
                ->update(array_merge($data, [
                    'updated_at' => now(),
                ]));

            // 2) Sync ke staff kepala sekolah (ambil 1 saja)
            $namaKepsek = $data['nama_kepala_sekolah'] ?? null;
            $nipKepsek = $data['nip_kepala_sekolah'] ?? null;

            if (! $namaKepsek && ! $nipKepsek) {
                return;
            }

            $kepsekUser = DB::table('users as u')
                ->join('model_has_roles as mhr', function ($join) {
                    $join->on('mhr.model_id', '=', 'u.id')
                        ->where('mhr.model_type', '=', 'App\\Models\\User');
                })
                ->join('roles as r', 'r.id', '=', 'mhr.role_id')
                ->whereIn('r.name', ['Kepala Sekolah'])
                ->select('u.id')
                ->orderBy('u.id')
                ->first();
            
            $updateUser = ['updated_at' => now()];
            if ($namaKepsek) {
                $updateUser['name'] = $namaKepsek;
            }

            if ($nipKepsek) {
                $updateUser['username'] = $nipKepsek;
            }

            DB::table('users')
                ->where('id', $kepsekUser->id)
                ->update($updateUser);

            if (! $kepsekUser) {
                return;
            }

            $staff = DB::table('staff')->where('user_id', $kepsekUser->id)->first();
            if (! $staff) {
                return;
            }

            $updateStaff = ['updated_at' => now()];
            if ($namaKepsek) {
                $updateStaff['nama'] = $namaKepsek;
            }

            if ($nipKepsek) {
                $updateStaff['nuptk'] = $nipKepsek;
            }

            DB::table('staff')
                ->where('staff_id', $staff->staff_id)
                ->update($updateStaff);
        });

        return redirect()->route('akademik.sekolah.index')
            ->with('success', 'Data sekolah berhasil diperbarui.');
    }
}
