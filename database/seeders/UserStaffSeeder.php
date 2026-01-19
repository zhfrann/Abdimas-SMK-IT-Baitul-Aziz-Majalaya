<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Staff;
use Spatie\Permission\Models\Role;

class UserStaffSeeder extends Seeder
{
    public function run(): void
    {
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
            Staff::firstOrCreate([
                'user_id' => $userAkad->id
            ], [
                'nip' => $nipAkad,
                'nama' => $userAkad->name,
                'jenis_kelamin' => $randomGender(),
            ]);
        }
    }
}
