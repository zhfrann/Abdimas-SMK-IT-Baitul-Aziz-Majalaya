<?php

namespace App\Http\Controllers;

use App\Models\Intrakurikuler;
use App\Models\KelasAjar;
use App\Models\KehadiranIntrakurikuler;
use App\Models\RiwayatKelas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AbsensiControllerIntrakurikuler extends Controller
{
    public function listIntrakurikuler()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $id = $user->id;
        $role = $user?->getRoleNames()->first();

        $q = Intrakurikuler::query()
            ->with([
                'kelasAjar' => function ($qq) {
                    $qq->with(['kelas', 'tahunAjaran'])
                        ->withCount('riwayatKelas');
                },
                'pengampu.staff',
            ])
            ->orderByDesc('intrakurikuler_id');

        if ($role === 'Guru Mapel') {
            $q->where('pengampu_user_id', $id);
        }

        $intrakurikuler = $q->get();

        // opsional kalau kamu masih butuh buat modal create intrakurikuler
        $kelasAjar = KelasAjar::with(['kelas', 'tahunAjaran'])
            ->orderByDesc('tahun_ajaran_id')
            ->orderByDesc('kelas_ajar_id')
            ->get();

        $guru = User::role('Guru Mapel')
            ->with('staff')
            ->orderBy('name')
            ->get();

        return view('absensi_intrakurikuler.index_intrakurikuler', compact('intrakurikuler', 'kelasAjar', 'guru'));
    }

    public function harian(Request $request, Intrakurikuler $intrakurikuler)
    {
        $selectedDate = $request->get('date') ?: now()->format('Y-m-d');

        $intrakurikuler->load([
            'kelasAjar.kelas',
            'kelasAjar.tahunAjaran',
            'kelasAjar.riwayatKelas.siswa.user', // pastikan relasi ada
        ]);

        // daftar siswa berdasar kelas_ajar intrakurikuler ini
        $students = $intrakurikuler->kelasAjar->riwayatKelas
            ->map(function ($rk) {
                return [
                    'riwayat_kelas_id' => $rk->riwayat_kelas_id,
                    'name' => $rk->siswa?->nama ?? $rk->siswa?->user?->name ?? '-',
                    'kelas' => $rk->kelasAjar?->kelas?->nama_kelas ?? $rk->kelas_ajar_id, // fallback
                    'avatar' => '/build/images/user/avatar-1.jpg', // optional: ganti kalau punya avatar
                ];
            })
            ->values()
            ->all();

        // absensi tanggal ini untuk semua siswa (key: riwayat_kelas_id)
        $attendanceMap = KehadiranIntrakurikuler::query()
            ->where('intrakurikuler_id', $intrakurikuler->intrakurikuler_id)
            ->whereDate('tanggal', $selectedDate)
            ->get()
            ->keyBy('riwayat_kelas_id');

        // rekap kecil
        $recap = [
            'hadir' => 0,
            'alpha' => 0,
            'sakit' => 0,
            'izin'  => 0,
            'belum' => 0,
        ];

        foreach ($students as $s) {
            $att = $attendanceMap->get($s['riwayat_kelas_id']);
            if (!$att) { $recap['belum']++; continue; }
            $recap[$att->status] = ($recap[$att->status] ?? 0) + 1;
        }

        return view('absensi_intrakurikuler.index', [
            'intrakurikuler' => $intrakurikuler,
            'selectedDate'   => $selectedDate,
            'students'       => $students,
            'attendanceMap'  => $attendanceMap,
            'recap'          => $recap,
        ]);
    }

    public function storeHarian(Request $request, Intrakurikuler $intrakurikuler)
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'riwayat_kelas_id' => ['required', 'integer'],
            'status' => ['required', Rule::in(['hadir', 'alpha', 'sakit', 'izin'])],
            'note' => ['nullable', 'string'],
        ]);

        // validasi note wajib untuk izin/sakit
        if (in_array($data['status'], ['izin', 'sakit'], true) && blank($data['note'])) {
            return back()->with('warning', 'Keterangan wajib diisi untuk status Izin / Sakit.');
        }

        // pastikan riwayat_kelas_id memang milik kelas_ajar intrakurikuler ini
        $validRk = RiwayatKelas::query()
            ->where('riwayat_kelas_id', $data['riwayat_kelas_id'])
            ->where('kelas_ajar_id', $intrakurikuler->kelas_ajar_id)
            ->exists();

        if (!$validRk) {
            return back()->with('warning', 'Siswa tidak valid untuk kelas/mapel ini.');
        }

        $where = [
            'intrakurikuler_id' => $intrakurikuler->intrakurikuler_id,
            'riwayat_kelas_id'  => $data['riwayat_kelas_id'],
            'tanggal'           => Carbon::parse($data['tanggal'])->format('Y-m-d'),
        ];

        $values = [
            'status' => $data['status'],
            'note'   => $data['note'] ?? null,
            'updated_by' => Auth::id(),
        ];

        // kalau insert baru, set created_by juga
        KehadiranIntrakurikuler::query()->updateOrCreate(
            $where,
            $values + ['created_by' => Auth::id()]
        );

        return back()->with('success', 'Absensi tersimpan.');
    }

    public function rekap(Request $request, Intrakurikuler $intrakurikuler)
    {
        $intrakurikuler->load([
            'kelasAjar.kelas',
            'kelasAjar.tahunAjaran',
            'kelasAjar.riwayatKelas.siswa.user',
        ]);

        // default range: bulan ini
        $from = $request->get('from') ?: now()->startOfMonth()->format('Y-m-d');
        $to   = $request->get('to')   ?: now()->format('Y-m-d');

        // list siswa
        $rkList = $intrakurikuler->kelasAjar->riwayatKelas;

        // aggregate counts per riwayat_kelas_id
        $stats = KehadiranIntrakurikuler::query()
            ->select([
                'riwayat_kelas_id',
                DB::raw("SUM(CASE WHEN status='hadir' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN status='alpha' THEN 1 ELSE 0 END) as alpha"),
                DB::raw("SUM(CASE WHEN status='sakit' THEN 1 ELSE 0 END) as sakit"),
                DB::raw("SUM(CASE WHEN status='izin'  THEN 1 ELSE 0 END) as izin"),
            ])
            ->where('intrakurikuler_id', $intrakurikuler->intrakurikuler_id)
            ->whereBetween('tanggal', [$from, $to])
            ->groupBy('riwayat_kelas_id')
            ->get()
            ->keyBy('riwayat_kelas_id');

        $rows = $rkList->map(function ($rk) use ($stats) {
            $s = $stats->get($rk->riwayat_kelas_id);

            $hadir = (int)($s->hadir ?? 0);
            $alpha = (int)($s->alpha ?? 0);
            $sakit = (int)($s->sakit ?? 0);
            $izin  = (int)($s->izin  ?? 0);

            $total = $hadir + $alpha + $sakit + $izin;
            $persen = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;

            return [
                'name' => $rk->siswa?->nama ?? $rk->siswa?->user?->name ?? '-',
                'avatar' => '/build/images/user/avatar-1.jpg',
                'hadir' => $hadir,
                'alpha' => $alpha,
                'sakit' => $sakit,
                'izin'  => $izin,
                'persen' => $persen,
            ];
        })->values()->all();

        return view('absensi_intrakurikuler.intrakurikuler_rekap', [
            'intrakurikuler' => $intrakurikuler,
            'rows' => $rows,
            'from' => $from,
            'to' => $to,
        ]);
    }
}
