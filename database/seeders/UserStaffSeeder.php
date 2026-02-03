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
            $nuptkGuru = 'gurumapel' . $i;
            $userGuru = User::firstOrCreate([
                'username' => $nuptkGuru,
            ], [
                'name' => 'Guru Mapel ' . $i,
                'email' => $nuptkGuru . '@mail.com',
                'password' => Hash::make('password123'),
            ]);
            $userGuru->assignRole('Guru Mapel');
            Staff::firstOrCreate([
                'user_id' => $userGuru->id
            ], [
                'nuptk' => $nuptkGuru,
                'nama' => $userGuru->name,
                'jenis_kelamin' => $randomGender(),
            ]);

            $nuptkWali = 'walikelas' . $i;
            $userWali = User::firstOrCreate([
                'username' => $nuptkWali,
            ], [
                'name' => 'Wali Kelas ' . $i,
                'email' => $nuptkWali . '@mail.com',
                'password' => Hash::make('password123'),
            ]);
            $userWali->assignRole('Wali Kelas');
            Staff::firstOrCreate([
                'user_id' => $userWali->id
            ], [
                'nuptk' => $nuptkWali,
                'nama' => $userWali->name,
                'jenis_kelamin' => $randomGender(),
            ]);
        }
        for ($i = 1; $i <= 2; $i++) {
            $nuptkAkad = 'akademik' . $i;
            $userAkad = User::firstOrCreate([
                'username' => $nuptkAkad,
            ], [
                'name' => 'Bagian Akademik ' . $i,
                'email' => $nuptkAkad . '@mail.com',
                'password' => Hash::make('password123'),
            ]);
            $userAkad->assignRole('Bagian Akademik');
            Staff::firstOrCreate([
                'user_id' => $userAkad->id
            ], [
                'nuptk' => $nuptkAkad,
                'nama' => $userAkad->name,
                'jenis_kelamin' => $randomGender(),
            ]);
        }
    }
}
