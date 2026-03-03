<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $defaultPassword = Hash::make('Secret12345');

        // Create Administrator
        User::insert([
            'id' => (string) Str::uuid(),
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => $defaultPassword,
            'email_verified_at' => $now,
            'role' => 'ADMIN',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        

        // Create user Galih as Applier
        $galihId = User::insertGetId([
            'id' => (string) Str::uuid(),
            'name' => 'Galih Anggoro Jati',
            'email' => 'galih@example.com',
            'password' => $defaultPassword,
            'email_verified_at' => $now,
            'role' => 'TALENT',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Create Galih Profile
        UserProfile::insert([
            'id' => (string) Str::uuid(),
            'user_id' => $galihId,
            'location' => 'Riau',
            'full_address' => 'Bukit Lingkar, RT 10 RW 03',
            'about_me' => 'about me',
            'phone' => '081234567890',
            'experience_year' => 5,
            'created_at' => $now,  
        ]);

        // Create user Ratih as Applier
        $ratihId = User::insertGetId([
            'id' => (string) Str::uuid(),
            'name' => 'Ratih Esti Hapsari',
            'email' => 'ratih@example.com',
            'password' => $defaultPassword,
            'email_verified_at' => $now,
            'role' => 'TALENT',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Create Ratih Profile
        UserProfile::insert([
            'id' => (string) Str::uuid(),
            'user_id' => $ratihId,
            'location' => 'Riau',
            'full_address' => 'Bukit Lingkar, RT 10 RW 03',
            'about_me' => 'about me',
            'phone' => '081234567890',
            'experience_year' => 6,
            'created_at' => $now,  
        ]);


        // Create User KOPNUS as Company
        $kopnus_id = User::insertGetId([
            'id' => (string) Str::uuid(),
            'name' => 'Koperasi Nusantara',
            'email' => 'kopnus@example.com',
            'password' => $defaultPassword,
            'email_verified_at' => $now,
            'role' => 'COMPANY',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Create Profile KOPNUS
        CompanyProfile::insert([
            'id' => (string) Str::uuid(),
            'company_id' => $kopnus_id,
            'address' => 'Jalan Prof Dr Soepomo',
            'location' => 'Jakarta',
            'company_size' => 1100,
            'founded_in' => '2004-01-01',
            'created_at' => $now,
            'updated_at' => $now,
        ]);


        // Create user Bank Mandiri as Company
        $mandiri_id = User::insertGetId([
            'id' => (string) Str::uuid(),
            'name' => 'Bank Mandiri',
            'email' => 'mandiri@example.com',
            'password' => $defaultPassword,
            'email_verified_at' => $now,
            'role' => 'COMPANY',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Create Company Profile Mandiri
        CompanyProfile::insert([
            'id' => (string) Str::uuid(),
            'company_id' => $mandiri_id,
            'address' => 'Jalan Cempaka Putih',
            'location' => 'Jakarta',
            'company_size' => 14000,
            'founded_in' => '1980-01-01',
            'created_at' => $now,
            'updated_at' => $now,
        ]);


        //Create user Sinarmas as Company
        $sinarmas_id = User::insertGetId([
            'id' => (string) Str::uuid(),
            'name' => 'Sinarmas',
            'email' => 'sinarmas@example.com',
            'password' => $defaultPassword,
            'email_verified_at' => $now,
            'role' => 'COMPANY',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Create Company Profile Sinarmas
        CompanyProfile::insert([
            'id' => (string) Str::uuid(),
            'company_id' => $sinarmas_id,
            'address' => 'Jalan Merdeka',
            'location' => 'Jakarta',
            'company_size' => 10000,
            'founded_in' => '2000-01-01',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
