<?php

namespace App\Http\Controllers;

use App\Models\Ekstrakurikuler;
use App\Models\KelasAjar;
use App\Models\OrangTua;
use App\Models\RiwayatKelas;
use App\Models\Siswa;
use App\Models\SiswaEkstrakurikuler;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EkstrakurikulerSiswaController extends Controller
{
    // Tampilkan daftar siswa ekskul
    public function index($ekstrakurikuler_id)
    {
        $user = Auth::user();
        $ekskul = Ekstrakurikuler::with('tahunAjaran')->findOrFail($ekstrakurikuler_id);
        if (!$user->hasRole('Bagian Akademik') && $ekskul->user_id != $user->id) {
            $namaEkstrakurikuler = $ekskul->nama_pelajaran;
            $tahunAjaran = $ekskul->tahunAjaran->tahun;
            $semester = $ekskul->tahunAjaran->semester;
            return back()->with('error', "Anda tidak punya akses untuk melihat Daftar Siswa di ekstrakurikuler $namaEkstrakurikuler $tahunAjaran $semester");
        }

        $siswaEkskul = SiswaEkstrakurikuler::with(['siswa.user', 'siswa.riwayatKelasTerakhir.kelasAjar.kelas'])
            ->where('ekstrakurikuler_id', $ekskul->ekstrakurikuler_id)
            ->get();

        return view('ekstrakurikuler.manage_siswa', compact('ekskul', 'siswaEkskul'));
    }

    public function create($ekstrakurikuler_id)
    {
        $ekskul = Ekstrakurikuler::with('tahunAjaran')->findOrFail($ekstrakurikuler_id);
        $orangTua = OrangTua::orderByDesc('orang_tua_id')->get();

        return view('ekstrakurikuler.create_siswa', compact('ekskul', 'orangTua'));
    }

    public function store(Request $request, $ekstrakurikuler_id)
    {
        $ekskul = Ekstrakurikuler::findOrFail($ekstrakurikuler_id);
        $tahunAjaranId = $ekskul->tahun_ajaran_id;

        $isOrtuBaru = empty($request->orang_tua_id);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'nis' => ['required', 'string', 'max:50', \Illuminate\Validation\Rule::unique('siswa', 'nis')],
            'nisn' => ['required', 'string', 'max:50', \Illuminate\Validation\Rule::unique('siswa', 'nisn')],
            'jenis_kelamin' => ['required', \Illuminate\Validation\Rule::in(['l', 'p'])],
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
            DB::transaction(function () use ($validated, $ekskul, $tahunAjaranId, $isOrtuBaru) {
                // Buat user siswa
                $user = User::create([
                    'name' => $validated['name'],
                    'username' => $validated['nis'],
                    'password' => bcrypt($validated['password']),
                    'role' => 'Siswa',
                ]);

                // Buat/ambil orang tua
                if ($isOrtuBaru) {
                    $ortu = OrangTua::create([
                        'nama_ayah' => $validated['ortu']['nama_ayah'],
                        'nama_ibu' => $validated['ortu']['nama_ibu'],
                        'pekerjaan_ayah' => $validated['ortu']['pekerjaan_ayah'],
                        'pekerjaan_ibu' => $validated['ortu']['pekerjaan_ibu'],
                        'jalan' => $validated['ortu']['jalan'],
                        'kelurahan_id' => $validated['ortu']['kelurahan_id'],
                    ]);
                } else {
                    $ortu = OrangTua::find($validated['orang_tua_id']);
                }

                // Buat siswa
                $siswa = Siswa::create([
                    'nama' => $user->name,
                    'user_id' => $user->id,
                    'orang_tua_id' => $ortu->orang_tua_id,
                    'nis' => $validated['nis'],
                    'nisn' => $validated['nisn'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'agama' => $validated['agama'],
                    'tempat_lahir_kabupaten_id' => $validated['tempat_lahir_kabupaten_id'],
                    'pendidikan_sebelumnya' => $validated['pendidikan_sebelumnya'],
                    'alamat' => $validated['alamat'],
                    'kelurahan_id' => $validated['kelurahan_id_hidden'],
                ]);

                // Cari kelas_ajar di tahun ajaran ekskul (boleh random, atau tidak perlu, karena ekskul tidak terikat kelas)
                // Buat riwayat_kelas di tahun ajaran ekskul (kelas_ajar_id bisa null jika tidak ada)
                $kelasAjar = KelasAjar::where('tahun_ajaran_id', $tahunAjaranId)->first();
                $riwayatKelas = RiwayatKelas::create([
                    'siswa_id' => $siswa->siswa_id,
                    'kelas_ajar_id' => $kelasAjar ? $kelasAjar->kelas_ajar_id : null,
                ]);

                // Tambahkan ke pivot ekskul
                SiswaEkstrakurikuler::create([
                    'ekstrakurikuler_id' => $ekskul->ekstrakurikuler_id,
                    'riwayat_kelas_id' => $riwayatKelas->riwayat_kelas_id,
                ]);
            });

            return redirect()->route('ekstrakurikuler.manage-siswa.index', $ekskul->ekstrakurikuler_id)
                ->with('success', 'Siswa berhasil ditambahkan ke ekskul.');
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['name' => 'Gagal menambah siswa: ' . $e->getMessage()]);
        }
    }



    // Hapus satu siswa dari ekskul
    public function destroy($ekstrakurikuler_id, $siswa_ekstrakurikuler_id)
    {
        $item = SiswaEkstrakurikuler::where('ekstrakurikuler_id', $ekstrakurikuler_id)
            ->findOrFail($siswa_ekstrakurikuler_id);
        $item->delete();

        return back()->with('success', 'Siswa berhasil dihapus dari ekskul.');
    }

    public function ajaxSearchSiswa(Request $request, $ekstrakurikuler_id)
    {
        $q = $request->input('q');
        $ekskul = Ekstrakurikuler::findOrFail($ekstrakurikuler_id);

        // Cari siswa yang sudah punya user, dan belum masuk ekskul ini di tahun ajaran ekskul
        $tahunAjaranId = $ekskul->tahun_ajaran_id;

        $siswaQuery = Siswa::query()
            ->whereHas('user')
            ->whereHas('riwayatKelas') // hanya siswa yang sudah pernah masuk kelas
            ->where(function ($query) use ($q) {
                $query->where('nama', 'like', "%$q%")
                    ->orWhere('nis', 'like', "%$q%")
                    ->orWhere('nisn', 'like', "%$q%");
            })
            // exclude siswa yang sudah masuk ekskul ini
            ->whereDoesntHave('siswaEkstrakurikuler', function ($q2) use ($ekskul) {
                $q2->where('ekstrakurikuler_id', $ekskul->ekstrakurikuler_id);
            });
        // ->limit(20);

        $results = $siswaQuery->get()->map(function ($siswa) {
            // Ambil riwayat_kelas terbaru
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

    public function addExistingSiswa(Request $request, $ekstrakurikuler_id)
    {
        $ekskul = Ekstrakurikuler::findOrFail($ekstrakurikuler_id);

        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id',
        ]);

        $exists = SiswaEkstrakurikuler::where('ekstrakurikuler_id', $ekskul->ekstrakurikuler_id)
            ->where('siswa_id', $request->siswa_id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['siswa_id' => 'Siswa sudah terdaftar di ekskul ini.']);
        }

        SiswaEkstrakurikuler::create([
            'siswa_id' => $request->siswa_id,
            'ekstrakurikuler_id' => $ekskul->ekstrakurikuler_id,
        ]);

        return back()->with('success', 'Siswa berhasil dimasukkan ke ekskul.');
    }
}
