<?php 

declare(strict_types=1);

namespace App\Http\Controllers\Talent;

use App\Http\Controllers\Controller;
use App\Http\Resources\TalentAppliedPostResource;
use App\Services\PostApplyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AppliedPostController extends Controller
{
    public function __construct(
        private readonly PostApplyService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $page = $request->query('page') ? (int) $request->query('page') : 1;
        $perPage = $request->query('per_page') ? (int) $request->query('per_page') : 10;
        $search = $request->query('search') ? $request->query('search') : '';
        $sortBy = $request->query('sort_by') ? $request->query('sort_by') : '';
        $sortOrder = $request->query('sort_order') ? strtolower($request->query('sort_order')) : '';

        $posts = $this->service->listAppliedPosts($user, $page, $perPage, $search, $sortBy, $sortOrder);

        return response()->json([
            'message' => 'Success',
            'data' => TalentAppliedPostResource::collection($posts->items()),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'per_page'     => $posts->perPage(),
                'total'        => $posts->total(),
                'last_page'    => $posts->lastPage(),
            ]
        ]);
    }
}
