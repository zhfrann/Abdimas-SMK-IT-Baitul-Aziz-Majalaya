<?php

namespace App\Http\Controllers;

use App\Models\Kabupaten;
use App\Models\Kelurahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    public function provinsi()
    {
        return response()->json(DB::table('provinsi')->orderBy('nama')->get());
    }

    public function kabupaten($provinsi_id)
    {
        return response()->json(
            DB::table('kabupaten')
                ->where('provinsi_id', $provinsi_id)
                ->orderBy('nama')
                ->get()
        );
    }

    public function kecamatan($kabupaten_id)
    {
        return response()->json(
            DB::table('kecamatan')
                ->where('kabupaten_id', $kabupaten_id)
                ->orderBy('nama')
                ->get()
        );
    }

    public function kelurahan($kecamatan_id)
    {
        return response()->json(
            DB::table('kelurahan')
                ->where('kecamatan_id', $kecamatan_id)
                ->orderBy('nama')
                ->get()
        );
    }

    public function searchKabupaten(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $page = max(1, (int) $request->get('page', 1));
        $perPage = 20;

        $query = Kabupaten::query()
            ->join('provinsi', 'provinsi.provinsi_id', '=', 'kabupaten.provinsi_id')
            ->select([
                'kabupaten.kabupaten_id as id',
                'kabupaten.nama as kabupaten_nama',
                'provinsi.nama as provinsi_nama',
            ])
            ->orderBy('provinsi.nama')
            ->orderBy('kabupaten.nama');

        if ($q !== '') {
            // search kena provinsi + kabupaten
            $query->where(function ($w) use ($q) {
                $w->where('kabupaten.nama', 'like', "%{$q}%")
                    ->orWhere('provinsi.nama', 'like', "%{$q}%");
            });
        }

        $total = (clone $query)->count();
        $rows = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        return response()->json([
            'results' => $rows->map(fn($r) => [
                'id' => $r->id,
                'text' => "{$r->kabupaten_nama} — {$r->provinsi_nama}",
            ]),
            'pagination' => [
                'more' => ($page * $perPage) < $total,
            ],
        ]);
    }

    public function searchKelurahan(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $page = max(1, (int) $request->get('page', 1));
        $perPage = 20;

        $query = Kelurahan::query()
            ->join('kecamatan', 'kecamatan.kecamatan_id', '=', 'kelurahan.kecamatan_id')
            ->join('kabupaten', 'kabupaten.kabupaten_id', '=', 'kecamatan.kabupaten_id')
            ->join('provinsi', 'provinsi.provinsi_id', '=', 'kabupaten.provinsi_id')
            ->select([
                'kelurahan.kelurahan_id as id',
                'kelurahan.nama as kelurahan_nama',
                'kecamatan.nama as kecamatan_nama',
                'kabupaten.nama as kabupaten_nama',
                'provinsi.nama as provinsi_nama',
            ])
            ->orderBy('provinsi.nama')
            ->orderBy('kabupaten.nama')
            ->orderBy('kecamatan.nama')
            ->orderBy('kelurahan.nama');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('kelurahan.nama', 'like', "%{$q}%")
                    ->orWhere('kecamatan.nama', 'like', "%{$q}%")
                    ->orWhere('kabupaten.nama', 'like', "%{$q}%")
                    ->orWhere('provinsi.nama', 'like', "%{$q}%");
            });
        }

        $total = (clone $query)->count();
        $rows = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        return response()->json([
            'results' => $rows->map(fn($r) => [
                'id' => $r->id,
                'text' => "{$r->kelurahan_nama}, {$r->kecamatan_nama}, {$r->kabupaten_nama} — {$r->provinsi_nama}",
            ]),
            'pagination' => [
                'more' => ($page * $perPage) < $total,
            ],
        ]);
    }
}
