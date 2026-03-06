<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostDetailResource;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListPostController extends Controller
{
    public function __construct(private readonly PostService $postService) {}

    /** 
     * List postingan job
     */
    public function index(Request $request): JsonResponse
    {
        $page = $request->query('page') ? (int) $request->query('page') : 1;
        $perPage = $request->query('per_page') ? (int) $request->query('per_page') : 10;

        $posts = $this->postService->listPostTalent($page, $perPage);

        return response()->json([
            'message' => 'Success',
            'data' => new PostCollection($posts)
        ], 200);
    }

    /**
     * Detail postingan jobs
     */
    public function show($id): JsonResponse
    {
        $post = $this->postService->findByIdDetail($id);

        return response()->json([
            'message' => 'Success',
            'data' => new PostDetailResource($post)
        ], 200);
    }
}