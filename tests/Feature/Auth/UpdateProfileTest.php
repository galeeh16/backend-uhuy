<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\Concerns\LoginTrait;
use Tests\TestCase;

class UpdateProfileTest extends TestCase
{
    use DatabaseTransactions, LoginTrait;

    public function test_update_profile_but_not_authenticated(): void
    {
        $response = $this->putJson('/api/auth/profile/update', []);

        $response
            ->assertStatus(401)
            ->assertJsonStructure(['message']);
    }

    public function test_update_profile_talent_but_file_greather_than_5mb(): void
    {
        $loginTalent = $this->login([
            'email' => 'galih@example.com',
            'password' => 'Secret123!'
        ]);
        $loginTalent
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'token']);

        $json = $loginTalent->json();

        $response = $this->putJson('/api/auth/profile/update', [
            'location'          => 'Jakarta',
            'full_address'      => 'Jalan Pondok Pinang 6 No 14',
            'phone'             => '08123456789',
            'photo'             => UploadedFile::fake()->image('photo.jpg')->size(6000),
            'cv'                => UploadedFile::fake()->create('cv.pdf', 6000),
            'portfolio'         => UploadedFile::fake()->create('portfolio.pdf', 6000),
            'availability_for_work' => true,
            'birth_date'        => '1996-01-01',
            'experience_year'   => 2,
            'about_me'          => 'My about me'
        ], [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'photo',
                    'cv',
                    'portfolio'
                ]
            ]);
    }
    
    public function test_update_profile_talent_but_wrong_file_extension(): void
    {
        $loginTalent = $this->login([
            'email' => 'galih@example.com',
            'password' => 'Secret123!'
        ]);
        $loginTalent
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'token']);

        $json = $loginTalent->json();

        $response = $this->putJson('/api/auth/profile/update', [
            'location'          => 'Jakarta',
            'full_address'      => 'Jalan Pondok Pinang 6 No 14',
            'phone'             => '08123456789',
            'photo'             => UploadedFile::fake()->image('photo.gif')->size(1000),
            'cv'                => UploadedFile::fake()->create('cv.xlxs', 1000),
            'portfolio'         => UploadedFile::fake()->create('portfolio.xlsx', 1000),
            'availability_for_work' => true,
            'birth_date'        => '1996-01-01',
            'experience_year'   => 2,
            'about_me'          => 'My about me'
        ], [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'photo',
                    'cv',
                    'portfolio'
                ]
            ]);
    }

    public function test_update_profile_talent_success(): void
    {
        $loginTalent = $this->login([
            'email' => 'galih@example.com',
            'password' => 'Secret123!'
        ]);
        $loginTalent
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'token']);

        $json = $loginTalent->json();

        $response = $this->putJson('/api/auth/profile/update', [
            'location'          => 'Jakarta',
            'full_address'      => 'Jalan Pondok Pinang 6 No 14',
            'phone'             => '08123456789',
            'photo'             => UploadedFile::fake()->image('photo.jpg')->size(1000),
            'cv'                => UploadedFile::fake()->create('cv.pdf', 1000),
            'portfolio'         => UploadedFile::fake()->create('portfolio.pdf', 1000),
            'availability_for_work' => true,
            'birth_date'        => '1996-01-01',
            'experience_year'   => 2,
            'about_me'          => 'My about me'
        ], [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function test_update_profile_company_but_photo_greather_than_5mb(): void
    {
        $loginCompany = $this->login([
            'email' => 'kopnus@example.com',
            'password' => 'Secret123!'
        ]);
        $loginCompany
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'token']);

        $json = $loginCompany->json();

        $response = $this->putJson('/api/auth/profile/update', [
            'address'           => 'Jakarta',
            'location'          => 'Jakarta',
            'about_company'     => 'Jakarta',
            'company_size'      => '1000',
            'founded_in'        => '1998-01-02',
            'photo'             => UploadedFile::fake()->image('photo.jpg')->size(6000),
        ], [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'photo',
                ]
            ]);
    }

    public function test_update_profile_talent_work_experiences_but_not_talent(): void 
    {
        $loginCompany = $this->login([
            'email' => 'kopnus@example.com',
            'password' => 'Secret123!'
        ]);
        $loginCompany
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'token']);

        $json = $loginCompany->json();

        $response = $this->putJson('/api/auth/profile/update-work-experiences', [
            'experiences' => [
                ['company' => 'Unit test', 'position' => 'Junior Programmer', 'start_at' => '2018-11-01', 'end_at' => '2019-11-01']
            ]
        ], [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(403)
            ->assertJsonStructure(['message']);
    }

    public function test_update_profile_talent_work_experiences_but_failed_validation(): void 
    {
        $loginTalent = $this->login([
            'email' => 'galih@example.com',
            'password' => 'Secret123!'
        ]);
        $loginTalent
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'token']);

        $json = $loginTalent->json();

        $response = $this->putJson('/api/auth/profile/update-work-experiences', [
            'experiences' => [
                ['company' => 'Unit test', 'position' => 'Junior Programmer', 'start_at' => '2018-11-01', 'end_at' => '2019-11-01'],
                ['company' => 'Unit test'],
            ]
        ], [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_update_profile_talent_work_experiences_success(): void 
    {
        $loginTalent = $this->login([
            'email' => 'galih@example.com',
            'password' => 'Secret123!'
        ]);
        $loginTalent
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'token']);

        $json = $loginTalent->json();

        $response = $this->putJson('/api/auth/profile/update-work-experiences', [
            'experiences' => [
                ['company' => 'Inarts Unit test', 'position' => 'Junior Programmer', 'start_at' => '2018-11-01', 'end_at' => '2019-11-01'],
                ['company' => 'Kopnus Unit test', 'position' => 'FullStack Programmer', 'start_at' => '2018-11-01'],
            ]
        ], [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['message']);
    }

    public function test_update_profile_talent_educations_but_not_talent(): void 
    {
        $loginCompany = $this->login([
            'email' => 'kopnus@example.com',
            'password' => 'Secret123!'
        ]);
        $loginCompany
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'token']);

        $json = $loginCompany->json();

        $response = $this->putJson('/api/auth/profile/update-educations', [
            'educations' => [
                ['degree' => 'SMA', 'institution_name' => 'SMA N 1 Batang Cenaku', 'field_of_study' => 'IPA', 'start_at' => '2018-11-01', 'end_at' => '2019-11-01']
            ]
        ], [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(403)
            ->assertJsonStructure(['message']);
    }

    public function test_update_profile_talent_educations_but_failed_validation(): void 
    {
        $loginTalent = $this->login([
            'email' => 'galih@example.com',
            'password' => 'Secret123!'
        ]);
        $loginTalent
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'token']);

        $json = $loginTalent->json();

        $response = $this->putJson('/api/auth/profile/update-educations', [
            'educations' => [
                ['degree' => 'SMAA', 'institution_name' => 'SMA N 1 Batang Cenaku', 'field_of_study' => 'IPA', 'start_at' => '2018-11-01', 'end_at' => '2019-11-01'],
                ['degree' => 'SMA'],
            ]
        ], [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'message', 
                'errors' => [
                    'educations.0.degree',
                    'educations.1.institution_name',
                    'educations.1.field_of_study',
                    'educations.1.start_at',
                ]
            ]);
    }

    public function test_update_profile_talent_educations_success(): void 
    {
        $loginTalent = $this->login([
            'email' => 'galih@example.com',
            'password' => 'Secret123!'
        ]);
        $loginTalent
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'token']);

        $json = $loginTalent->json();

        $response = $this->putJson('/api/auth/profile/update-educations', [
            'educations' => [
                ['degree' => 'SMA', 'institution_name' => 'SMA N 1 Batang Cenaku', 'field_of_study' => 'IPA', 'start_at' => '2010-03-01', 'end_at' => '2013-03-01'],
                ['degree' => 'S1', 'institution_name' => 'UPN Veteran Yogyakarta', 'field_of_study' => 'Teknik Informatika', 'start_at' => '2013-09-01', 'end_at' => '2018-03-01'],
            ]
        ], [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['message']);
    }
}
