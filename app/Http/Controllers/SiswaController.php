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
use Illuminate\Validation\ValidationException;

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
        $isOrtuBaru = empty($request->orang_tua_id);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'nis' => ['required', 'string', 'max:50', Rule::unique('siswa', 'nis')],
            'nisn' => ['required', 'string', 'max:50', Rule::unique('siswa', 'nisn')],
            'jenis_kelamin' => ['required', Rule::in(['l', 'p'])],
            'tanggal_lahir' => ['required', 'date'],
            'agama' => ['required', 'string', 'max:100'],
            'tempat_lahir_kabupaten_id' => ['required', 'exists:kabupaten,kabupaten_id'],
            'pendidikan_sebelumnya' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'kelurahan_id_hidden' => ['required', 'exists:kelurahan,kelurahan_id'],
            'alamat_sama_ortu' => ['nullable'],
        ];

        if ($isOrtuBaru) {
            $rules += [
                'ortu.nama_ayah' => ['required', 'string', 'max:255'],
                'ortu.nama_ibu' => ['required', 'string', 'max:255'],
                'ortu.pekerjaan_ayah' => ['required', 'string', 'max:255'],
                'ortu.pekerjaan_ibu' => ['required', 'string', 'max:255'],
                'ortu.jalan' => ['required', 'string', 'max:255'],
                'ortu.kelurahan_id' => ['required', 'exists:kelurahan,kelurahan_id'],
            ];
        } else {
            $rules += [
                'orang_tua_id' => ['required', 'exists:orang_tua,orang_tua_id'],
            ];
        }

        $validated = $request->validate($rules);

        try {
            DB::transaction(function () use ($validated, $kelas_ajar, $isOrtuBaru) {
                $userSiswa = User::create([
                    'name' => $validated['name'],
                    'username' => $validated['nis'], // username siswa = NIS
                    'email' => null,
                    'password' => Hash::make($validated['password']),
                ]);
                $userSiswa->assignRole('Siswa');
                if ($isOrtuBaru) {
                    $ortuUsername = 'ortu_' . $validated['nis'];
                    $suffix = 1;
                    $base = $ortuUsername;
                    while (User::where('username', $ortuUsername)->exists()) {
                        $ortuUsername = $base . '_' . $suffix;
                        $suffix++;
                    }

                    $userOrtu = User::create([
                        'name' => $validated['ortu']['nama_ayah'],
                        'username' => $ortuUsername,
                        'email' => null,
                        'password' => Hash::make($validated['password']),
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
                } else {
                    $orangTuaLama = OrangTua::findOrFail($validated['orang_tua_id']);

                    $ortuUsername = 'ortu_' . $validated['nis'];
                    $suffix = 1;
                    $base = $ortuUsername;
                    while (User::where('username', $ortuUsername)->exists()) {
                        $ortuUsername = $base . '_' . $suffix;
                        $suffix++;
                    }

                    $userOrtu = User::create([
                        'name' => $orangTuaLama->nama_ayah,
                        'username' => $ortuUsername,
                        'email' => null,
                        'password' => Hash::make($validated['password']),
                    ]);

                    $orangTua = OrangTua::create([
                        'user_id' => $userOrtu->id,
                        'nama_ayah' => $orangTuaLama->nama_ayah,
                        'nama_ibu' => $orangTuaLama->nama_ibu,
                        'pekerjaan_ayah' => $orangTuaLama->pekerjaan_ayah,
                        'pekerjaan_ibu' => $orangTuaLama->pekerjaan_ibu,
                        'jalan' => $orangTuaLama->jalan,
                        'kelurahan_id' => $orangTuaLama->kelurahan_id
                    ]);
                }


                $alamatSiswa = !empty($validated['alamat_sama_ortu'])
                    ? $orangTua->jalan
                    : $validated['alamat'];

                $kelurahanSiswa = !empty($validated['alamat_sama_ortu'])
                    ? $orangTua->kelurahan_id
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

                // 5) masuk ke kelas ajar ini
                RiwayatKelas::create([
                    'siswa_id' => $siswa->siswa_id,
                    'kelas_ajar_id' => $kelas_ajar->kelas_ajar_id,
                ]);
            });

            return redirect()
                ->route('akademik.siswa.index', $kelas_ajar->kelas_ajar_id)
                ->with('success', 'Siswa berhasil ditambahkan ke kelas ini.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }


    public function edit(KelasAjar $kelas_ajar, Siswa $siswa)
    {
        $kelas_ajar->load(['kelas', 'tahunAjaran']);

        $siswa->load([
            'user',
            'orangTua.kelurahan.kecamatan.kabupaten.provinsi',
            'kelurahan.kecamatan.kabupaten.provinsi',
            'tempatLahirKabupaten', // kalau kamu punya relasi ini di model Siswa
        ]);

        $orangTua = OrangTua::query()
            ->with('kelurahan')
            ->orderByDesc('orang_tua_id')
            ->get();

        $tempatLahirLabel = $siswa->tempatLahirKabupaten?->nama ?? null;
        $kelurahanLabel   = $siswa->kelurahan?->nama
            ? ($siswa->kelurahan->nama . ' — ' . $siswa->kelurahan->kecamatan?->nama . ' (' . $siswa->kelurahan->kecamatan?->kabupaten?->nama . ')')
            : null;

        $ortuKelurahanLabel = $siswa->orangTua?->kelurahan?->nama
            ? ($siswa->orangTua->kelurahan->nama . ' — ' . $siswa->orangTua->kelurahan->kecamatan?->nama . ' (' . $siswa->orangTua->kelurahan->kecamatan?->kabupaten?->nama . ')')
            : null;

        return view('akademik.siswa.edit', compact(
            'kelas_ajar',
            'siswa',
            'orangTua',
            'tempatLahirLabel',
            'kelurahanLabel',
            'ortuKelurahanLabel'
        ));
    }


    public function update(Request $request, KelasAjar $kelas_ajar, Siswa $siswa)
    {
        $siswa->load(['user', 'orangTua.user']);

        $validated = $request->validate([
            // user siswa
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],

            // siswa
            'nis' => ['required', 'string', 'max:50', Rule::unique('siswa', 'nis')->ignore($siswa->siswa_id, 'siswa_id')],
            'nisn' => ['required', 'string', 'max:50', Rule::unique('siswa', 'nisn')->ignore($siswa->siswa_id, 'siswa_id')],
            'jenis_kelamin' => ['required', Rule::in(['l', 'p'])],
            'tanggal_lahir' => ['required', 'date'],
            'agama' => ['required', 'string', 'max:100'],
            'tempat_lahir_kabupaten_id' => ['required', 'exists:kabupaten,kabupaten_id'],
            'pendidikan_sebelumnya' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],

            'kelurahan_id_hidden' => ['required', 'exists:kelurahan,kelurahan_id'],

            'ortu.nama_ayah' => ['required', 'string', 'max:255'],
            'ortu.nama_ibu' => ['required', 'string', 'max:255'],
            'ortu.pekerjaan_ayah' => ['required', 'string', 'max:255'],
            'ortu.pekerjaan_ibu' => ['required', 'string', 'max:255'],
            'ortu.jalan' => ['required', 'string', 'max:255'],
            'ortu.kelurahan_id' => ['required', 'exists:kelurahan,kelurahan_id'],

            'alamat_sama_ortu' => ['nullable'],
        ]);

        DB::transaction(function () use ($validated, $siswa) {

            $requestUsername = $validated['nis'];
            $existsUsername = User::where('username', $requestUsername)
                ->where('id', '!=', $siswa->user_id)
                ->exists();

            if ($existsUsername) {
                throw ValidationException::withMessages([
                    'nis' => 'NIS ini sudah dipakai sebagai username akun lain.',
                ]);
            }

            $payloadUser = [
                'name' => $validated['name'],
                'username' => $validated['nis'],
            ];

            if (!empty($validated['password'])) {
                $payloadUser['password'] = Hash::make($validated['password']);
            }

            $siswa->user->update($payloadUser);

            if (!$siswa->orangTua) {
                throw ValidationException::withMessages([
                    'ortu' => 'Data orang tua tidak ditemukan untuk siswa ini.',
                ]);
            }

            $ortuInput = $validated['ortu'];

            $siswa->orangTua->update([
                'nama_ayah' => $ortuInput['nama_ayah'],
                'nama_ibu' => $ortuInput['nama_ibu'],
                'pekerjaan_ayah' => $ortuInput['pekerjaan_ayah'],
                'pekerjaan_ibu' => $ortuInput['pekerjaan_ibu'],
                'jalan' => $ortuInput['jalan'],
                'kelurahan_id' => $ortuInput['kelurahan_id'],
            ]);

            if ($siswa->orangTua->user) {
                $payloadUserOrtu = [
                    'name' => $ortuInput['nama_ayah'],
                ];
                if (!empty($validated['password'])) {
                    $payloadUserOrtu['password'] = Hash::make($validated['password']);
                }
                $siswa->orangTua->user->update($payloadUserOrtu);
            }

            $alamatSiswa = $validated['alamat'];
            $kelurahanSiswa = $validated['kelurahan_id_hidden'];

            if (!empty($validated['alamat_sama_ortu'])) {
                $alamatSiswa = $siswa->orangTua->jalan;
                $kelurahanSiswa = $siswa->orangTua->kelurahan_id;
            }

            $siswa->update([
                'nis' => $validated['nis'],
                'nisn' => $validated['nisn'],
                'nama' => $validated['name'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tempat_lahir_kabupaten_id' => $validated['tempat_lahir_kabupaten_id'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'agama' => $validated['agama'],
                'pendidikan_sebelumnya' => $validated['pendidikan_sebelumnya'],
                'alamat' => $alamatSiswa,
                'kelurahan_id' => $kelurahanSiswa,
                'orang_tua_id' => $siswa->orang_tua_id,
            ]);
        });

        return redirect()
            ->route('akademik.siswa.index', $kelas_ajar->kelas_ajar_id)
            ->with('success', 'Data siswa berhasil diperbarui.');
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

    public function ajaxSearchSiswa(Request $request, KelasAjar $kelas_ajar)
    {
        $q = $request->input('q');
        $tahunAjaranId = $kelas_ajar->tahun_ajaran_id;

        // Cari siswa yang pernah punya riwayat kelas di tahun ajaran sebelumnya, tapi belum ada di kelas_ajar ini
        $siswaQuery = Siswa::query()
            ->whereHas('riwayatKelas', function ($q1) use ($tahunAjaranId) {
                $q1->whereHas('kelasAjar', function ($q2) use ($tahunAjaranId) {
                    $q2->where('tahun_ajaran_id', '<', $tahunAjaranId);
                });
            })
            ->whereDoesntHave('riwayatKelas', function ($q3) use ($kelas_ajar) {
                $q3->where('kelas_ajar_id', $kelas_ajar->kelas_ajar_id);
            })
            ->where(function ($query) use ($q) {
                $query->where('nama', 'like', "%$q%")
                    ->orWhere('nis', 'like', "%$q%")
                    ->orWhere('nisn', 'like', "%$q%");
            });

        $results = $siswaQuery->get()->map(function ($siswa) {
            $rk = $siswa->riwayatKelas()->latest('riwayat_kelas_id')->first();
            $kelas = $rk?->kelasAjar?->kelas?->nama_kelas ?? '-';
            $tahun = $rk?->kelasAjar?->tahunAjaran?->tahun ?? '-';
            $semester = $rk?->kelasAjar?->tahunAjaran?->semester ?? '-';

            return [
                'id' => $siswa->siswa_id,
                'text' => "{$siswa->nama} | Kelas: {$kelas} | Tahun: {$tahun} {$semester} | NIS: {$siswa->nis} | NISN: {$siswa->nisn}"
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function addExistingSiswa(Request $request, KelasAjar $kelas_ajar)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id',
        ]);

        // Cek apakah sudah ada di kelas ini
        $exists = RiwayatKelas::where('kelas_ajar_id', $kelas_ajar->kelas_ajar_id)
            ->where('siswa_id', $request->siswa_id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['siswa_id' => 'Siswa sudah terdaftar di kelas ini.']);
        }

        // Cek apakah siswa sudah punya kelas lain di tahun ajaran & semester yang sama
        $tahunAjaranId = $kelas_ajar->tahun_ajaran_id;
        $semester = $kelas_ajar->tahunAjaran->semester;

        $sudahAdaDiTahunAjaranIni = RiwayatKelas::where('siswa_id', $request->siswa_id)
            ->whereHas('kelasAjar', function ($q) use ($tahunAjaranId, $semester) {
                $q->where('tahun_ajaran_id', $tahunAjaranId)
                    ->whereHas('tahunAjaran', function ($q2) use ($semester) {
                        $q2->where('semester', $semester);
                    });
            })
            ->exists();

        if ($sudahAdaDiTahunAjaranIni) {
            return back()->withErrors(['siswa_id' => 'Siswa sudah terdaftar di kelas lain pada tahun ajaran & semester ini.']);
        }

        // Tambahkan ke kelas_ajar (naik kelas)
        RiwayatKelas::create([
            'siswa_id' => $request->siswa_id,
            'kelas_ajar_id' => $kelas_ajar->kelas_ajar_id,
        ]);

        return back()->with('success', 'Siswa berhasil dimasukkan ke kelas.');
    }
}
