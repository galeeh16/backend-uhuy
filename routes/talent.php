<?php

use App\Http\Controllers\Talent\AppliedPostController;
use App\Http\Controllers\Talent\ApplyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'token.expired', 'role:TALENT'])
->prefix('talent')
->group(function() {
    // Talent apply job
    Route::middleware(['abilities:apply:create'])->post('/posts/{postId}/apply', [ApplyController::class, 'apply']);
    // List talent applied posts
    Route::middleware(['abilities:apply:view-status'])->get('/applied/posts', [AppliedPostController::class, 'index']);
});

// // Talent lihat status
// Route::middleware(['auth:sanctum', 'abilities:apply:view-status'])->get('/my-applications', ...);

// // Company lihat applicant
// Route::middleware(['auth:sanctum', 'abilities:apply:view'])->get('/posts/{post}/applications', ...);

// // Company update status
// Route::middleware(['auth:sanctum', 'abilities:apply:update-status'])->patch('/applications/{id}/status', ...);