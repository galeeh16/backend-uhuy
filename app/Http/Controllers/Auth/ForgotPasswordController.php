<?php 

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordValidator;
use Illuminate\Support\Str;

final class ForgotPasswordController extends Controller
{
    /**
     * Send reset link
     */
    public function sendResetLink(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
 
        $status = Password::sendResetLink($request->only('email'));
    
        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Link reset password telah dikirim ke email.'], 200)
            : response()->json(['message' => 'Gagal mengirim email.'], 400);
    }

    /**
     * Forgot password
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string', 'max:1000'],
            'email' => ['required', 'email'],
            'password' => ['required', PasswordValidator::min(8)->max(30)->letters()->numbers()->symbols()->mixedCase()],
            'password_confirmation' => ['required', 'string', 'same:password']
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password berhasil diperbarui.'], 200)
            : response()->json(['message' => 'Token atau email tidak valid.'], 400);
    }
}