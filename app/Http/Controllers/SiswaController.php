<?php

namespace App\Http\Controllers;

use App\Models\KelasAjar;
use App\Models\RiwayatKelas;
use App\Models\Kabupaten;
use App\Models\Kelurahan;
use App\Models\OrangTua;
use App\Models\Provinsi;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SiswaController extends Controller
{
    public function index(KelasAjar $kelas_ajar)
    {
        $kelas_ajar->load(['kelas', 'tahunAjaran']);

        $riwayat = RiwayatKelas::query()
            ->where('kelas_ajar_id', $kelas_ajar->kelas_ajar_id)
            ->with(['siswa.user'])
            ->orderByDesc('riwayat_kelas_id')
            ->get();

        return view('akademik.siswa.index', compact('kelas_ajar', 'riwayat'));
    }

    public function create(KelasAjar $kelas_ajar)
    {
        $kelas_ajar->load(['kelas', 'tahunAjaran']);

        $orangTua = OrangTua::query()->orderByDesc('orang_tua_id')->get();

        return view('akademik.siswa.create', compact('kelas_ajar', 'orangTua'));
    }

    public function store(Request $request, KelasAjar $kelas_ajar)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],

            'nis' => ['required', 'string', 'max:50', Rule::unique('siswa', 'nis')],
            'nisn' => ['required', 'string', 'max:50', Rule::unique('siswa', 'nisn')],
            'jenis_kelamin' => ['required', Rule::in(['l', 'p'])],
            'tanggal_lahir' => ['required', 'date'],
            'agama' => ['required', 'string', 'max:100'],
            'tempat_lahir_kabupaten_id' => ['required', 'exists:kabupaten,kabupaten_id'],
            'pendidikan_sebelumnya' => ['required', 'string', 'max:255'],

            // alamat siswa
            'alamat' => ['required', 'string'],
            'kelurahan_id_hidden' => ['required', 'exists:kelurahan,kelurahan_id'],

            // nested ortu
            'ortu.nama_ayah' => ['required', 'string', 'max:255'],
            'ortu.nama_ibu' => ['required', 'string', 'max:255'],
            'ortu.pekerjaan_ayah' => ['required', 'string', 'max:255'],
            'ortu.pekerjaan_ibu' => ['required', 'string', 'max:255'],
            'ortu.jalan' => ['required', 'string', 'max:255'],
            'ortu.kelurahan_id' => ['required', 'exists:kelurahan,kelurahan_id'],

            // opsional checkbox
            'alamat_sama_ortu' => ['nullable'],
        ]);

        try {
            DB::transaction(function () use ($validated, $kelas_ajar) {

                $userSiswa = User::create([
                    'name' => $validated['name'],
                    'username' => $validated['nis'],
                    'email' => null,
                    'password' => Hash::make($validated['password']),
                ]);
                $userSiswa->assignRole('Siswa');

                $ortuUsername = 'ortu_' . $validated['nis'];

                $suffix = 1;
                $base = $ortuUsername;
                while (User::where('username', $ortuUsername)->exists()) {
                    $ortuUsername = $base . '_' . $suffix;
                    $suffix++;
                }

                $userOrtu = User::create([
                    'name' => $validated['ortu']['nama_ayah'], // atau gabung ayah/ibu
                    'username' => $ortuUsername,
                    'email' => null,
                    'password' => Hash::make($validated['password']), // sama dulu
                ]);
                $userOrtu->assignRole('Orang Tua');

                $orangTua = OrangTua::create([
                    'user_id' => $userOrtu->id,
                    'nama_ayah' => $validated['ortu']['nama_ayah'],
                    'nama_ibu' => $validated['ortu']['nama_ibu'],
                    'pekerjaan_ayah' => $validated['ortu']['pekerjaan_ayah'],
                    'pekerjaan_ibu' => $validated['ortu']['pekerjaan_ibu'],
                    'jalan' => $validated['ortu']['jalan'],
                    'kelurahan_id' => $validated['ortu']['kelurahan_id'],
                ]);

                $alamatSiswa = !empty($validated['alamat_sama_ortu'])
                    ? $validated['ortu']['jalan']
                    : $validated['alamat'];

                $kelurahanSiswa = !empty($validated['alamat_sama_ortu'])
                    ? $validated['ortu']['kelurahan_id']
                    : $validated['kelurahan_id_hidden'];

                $siswa = Siswa::create([
                    'user_id' => $userSiswa->id,
                    'nis' => $validated['nis'],
                    'nisn' => $validated['nisn'],
                    'nama' => $validated['name'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'tempat_lahir_kabupaten_id' => $validated['tempat_lahir_kabupaten_id'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'agama' => $validated['agama'],
                    'pendidikan_sebelumnya' => $validated['pendidikan_sebelumnya'],
                    'alamat' => $alamatSiswa,
                    'orang_tua_id' => $orangTua->orang_tua_id,
                    'kelurahan_id' => $kelurahanSiswa,
                ]);

                RiwayatKelas::create([
                    'siswa_id' => $siswa->siswa_id,
                    'kelas_ajar_id' => $kelas_ajar->kelas_ajar_id,
                ]);
            });

            return redirect()
                ->route('akademik.siswa.index', $kelas_ajar->kelas_ajar_id)
                ->with('success', 'Siswa & akun orang tua berhasil dibuat, dan siswa dimasukkan ke kelas ini.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function edit(KelasAjar $kelas_ajar, Siswa $siswa)
    {
        // $kelas_ajar->load(['kelas', 'tahunAjaran']);
        // $siswa->load(['user']);

        // $orangTua = OrangTua::query()->orderByDesc('orang_tua_id')->get();
        // $kabupaten = Kabupaten::query()->orderBy('nama')->get();
        // $kelurahan = Kelurahan::query()->with('kecamatan.kabupaten')->orderBy('nama')->get();

        // return view('akademik.siswa.edit', compact('kelas_ajar', 'siswa', 'orangTua', 'kabupaten', 'kelurahan'));
    }

    public function update(Request $request, KelasAjar $kelas_ajar, Siswa $siswa)
    {
        // $siswa->load(['user']);

        // $validated = $request->validate([
        //     'name' => ['required', 'string', 'max:255'],
        //     'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($siswa->user_id)],
        //     'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($siswa->user_id)],
        //     'password' => ['nullable', 'string', 'min:6', 'confirmed'],

        //     'nis' => ['required', 'string', 'max:50', Rule::unique('siswa', 'nis')->ignore($siswa->siswa_id, 'siswa_id')],
        //     'nisn' => ['required', 'string', 'max:50', Rule::unique('siswa', 'nisn')->ignore($siswa->siswa_id, 'siswa_id')],
        //     'jenis_kelamin' => ['required', Rule::in(['l', 'p'])],
        //     'tempat_lahir_kabupaten_id' => ['required', 'exists:kabupaten,kabupaten_id'],
        //     'tanggal_lahir' => ['required', 'date'],
        //     'agama' => ['required', 'string', 'max:100'],
        //     'pendidikan_sebelumnya' => ['required', 'string', 'max:255'],
        //     'alamat' => ['required', 'string'],
        //     'orang_tua_id' => ['required', 'exists:orang_tua,orang_tua_id'],
        //     'kelurahan_id' => ['required', 'exists:kelurahan,kelurahan_id'],
        // ]);

        // DB::transaction(function () use ($validated, $siswa) {
        //     $siswa->user->update([
        //         'name' => $validated['name'],
        //         'username' => $validated['username'],
        //         'email' => $validated['email'] ?? null,
        //         ...(isset($validated['password']) && $validated['password']
        //             ? ['password' => Hash::make($validated['password'])]
        //             : []),
        //     ]);

        //     $siswa->update([
        //         'nis' => $validated['nis'],
        //         'nisn' => $validated['nisn'],
        //         'nama' => $validated['name'],
        //         'jenis_kelamin' => $validated['jenis_kelamin'],
        //         'tempat_lahir_kabupaten_id' => $validated['tempat_lahir_kabupaten_id'],
        //         'tanggal_lahir' => $validated['tanggal_lahir'],
        //         'agama' => $validated['agama'],
        //         'pendidikan_sebelumnya' => $validated['pendidikan_sebelumnya'],
        //         'alamat' => $validated['alamat'],
        //         'orang_tua_id' => $validated['orang_tua_id'],
        //         'kelurahan_id' => $validated['kelurahan_id'],
        //     ]);
        // });

        // return redirect()->route('akademik.siswa.index', $kelas_ajar->kelas_ajar_id)
        //     ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(KelasAjar $kelas_ajar, Siswa $siswa)
    {
        // // hapus siswa dari kelas ajar ini (hapus riwayat_kelas saja)
        // RiwayatKelas::where('kelas_ajar_id', $kelas_ajar->kelas_ajar_id)
        //     ->where('siswa_id', $siswa->siswa_id)
        //     ->delete();

        // return redirect()->route('akademik.siswa.index', $kelas_ajar->kelas_ajar_id)
        //     ->with('success', 'Siswa berhasil dikeluarkan dari kelas ini.');
    }
}
