<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\UserDegree;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserDetailResource;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct(private readonly UserService $userService) {}

    /**
     * Show profile
     */
    public function profile(Request $request): JsonResponse 
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $cacheKey = "user_me_{$user->id}";

        $userResponse = Cache::remember($cacheKey, now()->addMinute(60), function() use ($user) {
            if ($user->role === 'TALENT') {
                $user->load('userProfile');
            } elseif ($user->role === 'COMPANY') {
                $user->load('companyProfile');
            }

            $userDetailResource = new UserDetailResource($user);
            return $userDetailResource;
        });

        return response()->json([
            'message' => 'Success', 
            'data' => $userResponse
        ], 200);
    }

    /**
     * Update personal information
     *
     * @param UpdateProfileRequest $request
     * @return JsonResponse
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $validated = $request->validated();

        match ($user->role) {
            'TALENT'  => $this->userService->updateUserProfile($validated, $user),
            'COMPANY' => $this->userService->updateCompanyProfile($validated, $user),
            default   => throw new Exception('Invalid Role'),
        };

        return response()->json([
            'message' => 'Success update profile',
        ], 200);
    }

    /**
     * Update talent work experiences
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateWorkExperiences(Request $request): JsonResponse 
    {
        $validated = $request->validate([
            'experiences' => ['required', 'array'],
            'experiences.*.id' => ['nullable'],
            'experiences.*.company' => ['required', 'string', 'min:3', 'max:255'],
            'experiences.*.position' => ['required', 'string', 'min:3', 'max:255'],
            'experiences.*.start_at' => ['required', 'date_format:Y-m-d'],
            'experiences.*.end_at' => ['nullable', 'date_format:Y-m-d'],
            'experiences.*.description' => ['nullable', 'string', 'min:3', 'max:500'],
        ]);

        DB::beginTransaction();

        try {
            /** @var \App\Models\User $user */
            $user = $request->user();

            $this->userService->updateWorkExperiences($user, $validated['experiences']);

            DB::commit();
            return response()->json([
                'message' => 'Success update work experiences'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            $err = $this->formatError($e);
            Log::error('Update User Work Experiences Error', ['error' => $err]);
            return $this->responseInternalServerError();
        }
    }

    public function updateEducations(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'educations' => ['required', 'array'],
            'educations.*.degree' => ['required', 'string', Rule::in(UserDegree::cases())],
            'educations.*.institution_name' => ['required', 'string', 'max:255'],
            'educations.*.field_of_study' => ['required', 'string', 'max:255'],
            'educations.*.start_at' => ['required', 'date_format:Y-m-d'],
            'educations.*.end_at' => ['nullable', 'date_format:Y-m-d'],
        ]);

        DB::beginTransaction();

        try {
            /** @var \App\Models\User $user */
            $user = $request->user();

            $this->userService->updateEducations($user, $validated['educations']);
            DB::commit();
            return response()->json([
                'message' => 'Success update educations'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            $err = $this->formatError($e);
            Log::error('Update User Educations Error', ['error' => $err]);
            return $this->responseInternalServerError();
        }
    }

}
