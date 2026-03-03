<?php

use App\Http\Controllers\Company\CandidateController;
use App\Http\Controllers\Company\PostController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'token.expired', 'role:COMPANY'])
->prefix('company')
->group(function() {
    // Posts
    // list posts
    Route::get('/posts', [PostController::class, 'list'])->middleware('abilities:post:view');  
    // view post  
    Route::get('/posts/{postId}', [PostController::class, 'show'])->middleware('abilities:post:view');   
    // add post 
    Route::post('/posts', [PostController::class, 'store'])->middleware('abilities:post:create');   
    // update post
    Route::put('/posts/{postId}', [PostController::class, 'update'])->middleware('abilities:post:update');
    // delete post
    Route::delete('/posts/{postId}', [PostController::class, 'delete'])->middleware('abilities:post:delete');

    // Candidates
    // list all candidates who apply post
    Route::get('/candidates', [CandidateController::class, 'list'])->middleware('abilities:post:view');
    // view candidate who apply post by id
    Route::get('/candidates/{candidateId}', [CandidateController::class, 'show'])->middleware('abilities:post:view');
});