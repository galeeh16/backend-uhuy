<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserDetailResource;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        if ($user->role === 'TALENT') {
            $user->load('userProfile');
        } elseif ($user->role === 'COMPANY') {
            $user->load('companyProfile');
        }

        return response()->json([
            'message' => 'Success', 
            'data' => new UserDetailResource($user)
        ], 200);
    }

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

}
