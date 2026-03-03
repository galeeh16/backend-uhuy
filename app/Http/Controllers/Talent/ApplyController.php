<?php 

declare(strict_types=1);

namespace App\Http\Controllers\Talent;

use App\Http\Controllers\Controller;
use App\Services\PostApplyService;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ApplyController extends Controller
{
    public function __construct(
        private readonly PostService $postService, 
        private readonly PostApplyService $postApplyService
    ) {}

    public function apply(Request $request, $postId): JsonResponse
    {
        // cek apakah post exists
        $post = $this->postService->findById($postId);

        /** @var \App\Models\User $user */
        $user = $request->user();

        $this->postApplyService->apply($user, $post);

        return response()->json([
            'message' => 'Successfully applied for a job',
        ], 201);
    }
}