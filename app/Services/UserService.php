<?php 

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

class UserService
{
    public function login(string $email, #[SensitiveParameter] string $password): array
    {
        // cek kredensial
        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            throw ValidationException::withMessages([
                'email' => ['Email or password are invalid.'],
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        // (opsional) hapus token lama → single device login
        $user->tokens()->delete();
        $abilities = $this->forRole($user->role);

        // buat token
        $token = $user->createToken('api-token', $abilities, now()->addDays(7))->plainTextToken;

        return [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'role' => $user->role
            ]
        ];
    }

    private function forRole(string $role): array
    {
        return match ($role) {
            'ADMIN' => ['*'],

            'COMPANY' => [
                'post:create',
                'post:update',
                'post:delete',
                'post:view',
                'apply:view',
                'apply:update-status',
            ],

            'TALENT' => [
                'post:view',
                'apply:create',
                'apply:view-status',
            ],

            default => [],
        };
    }

    public function registerUser(array $field): User
    {
        /** @var User $user */
        $user =  User::create($field);

        if ($user->role === 'TALENT') {
            $user->userProfile()->create([
                'id' => (string) Str::uuid()
            ]);
        } elseif ($user->role === 'COMPANY') {
            $user->companyProfile()->create([
                'id' => (string) Str::uuid()
            ]);
        }

        // notif ke user untuk verifikasi email
        $user->sendEmailVerificationNotification();

        return $user;
    }

    // update user profile
    public function updateUserProfile(array $validated, User $user): void
    {
        /** @var Storage $storage */
        $storage = Storage::disk('public');

        /** @var \App\Models\UserProfile $profile */
        $profile = $user->userProfile;

        $newDirectory = date('Y/m');
        $baseDir = 'talents/' . $newDirectory;

        $dataUpdate = []; // field-field update
        $newFiles = [];   // simpan file baru
        $oldFiles = [];   // simpan file lama untuk dihapus setelah sukses

        try {
            // upload file terlebih dahulu, tapi jangan hapus file lama
            if (!empty($validated['photo'])) {
                /** @var UploadedFile $file */
                $file = $validated['photo'];

                $newName = 'photo' . '_user_' . $user->id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                $path = $storage->putFileAs($baseDir, $file, $newName);

                $newFiles['photo'] = $path;
                $dataUpdate['photo'] = $path;

                // simpan file lama (jangan hapus dulu)
                if ($profile->photo) {
                    $oldFiles[] = $profile->photo;
                }
            }

            // upload cv
            if (!empty($validated['cv'])) {
                /** @var UploadedFile $file */
                $file = $validated['cv'];

                $newName = 'cv' . '_user_' . $user->id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                $path = $storage->putFileAs($baseDir, $file, $newName);

                $newFiles['cv'] = $path;
                $dataUpdate['cv'] = $path;

                // simpan file lama (jangan hapus dulu)
                if ($profile->cv) {
                    $oldFiles[] = $profile->cv;
                }
            }

            // upload portfolio
            if (!empty($validated['portfolio'])) {
                /** @var UploadedFile $file */
                $file = $validated['portfolio'];

                $newName = 'portfolio' . '_user_' . $user->id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                $path = $storage->putFileAs($baseDir, $file, $newName);

                $newFiles['portfolio'] = $path;
                $dataUpdate['portfolio'] = $path;

                // simpan file lama (jangan hapus dulu)
                if ($profile->portfolio) {
                    $oldFiles[] = $profile->portfolio;
                }
            }

            // field-field non file
            $allowedFields = [
                'location',
                'full_address',
                'about_me',
                'phone',
                'birth_date',
                'experience_year',
                'availability_for_work',
            ];

            $dataUpdate = array_merge(
                $dataUpdate,
                array_intersect_key($validated, array_flip($allowedFields))
            );

            // db update
            DB::transaction(function () use ($user, $dataUpdate) {
                $user->userProfile()->updateOrCreate(
                    ['user_id' => $user->id],
                    $dataUpdate
                );
            });
            
            // hapus file lama, setelah db update sukses
            foreach ($oldFiles as $oldFile) {
                try {
                    if ($storage->exists($oldFile)) {
                        $storage->delete($oldFile);
                    }
                } catch (\Throwable $e) {
                    Log::warning('Failed deleting old file', [
                        'file' => $oldFile,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // rollback file baru jika gagal
            foreach ($newFiles as $file) {
                if ($storage->exists($file)) {
                    $storage->delete($file);
                }
            }

            throw $e; // biarkan controller handle
        }
    }

    // update company profile
    public function updateCompanyProfile(array $validated, User $user) 
    {
        /** @var Storage $storage */
        $storage = Storage::disk('public');

        /** @var \App\Models\CompanyProfile $profile */
        $profile = $user->companyProfile;

        $newDirectory = date('Y/m');
        $baseDir = 'companies/' . $newDirectory;

        $dataUpdate = []; // field-field update
        $newFiles = [];   // simpan file baru
        $oldFiles = [];   // simpan file lama untuk dihapus setelah sukses

        try {
            // upload file terlebih dahulu, tapi jangan hapus file lama
            if (!empty($validated['photo'])) {
                /** @var UploadedFile $file */
                $file = $validated['photo'];
                $newName = 'photo' . '_user_' . $user->id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $storage->putFileAs($baseDir, $file, $newName);

                $newFiles['photo'] = $path;
                $dataUpdate['photo'] = $path;

                // simpan file lama (jangan hapus dulu)
                if ($profile->photo) {
                    $oldFiles[] = $profile->photo;
                }
            }

            // upload cv
            if (!empty($validated['cv'])) {
                /** @var UploadedFile $file */
                $file = $validated['cv'];
                $newName = 'cv' . '_user_' . $user->id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $storage->putFileAs($baseDir, $file, $newName);

                $newFiles['cv'] = $path;
                $dataUpdate['cv'] = $path;

                // simpan file lama (jangan hapus dulu)
                if ($profile->cv) {
                    $oldFiles[] = $profile->cv;
                }
            }

            // upload portfolio
            if (!empty($validated['portfolio'])) {
                /** @var UploadedFile $file */
                $file = $validated['portfolio'];
                $newName = 'portfolio' . '_user_' . $user->id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $storage->putFileAs($baseDir, $file, $newName);

                $newFiles['portfolio'] = $path;
                $dataUpdate['portfolio'] = $path;

                // simpan file lama (jangan hapus dulu)
                if ($profile->portfolio) {
                    $oldFiles[] = $profile->portfolio;
                }
            }

            // field-field non file
            $allowedFields = [
                'address',
                'location',
                'about_company',
                'company_size',
                'founded_in',
                'photo',
                'website_url',
                'facebook_url',
                'instagram_url',
                'twitter_url',
                'linked_in_url',
            ];

            $dataUpdate = array_merge(
                $dataUpdate,
                array_intersect_key($validated, array_flip($allowedFields))
            );

            // db update
            DB::transaction(function () use ($user, $dataUpdate) {
                $user->companyProfile()->updateOrCreate(
                    ['company_id' => $user->id],
                    $dataUpdate
                );
            });
            
            // hapus file lama, setelah db update sukses
            foreach ($oldFiles as $oldFile) {
                try {
                    if ($storage->exists($oldFile)) {
                        $storage->delete($oldFile);
                    }
                } catch (\Throwable $e) {
                    Log::warning('Failed deleting old file', [
                        'file' => $oldFile,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // rollback file baru jika gagal
            foreach ($newFiles as $file) {
                if ($storage->exists($file)) {
                    $storage->delete($file);
                }
            }

            throw $e; // biarkan controller handle
        }
    }

    public function isExistsEmail(string $email): bool 
    {
        return User::where('email', $email)->exists();
    }
    
}