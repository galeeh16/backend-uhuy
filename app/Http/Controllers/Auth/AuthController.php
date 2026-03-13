<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private readonly UserService $userService) {}

    /**
     * Login
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            $login = $this->userService->login($validated['email'], $validated['password']);
            Log::info('Login Success');
            return response()->json([
                'message' => 'Login berhasil',
                'token'   => $login['token'],
                'user'    => $login['user'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->validator->errors()
            ], 422);
        } catch (Exception $e) {
            $err = $this->formatError($e);
            Log::error('Login Error', ['error' => $err]);
            return $this->responseInternalServerError();
        }
    }

    /**
     * Register
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'unique:users,email'],
            'name' => ['required', 'string', 'min:3', 'max:50'],
            'password' => [Password::required()->min(8)->max(30)->letters()->numbers()->symbols()->mixedCase()],
            'password_confirmation' => ['required', 'same:password'],
            'role' => ['required', 'string', Rule::in(['COMPANY', 'TALENT'])]
        ]);

        DB::beginTransaction();

        try {
            // create user and user profile
            $user = $this->userService->registerUser([
                'email' => $validated['email'],
                'name' => $validated['name'],
                'password' => bcrypt($validated['password']),
                'role' => $validated['role'],
            ]);

            DB::commit();

            Log::info('Register user successfully', [
                'email' => $validated['email']
            ]);

            return response()->json([
                'message' => 'Register user successfully',
                'data' => $user,
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            $err = $this->formatError($e);
            Log::error('Register Uses Failed', ['error' => $err]);
            return $this->responseInternalServerError();
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    public function checkEmail(Request $request): JsonResponse
    {
        $validated = $request->validate(['email' => 'required|email']);

        $exists = $this->userService->isExistsEmail($validated['email']);

        return response()->json([
            'data' => [
                'available' => $exists ? false : true
            ] 
        ], 200);
    }
}
