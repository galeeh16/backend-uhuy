<?php 

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Services\PostApplyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CandidateController extends Controller
{
    public function __construct(private readonly PostApplyService $postApplyService) {}

    public function list(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $page = $request->page ? (int) $request->page : 1;
        $perPage = $request->per_page ? (int) $request->per_page : 10;
        $search = $request->search ? $request->search : '';
        $sortBy = $request->sort_by ? $request->sort_by : '';
        $sortOrder = $request->sort_order ? strtolower($request->sort_order) : '';

        $candidates = $this->postApplyService->listAllCandidates($user->id, $page, $perPage, $search, $sortBy, $sortOrder);

        return response()->json([
            'message' => 'Success',
            'data' => $candidates->items(),
            'meta' => [
                'current_page' => $candidates->currentPage(),
                'per_page'     => $candidates->perPage(),
                'total'        => $candidates->total(),
                'last_page'    => $candidates->lastPage(),
            ],
        ], 200);
    }

    public function show(Request $request, $postApplyId): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $candidate = $this->postApplyService->findPostApplyByID($user, $postApplyId);

        return response()->json([
            'message' => 'Success',
            'data' => $candidate,
        ], 200);
    }
}