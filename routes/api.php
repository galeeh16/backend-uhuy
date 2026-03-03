<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\VerifiyEmailController;
use App\Http\Controllers\ListPostController;
use Illuminate\Support\Facades\Route;

Route::get('/_debug-middleware', function () {
    return app('router')->getMiddleware();
});

// validasi check email exists
Route::post('/check-email', [AuthController::class, 'checkEmail']);

// Endpoint PROSES Verifikasi (Ini yang diklik user di email)
Route::get('/auth/email/verify/{id}/{hash}', [VerifiyEmailController::class, 'verify'])->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

// Resend Verification Email
Route::post('/auth/email/verification-notification', [VerifiyEmailController::class, 'sendEmailVerification'])->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');

// login
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::get('/auth/me', [ProfileController::class, 'profile'])->middleware('auth:sanctum', 'token.expired');
Route::put('/auth/profile/update', [ProfileController::class, 'update'])->middleware('auth:sanctum', 'token.expired');
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum', 'token.expired');

Route::post('/auth/forgot-password/send-reset-link', [ForgotPasswordController::class, 'sendResetLink'])->middleware('guest');
Route::post('/auth/forgot-password/reset-password', [ForgotPasswordController::class, 'resetPassword'])->middleware('guest');

// Route::middleware(['auth:sanctum', 'abilities:post:create'])->post('/posts', [PostController::class, 'store']);

// Route::middleware(['auth:sanctum', 'abilities:post:update'])
//     ->put('/posts/{post}', ...);

// list posts without any middleware
Route::get('/posts', [ListPostController::class, 'index']);

Route::get('/posts/{id}', [ListPostController::class, 'show'])->whereUuid('id')->middleware('auth:sanctum', 'token.expired');