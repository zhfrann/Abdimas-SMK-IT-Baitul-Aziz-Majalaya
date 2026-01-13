<?php

namespace App\Http\Controllers;

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
}
