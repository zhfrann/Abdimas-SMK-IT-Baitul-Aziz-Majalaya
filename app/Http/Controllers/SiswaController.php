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
        $province = Provinsi::query()->orderBy('nama')->get();

        return view('akademik.siswa.create', compact('kelas_ajar', 'orangTua', 'province'));
    }

    public function store(Request $request, KelasAjar $kelas_ajar)
    {
        $validated = $request->validate([
            // user
            'name' => ['required','string','max:255'],
            'username' => ['required','string','max:50', Rule::unique('users','username')],
            'email' => ['nullable','email','max:255', Rule::unique('users','email')],
            'password' => ['required','string','min:6','confirmed'],

            // siswa
            'nis' => ['required','string','max:50', Rule::unique('siswa','nis')],
            'nisn' => ['required','string','max:50', Rule::unique('siswa','nisn')],
            'jenis_kelamin' => ['required', Rule::in(['l','p'])],
            'tempat_lahir_kabupaten_id' => ['required','exists:kabupaten,kabupaten_id'],
            'tanggal_lahir' => ['required','date'],
            'agama' => ['required','string','max:100'],
            'pendidikan_sebelumnya' => ['required','string','max:255'],
            'alamat' => ['required','string'],
            'orang_tua_id' => ['required','exists:orang_tua,orang_tua_id'],
            'kelurahan_id' => ['required','exists:kelurahan,kelurahan_id'],
        ]);

        DB::transaction(function () use ($validated, $kelas_ajar) {
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'] ?? null,
                'password' => Hash::make($validated['password']),
            ]);

            $user->assignRole('Siswa');

            $siswa = Siswa::create([
                'user_id' => $user->id,
                'nis' => $validated['nis'],
                'nisn' => $validated['nisn'],
                'nama' => $validated['name'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tempat_lahir_kabupaten_id' => $validated['tempat_lahir_kabupaten_id'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'agama' => $validated['agama'],
                'pendidikan_sebelumnya' => $validated['pendidikan_sebelumnya'],
                'alamat' => $validated['alamat'],
                'orang_tua_id' => $validated['orang_tua_id'],
                'kelurahan_id' => $validated['kelurahan_id'],
            ]);

            // masukkan siswa ke kelas ajar ini
            RiwayatKelas::create([
                'siswa_id' => $siswa->siswa_id,
                'kelas_ajar_id' => $kelas_ajar->kelas_ajar_id,
            ]);
        });

        return redirect()->route('akademik.siswa.index', $kelas_ajar->kelas_ajar_id)
            ->with('success', 'Siswa berhasil ditambahkan ke kelas ini.');
    }

    public function edit(KelasAjar $kelas_ajar, Siswa $siswa)
    {
        $kelas_ajar->load(['kelas', 'tahunAjaran']);
        $siswa->load(['user']);

        $orangTua = OrangTua::query()->orderByDesc('orang_tua_id')->get();
        $kabupaten = Kabupaten::query()->orderBy('nama')->get();
        $kelurahan = Kelurahan::query()->with('kecamatan.kabupaten')->orderBy('nama')->get();

        return view('akademik.siswa.edit', compact('kelas_ajar', 'siswa', 'orangTua', 'kabupaten', 'kelurahan'));
    }

    public function update(Request $request, KelasAjar $kelas_ajar, Siswa $siswa)
    {
        $siswa->load(['user']);

        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'username' => ['required','string','max:50', Rule::unique('users','username')->ignore($siswa->user_id)],
            'email' => ['nullable','email','max:255', Rule::unique('users','email')->ignore($siswa->user_id)],
            'password' => ['nullable','string','min:6','confirmed'],

            'nis' => ['required','string','max:50', Rule::unique('siswa','nis')->ignore($siswa->siswa_id, 'siswa_id')],
            'nisn' => ['required','string','max:50', Rule::unique('siswa','nisn')->ignore($siswa->siswa_id, 'siswa_id')],
            'jenis_kelamin' => ['required', Rule::in(['l','p'])],
            'tempat_lahir_kabupaten_id' => ['required','exists:kabupaten,kabupaten_id'],
            'tanggal_lahir' => ['required','date'],
            'agama' => ['required','string','max:100'],
            'pendidikan_sebelumnya' => ['required','string','max:255'],
            'alamat' => ['required','string'],
            'orang_tua_id' => ['required','exists:orang_tua,orang_tua_id'],
            'kelurahan_id' => ['required','exists:kelurahan,kelurahan_id'],
        ]);

        DB::transaction(function () use ($validated, $siswa) {
            $siswa->user->update([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'] ?? null,
                ...(isset($validated['password']) && $validated['password']
                    ? ['password' => Hash::make($validated['password'])]
                    : []),
            ]);

            $siswa->update([
                'nis' => $validated['nis'],
                'nisn' => $validated['nisn'],
                'nama' => $validated['name'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tempat_lahir_kabupaten_id' => $validated['tempat_lahir_kabupaten_id'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'agama' => $validated['agama'],
                'pendidikan_sebelumnya' => $validated['pendidikan_sebelumnya'],
                'alamat' => $validated['alamat'],
                'orang_tua_id' => $validated['orang_tua_id'],
                'kelurahan_id' => $validated['kelurahan_id'],
            ]);
        });

        return redirect()->route('akademik.siswa.index', $kelas_ajar->kelas_ajar_id)
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(KelasAjar $kelas_ajar, Siswa $siswa)
    {
        // hapus siswa dari kelas ajar ini (hapus riwayat_kelas saja)
        RiwayatKelas::where('kelas_ajar_id', $kelas_ajar->kelas_ajar_id)
            ->where('siswa_id', $siswa->siswa_id)
            ->delete();

        return redirect()->route('akademik.siswa.index', $kelas_ajar->kelas_ajar_id)
            ->with('success', 'Siswa berhasil dikeluarkan dari kelas ini.');
    }
}
