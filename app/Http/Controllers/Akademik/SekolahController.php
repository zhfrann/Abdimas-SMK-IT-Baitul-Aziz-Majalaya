<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SekolahController extends Controller
{
    public function index()
    {
        $sekolah = Sekolah::query()
            ->with([
                'kelurahan',
                'staff.user',
            ])
            ->first();

        return view('akademik.sekolah.index', compact('sekolah'));
    }

    public function edit()
    {
        $sekolah = Sekolah::query()
            ->with([
                'kelurahan',
                'staff.user',
            ])
            ->first();

        $kelurahanLabel = $sekolah?->kelurahan?->nama;

        return view('akademik.sekolah.edit', [
            'sekolah' => $sekolah,
            'kelurahanLabel' => $kelurahanLabel,
        ]);
    }

    public function update(Request $request)
    {
        $sekolah = Sekolah::query()
            ->with(['staff.user'])
            ->first();

        if (! $sekolah) {
            return redirect()->route('akademik.sekolah.index')
                ->with('error', 'Data sekolah belum tersedia.');
        }

        $validated = $request->validate([
            'nama_sekolah' => ['required', 'string', 'max:255'],
            'nss' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'kelurahan_id' => ['nullable', 'string', Rule::exists('kelurahan', 'kelurahan_id')],
            'website' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telp' => ['nullable', 'string', 'max:50'],

            'nama_kepala_sekolah' => ['nullable', 'string', 'max:255'],
            'nuptk_kepala_sekolah' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($sekolah, $validated) {
            $sekolahData = [
                'nama_sekolah' => $validated['nama_sekolah'],
                'nss' => $validated['nss'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'kode_pos' => $validated['kode_pos'] ?? null,
                'kelurahan_id' => $validated['kelurahan_id'] ?? null,
                'website' => $validated['website'] ?? null,
                'email' => $validated['email'] ?? null,
                'telp' => $validated['telp'] ?? null,
            ];

            $sekolah->update($sekolahData);

            $namaKepsek = $validated['nama_kepala_sekolah'] ?? null;
            $nuptkKepsek  = $validated['nuptk_kepala_sekolah'] ?? null;

            if (blank($namaKepsek) && blank($nuptkKepsek)) {
                return;
            }

            $staff = $sekolah->staff; 
            if (! $staff) {
                return;
            }

            $staffUpdate = [];
            if (filled($namaKepsek)) {
                $staffUpdate['nama'] = $namaKepsek;
            }
            if (filled($nuptkKepsek)) {
                $staffUpdate['nuptk'] = $nuptkKepsek; 
            }

            if (! empty($staffUpdate)) {
                $staff->update($staffUpdate);
            }

            if ($staff->relationLoaded('user') ? $staff->user : $staff->user()->exists()) {
                $user = $staff->relationLoaded('user') ? $staff->user : $staff->user()->first();

                if ($user) {
                    $userUpdate = [];

                    if (filled($namaKepsek)) {
                        $userUpdate['name'] = $namaKepsek;
                    }

                    if (filled($nuptkKepsek)) {
                        $userUpdate['username'] = $nuptkKepsek;
                    }

                    if (! empty($userUpdate)) {
                        $user->update($userUpdate);
                    }
                }
            }
        });

        return redirect()->route('akademik.sekolah.index')
            ->with('success', 'Data sekolah berhasil diperbarui.');
    }
}