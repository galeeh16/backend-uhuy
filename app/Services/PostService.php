<?php 

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class PostService
{
    public function isExistsPost(string $postId): bool
    {
        return Post::where('id', $postId)->exists();
    }

    public function findById(string $postId): Post 
    {
        $post = Post::where('id', $postId)->first();

        if (!$post) {
            throw new NotFoundException('Post not found');
        }
        
        return $post;
    }

    public function findByIdDetail(string $postId): Post 
    {
        $post = Post::with(['company', 'company.companyProfile'])
            ->where('id', $postId)
            ->first();

        if (!$post) {
            throw new NotFoundException('Post not found');
        }
        
        return $post;
    }

    public function createPost(array $fields): Post 
    {
        return Post::create($fields);
    }

    public function updatePost(array $fields, string $id): int
    {
        return Post::where('id', $id)->update($fields);
    }

    public function listPostCompany(
        string $companyId, 
        int $page, 
        int $perPage, 
        ?string $search, 
        ?string $sortBy = 'created_at', // Default kolom pivot
        ?string $sortOrder = 'desc'
    ): LengthAwarePaginator
    {
        $sortOrder = in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'desc';

        // Mapping key dari frontend ke kolom database jika berbeda
        $sortMap = [
            'created_at' => 'posts.created_at',
            'post_title' => 'posts.post_title',
            'location'   => 'posts.location',
        ];
        
        $sortColumn = isset($sortMap[$sortBy]) ? $sortMap[$sortBy] : 'posts.created_at'; // default set to posts.created_at

        $posts = Post::query()
            ->with([
                'company', 
                'company.companyProfile'
            ])
            ->where('company_id', $companyId)
            ->when($search, function(Builder $query, $value) {
                $searchTerm = '%' . strtolower($value) . '%';

                // Bungkus dalam parameter grouping: WHERE (a OR b)
                $query->where(function($q) use ($searchTerm) {
                    $q
                        ->where(DB::raw("lower(posts.post_title)"), 'like', $searchTerm)
                        ->orWhere(DB::raw("lower(posts.location)"), 'like', $searchTerm)
                        // Gunakan whereHas untuk menembus ke relasi 'company'
                        ->orWhereHas('company', function($qComp) use ($searchTerm) {
                            $qComp->where(DB::raw("lower(name)"), 'like', $searchTerm);
                        });
                });
            })
            ->orderBy($sortColumn, $sortOrder)
            ->paginate(
                page: $page,
                perPage: $perPage
            );

        return $posts;
    }

    public function listPostTalent(int $page, int $perPage, ?string $search=null): LengthAwarePaginator
    {
        $posts = Post::query()
            ->select('id', 'company_id', 'post_title', 'location', 'employment_type', 'level_type', 'created_at')
            ->with([
                'company',
                'company.companyProfile'
            ])
            ->orderByDesc('created_at')
            ->paginate(
                perPage: $perPage,
                page: $page
            );

        return $posts;
    }
}