<?php 

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostDetailResource;
use App\Services\PostService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

final class PostController extends Controller
{
    public function __construct(private readonly PostService $postService)
    {
        //
    }

    public function list(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $page = $request->page ? (int) $request->page : 1;
        $perPage = $request->per_page ? (int) $request->per_page : 10;
        $search = $request->search ? $request->search : '';
        $sortBy = $request->sort_by ? $request->sort_by : '';
        $sortOrder = $request->sort_order ? strtolower($request->sort_order) : '';

        $posts = $this->postService->listPostCompany($user->id, $page, $perPage, $search, $sortBy, $sortOrder);

        return response()->json([
            'message' => 'Success',
            'data' => new PostCollection($posts)
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'post_title' => ['required', 'string', 'min:3', 'max:255'],
            'location' => ['required', 'string', 'min:3', 'max:255'],
            'overview' => ['required', 'string', 'min:3', 'max:10000'],
            'responsibilities' => ['required', 'string', 'min:3', 'max:10000'],
            'requirements' => ['required', 'string', 'min:3', 'max:10000'],
            'skills' => ['required', 'string', 'min:3', 'max:10000'],
            'experience_year' => ['required', 'integer', 'gt:0', 'lt:50'],
            'employment_type' => ['required', 'string', Rule::in(['work_from_home', 'full_time', 'remote', 'contract'])],
            'level_type' => ['required', 'string', Rule::in(['junior', 'middle', 'senior', 'head'])],
            'salary' => ['nullable', 'integer', 'gt:0'],
        ]);

        DB::beginTransaction();

        try {
            /** @var \App\Models\User $user */
            $user = $request->user();

            $post = $this->postService->createPost([
                'company_id'        => $user->id,
                'post_title'        => $validated['post_title'],
                'location'          => $validated['location'],
                'overview'          => $validated['overview'],
                'responsibilities'  => $validated['responsibilities'],
                'requirements'      => $validated['requirements'],
                'skills'            => $validated['skills'],
                'experience_year'   => $validated['experience_year'],
                'employment_type'   => $validated['employment_type'],
                'level_type'        => $validated['level_type'],
                'salary'            => $validated['salary'] ?? 0,
            ]);

            Log::info('Create post successfully');
            DB::commit();
            return response()->json([
                'message' => 'Success create post',
                'data' => $post
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            $err = $this->formatError($e);
            Log::error('Failed create post', ['error' => $err]);
            return $this->responseInternalServerError();
        }
    }

    public function show($postId): JsonResponse 
    {
        $post = $this->postService->findById($postId);

        Gate::authorize('update', $post);

        $post->load([
            'company:id,name',
            'company.companyProfile:id,company_id,address,location,about_company,company_size,founded_in,photo,website_url,facebook_url,instagram_url,twitter_url,linked_in_url'
        ]);

        return response()->json([
            'message' => 'Success',
            'data' => new PostDetailResource($post),
        ], 200);   
    }

    public function update(Request $request, $postId): JsonResponse 
    {
        $validated = $request->validate([
            'post_title' => ['required', 'string', 'min:3', 'max:255'],
            'location' => ['required', 'string', 'min:3', 'max:255'],
            'overview' => ['required', 'string', 'min:3', 'max:10000'],
            'responsibilities' => ['required', 'string', 'min:3', 'max:10000'],
            'requirements' => ['required', 'string', 'min:3', 'max:10000'],
            'skills' => ['required', 'string', 'min:3', 'max:10000'],
            'experience_year' => ['required', 'integer', 'gt:0', 'lt:50'],
            'employment_type' => ['required', 'string', Rule::in(['work_from_home', 'full_time', 'remote', 'contract'])],
            'level_type' => ['required', 'string', Rule::in(['junior', 'middle', 'senior', 'head'])],
            'salary' => ['nullable', 'integer', 'gt:0'],
        ]);

        DB::beginTransaction();

        try {
            $post = $this->postService->findById($postId);

            Gate::authorize('update', $post);

            $post = $this->postService->updatePost([
                'post_title'        => $validated['post_title'],
                'location'          => $validated['location'],
                'overview'          => $validated['overview'],
                'responsibilities'  => $validated['responsibilities'],
                'requirements'      => $validated['requirements'],
                'skills'            => $validated['skills'],
                'experience_year'   => $validated['experience_year'],
                'employment_type'   => $validated['employment_type'],
                'level_type'        => $validated['level_type'],
                'salary'            => $validated['salary'] ?? 0,
            ], $postId);

            Log::info('Update post successfully');
            DB::commit();
            return response()->json([
                'message' => 'Success update post',
            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (Exception $e) {
            DB::rollBack();
            $err = $this->formatError($e);
            Log::error('Failed update post', ['error' => $err]);
            return $this->responseInternalServerError();
        }
    }

    public function delete($postId): JsonResponse
    {
        $post = $this->postService->findById($postId);

        Gate::authorize('delete', $post);

        DB::beginTransaction();

        try {
            Gate::authorize('delete', $post);

            $post->delete();

            Log::info('Update delete successfully');
            DB::commit();
            return response()->json([
                'message' => 'Success delete post',
                'data' => null
            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (Exception $e) {
            DB::rollBack();
            $err = $this->formatError($e);
            Log::error('Failed delete post', ['error' => $err]);
            return $this->responseInternalServerError();
        }
    }
}
