<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Models\KelasAjar;
use Spatie\Permission\Models\Role;

class DemoAkademikSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $roles = ['Guru Mapel', 'Wali Kelas', 'Bagian Akademik'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Users
        $guruMapel = [];
        $waliKelas = [];
        $bagianAkademik = [];
        for ($i = 1; $i <= 3; $i++) {
            $guruMapel[] = User::firstOrCreate([
                'username' => 'gurumapel' . $i,
            ], [
                'name' => 'Guru Mapel ' . $i,
                'email' => 'gurumapel' . $i . '@mail.com',
                'password' => Hash::make('password123'),
            ]);
            $guruMapel[$i - 1]->assignRole('Guru Mapel');

            $waliKelas[] = User::firstOrCreate([
                'username' => 'walikelas' . $i,
            ], [
                'name' => 'Wali Kelas ' . $i,
                'email' => 'walikelas' . $i . '@mail.com',
                'password' => Hash::make('password123'),
            ]);
            $waliKelas[$i - 1]->assignRole('Wali Kelas');
        }
        for ($i = 1; $i <= 2; $i++) {
            $bagianAkademik[] = User::firstOrCreate([
                'username' => 'akademik' . $i,
            ], [
                'name' => 'Bagian Akademik ' . $i,
                'email' => 'akademik' . $i . '@mail.com',
                'password' => Hash::make('password123'),
            ]);
            $bagianAkademik[$i - 1]->assignRole('Bagian Akademik');
        }

        // Tahun Ajaran
        $tahunAjaranList = [
            ['tahun' => '2023/2024', 'semester' => 'Ganjil'],
            ['tahun' => '2023/2024', 'semester' => 'Genap'],
            ['tahun' => '2024/2025', 'semester' => 'Ganjil'],
            ['tahun' => '2024/2025', 'semester' => 'Genap'],
            ['tahun' => '2025/2026', 'semester' => 'Ganjil'],
            ['tahun' => '2025/2026', 'semester' => 'Genap'],
        ];
        $tahunAjaranModels = [];
        foreach ($tahunAjaranList as $ta) {
            $tahunAjaranModels[] = TahunAjaran::firstOrCreate($ta);
        }

        // Kelas dan Kelas Ajar
        $kelasList = ['X RPL 1', 'X RPL 2', 'X TKJ 1', 'X TKJ 2'];
        foreach ($tahunAjaranModels as $ta) {
            foreach ($kelasList as $idx => $namaKelas) {
                $kelas = Kelas::firstOrCreate(['nama_kelas' => $namaKelas]);
                // Rotasi wali kelas
                $wali = $waliKelas[$idx % count($waliKelas)];
                KelasAjar::firstOrCreate([
                    'kelas_id' => $kelas->kelas_id,
                    'tahun_ajaran_id' => $ta->tahun_ajaran_id,
                ], [
                    'wali_user_id' => $wali->id,
                ]);
            }
        }
    }
}
