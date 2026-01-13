<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Models\KelasAjar;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DemoAkademikSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Roles
            $roles = ['Guru Mapel', 'Wali Kelas', 'Bagian Akademik'];
            foreach ($roles as $role) {
                Role::firstOrCreate(['name' => $role]);
            }

            // Helper untuk random gender
            $randomGender = function () {
                return rand(0, 1) ? 'L' : 'P';
            };

            // Users & Staff
            $guruMapel = [];
            $waliKelas = [];
            $bagianAkademik = [];
            for ($i = 1; $i <= 3; $i++) {
                $nipGuru = 'gurumapel' . $i;
                $userGuru = User::firstOrCreate([
                    'username' => $nipGuru,
                ], [
                    'name' => 'Guru Mapel ' . $i,
                    'email' => $nipGuru . '@mail.com',
                    'password' => Hash::make('password123'),
                ]);
                $userGuru->assignRole('Guru Mapel');
                $guruMapel[] = $userGuru;
                Staff::firstOrCreate([
                    'user_id' => $userGuru->id
                ], [
                    'nip' => $nipGuru,
                    'nama' => $userGuru->name,
                    'jenis_kelamin' => $randomGender(),
                ]);

                $nipWali = 'walikelas' . $i;
                $userWali = User::firstOrCreate([
                    'username' => $nipWali,
                ], [
                    'name' => 'Wali Kelas ' . $i,
                    'email' => $nipWali . '@mail.com',
                    'password' => Hash::make('password123'),
                ]);
                $userWali->assignRole('Wali Kelas');
                $waliKelas[] = $userWali;
                Staff::firstOrCreate([
                    'user_id' => $userWali->id
                ], [
                    'nip' => $nipWali,
                    'nama' => $userWali->name,
                    'jenis_kelamin' => $randomGender(),
                ]);
            }
            for ($i = 1; $i <= 2; $i++) {
                $nipAkad = 'akademik' . $i;
                $userAkad = User::firstOrCreate([
                    'username' => $nipAkad,
                ], [
                    'name' => 'Bagian Akademik ' . $i,
                    'email' => $nipAkad . '@mail.com',
                    'password' => Hash::make('password123'),
                ]);
                $userAkad->assignRole('Bagian Akademik');
                $bagianAkademik[] = $userAkad;
                Staff::firstOrCreate([
                    'user_id' => $userAkad->id
                ], [
                    'nip' => $nipAkad,
                    'nama' => $userAkad->name,
                    'jenis_kelamin' => $randomGender(),
                ]);
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
        });
    }
}
