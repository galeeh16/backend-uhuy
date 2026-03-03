<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerifiyEmailController extends Controller
{
    /**
     * Send email verification
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendEmailVerification(Request $request): JsonResponse
    {
        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent!'], 200);
    }

    /**
     * Proses verifikasi email
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verify(Request $request): JsonResponse
    {
        // 1. Validasi Signature (Sangat Penting untuk Keamanan)
        // Ini memastikan URL tidak dimanipulasi meskipun tanpa login
        if (! $request->hasValidSignature()) {
            return response()->json(['message' => 'Link verifikasi tidak valid atau sudah kadaluwarsa.'], 403);
        }

        // 2. Cari User berdasarkan ID dari URL
        $user = User::findOrFail($request->route('id'));

        // 3. Validasi Hash Email
        // Memastikan hash email di URL cocok dengan email user di database
        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Hash email tidak cocok.'], 403);
        }

        // 4. Cek apakah sudah pernah diverifikasi
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email sudah diverifikasi sebelumnya.']);
        }

        // 5. Proses Verifikasi (Pengganti fulfill())
        if ($user->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        return response()->json([
            'message' => 'Email berhasil diverifikasi!',
        ], 200);
    }
}
