<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\Concerns\LoginTrait;
use Tests\TestCase;

class UpdateProfileTest extends TestCase
{
    use DatabaseTransactions, LoginTrait;

    public function test_update_profile_without_login(): void
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
            'password' => 'Secret12345'
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
            'password' => 'Secret12345'
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
            'password' => 'Secret12345'
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
            'password' => 'Secret12345'
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
}
