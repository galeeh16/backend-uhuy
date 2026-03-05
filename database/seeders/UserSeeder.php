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
        $defaultPassword = Hash::make('Secret123!');

        // Create Administrator
        User::insert([
            'id' => (string) Str::ulid(),
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => $defaultPassword,
            'email_verified_at' => $now,
            'role' => 'ADMIN',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->createTalent();

        $this->createCompany();
    }

    private function createTalent(): void 
    {
        // Create user Galih as Talent
        $galih = User::factory()->talent()->create([
            'name' => 'Galih Anggoro Jati',
            'email' => 'galih@example.com'
        ]);

        // Create Galih Profile
        UserProfile::factory()->for($galih, 'user')->create();

        // Create user Ratih as Talent
        $ratih = User::factory()->talent()->create([
            'name' => 'Ratih Esti Hapsari',
            'email' => 'ratih@example.com'
        ]);

        // Create Ratih Profile
        UserProfile::factory()->for($ratih, 'user')->create();
    }

    private function createCompany(): void 
    {
        // Create User KOPNUS as Company
        $kopnus = User::factory()->company()->create([
            'name' => 'Koperasi Nusantara',
            'email' => 'kopnus@example.com'
        ]);
        // Create Kopnus Profile
        CompanyProfile::factory()->for($kopnus, 'company')->create();

        // Create User Mandiri as Company
        $mandiri = User::factory()->company()->create([
            'name' => 'Bank Mandiri',
            'email' => 'mandiri@example.com'
        ]);
        // Create Mandiri Profile
        CompanyProfile::factory()->for($mandiri, 'company')->create();

        // Create User Sinarmas as Company
        $sinarmas = User::factory()->company()->create([
            'name' => 'Sinarmas',
            'email' => 'sinarmas@example.com'
        ]);
        // Create Sinarmas Profile
        CompanyProfile::factory()->for($sinarmas, 'company')->create();

        // Create User Astra as Company
        $astra = User::factory()->company()->create([
            'name' => 'Astra International',
            'email' => 'astra@example.com'
        ]);
        // Create Sinarmas Profile
        CompanyProfile::factory()->for($astra, 'company')->create();

        // Create User Microsoft as Company
        $microsoft = User::factory()->company()->create([
            'name' => 'Microsoft',
            'email' => 'microsoft@example.com'
        ]);
        // Create Sinarmas Profile
        CompanyProfile::factory()->for($microsoft, 'company')->create();

        // Create User Bank BRI as Company
        $bri = User::factory()->company()->create([
            'name' => 'Bank BRI',
            'email' => 'bri@example.com'
        ]);
        // Create Bank BRI Profile
        CompanyProfile::factory()->for($bri, 'company')->create();
    }
}
