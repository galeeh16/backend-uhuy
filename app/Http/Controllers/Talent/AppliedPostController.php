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
        // sleep(1);
        /** @var \App\Models\User $user */
        $user = $request->user();
        $page = $request->page ? (int) $request->page : 1;
        $perPage = $request->per_page ? (int) $request->per_page : 10;
        $search = $request->search ? $request->search : '';
        $sortBy = $request->sort_by ? $request->sort_by : '';
        $sortOrder = $request->sort_order ? strtolower($request->sort_order) : '';

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
