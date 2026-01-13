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

            // domisili siswa: bisa dari select (kelurahan_id) atau hidden (kelurahan_id_hidden)
            'kelurahan_id' => ['nullable', 'exists:kelurahan,kelurahan_id'],
            'kelurahan_id_hidden' => ['nullable', 'exists:kelurahan,kelurahan_id'],

            // pilih ortu existing (opsional)
            'orang_tua_id' => ['nullable', 'exists:orang_tua,orang_tua_id'],

            // ortu baru (WAJIB hanya jika orang_tua_id kosong)
            'ortu.nama_ayah' => ['required_without:orang_tua_id', 'string', 'max:255'],
            'ortu.nama_ibu' => ['required_without:orang_tua_id', 'string', 'max:255'],
            'ortu.pekerjaan_ayah' => ['required_without:orang_tua_id', 'string', 'max:255'],
            'ortu.pekerjaan_ibu' => ['required_without:orang_tua_id', 'string', 'max:255'],
            'ortu.jalan' => ['required_without:orang_tua_id', 'string', 'max:255'],
            'ortu.kelurahan_id' => ['required_without:orang_tua_id', 'exists:kelurahan,kelurahan_id'],

            'alamat_sama_ortu' => ['nullable'],
        ], [
            // pesan lebih enak dibaca
            'ortu.*.required_without' => 'Jika tidak memilih Orang Tua, maka data orang tua baru wajib diisi.',
        ]);

        // ambil kelurahan siswa dari hidden dulu, fallback ke select
        $kelurahanSiswaInput = $validated['kelurahan_id_hidden']
            ?? $validated['kelurahan_id']
            ?? null;

        if (!$kelurahanSiswaInput && empty($validated['alamat_sama_ortu'])) {
            return back()->withInput()->withErrors([
                'kelurahan_id' => 'Kelurahan domisili siswa wajib dipilih.',
            ]);
        }

        try {
            DB::transaction(function () use ($validated, $kelas_ajar, $kelurahanSiswaInput) {

                // 1) buat user siswa
                $userSiswa = User::create([
                    'name' => $validated['name'],
                    'username' => $validated['nis'], // username siswa = NIS
                    'email' => null,
                    'password' => Hash::make($validated['password']),
                ]);
                $userSiswa->assignRole('Siswa');

                // 2) tentukan orang tua (existing / create baru)
                $orangTua = null;

                if (!empty($validated['orang_tua_id'])) {
                    // pakai ortu existing
                    $orangTua = OrangTua::with('kelurahan')->findOrFail($validated['orang_tua_id']);
                } else {
                    // buat ortu baru
                    $ortuUsernameBase = 'ortu_' . $validated['nis'];
                    $ortuUsername = $ortuUsernameBase;
                    $i = 1;
                    while (User::where('username', $ortuUsername)->exists()) {
                        $ortuUsername = $ortuUsernameBase . '_' . $i;
                        $i++;
                    }

                    $userOrtu = User::create([
                        'name' => $validated['ortu']['nama_ayah'],
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
                }

                // 3) alamat/kelurahan siswa
                $alamatSiswa = $validated['alamat'];
                $kelurahanSiswa = $kelurahanSiswaInput;

                if (!empty($validated['alamat_sama_ortu']) && $orangTua) {
                    $alamatSiswa = $orangTua->jalan;
                    $kelurahanSiswa = $orangTua->kelurahan_id;
                }

                // 4) buat siswa
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
}
