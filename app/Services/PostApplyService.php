<?php 

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\AlreadyAppliedException;
use App\Exceptions\ForbiddenException;
use App\Models\Post;
use App\Models\PostApply;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostApplyService
{
    public function apply(User $user, Post $post): void
    {
        // validasi role
        if ($user->role !== 'TALENT') {
            throw new ForbiddenException('Only talent can apply for jobs');
        }

        try {
            $user->appliedPosts()->attach($post->id, [
                'id' => (string) Str::ulid(), // WAJIB: Karena Primary Key post_applies adalah ULID
                'status' => 'PENDING',
            ]);

            // optional: update counter
            $post->increment('total_applied');

        } catch (QueryException $e) {
            // duplicate key violation (PostgreSQL & MySQL)
            if ($this->isDuplicateApply($e)) {
                throw new AlreadyAppliedException();
            }

            throw $e;
        }
    }

    private function isDuplicateApply(QueryException $e): bool
    {
        return str_contains($e->getMessage(), 'unique')
            || str_contains($e->getMessage(), 'Duplicate');
    }

    public function listAppliedPosts(
        User $user, 
        int $page, 
        int $perPage, 
        ?string $search,
        ?string $sortBy = 'created_at', // Default kolom sort
        ?string $sortOrder = 'desc'
    ): LengthAwarePaginator
    {
        $sortOrder = in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'desc';

        // Mapping key dari frontend ke kolom database jika berbeda
        $sortMap = [
            'applied_at' => 'post_applies.created_at',
            'post_title' => 'posts.post_title',
            'status'     => 'post_applies.status'
        ];
        
        $sortColumn = isset($sortMap[$sortBy]) ? $sortMap[$sortBy] : 'post_applies.created_at';

        return $user->appliedPosts()
            ->with([
                'company:id,name,email',
                'company.companyProfile:id,company_id,photo'
            ])
            ->when($search, function(Builder $query, $value) {
                $searchTerm = '%' . strtolower($value) . '%';

                // Bungkus dalam parameter grouping: WHERE (a OR b)
                $query->where(function($q) use ($searchTerm) {
                    $q->where(DB::raw("lower(posts.post_title)"), 'like', $searchTerm)
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
    }
    
    public function listAllCandidates(
        string $companyId, 
        int $page, 
        int $perPage, 
        ?string $search,
        ?string $sortBy = 'applied_at',
        ?string $sortOrder = 'desc'
    ): LengthAwarePaginator
    {
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'desc';

        $sortMap = [
            'applied_at' => 'pa.created_at',
            'post_title' => 'p.post_title',
            'status'     => 'pa.status',
            'candidate_name' => 'u.name'
        ];
        
        $sortColumn = $sortMap[$sortBy] ?? 'pa.created_at';

        return DB::table('post_applies as pa')
            ->join('users as u', 'pa.user_id', '=', 'u.id')
            ->join('posts as p', 'pa.post_id', '=', 'p.id')
            ->leftJoin('user_profile as up', 'u.id', '=', 'up.user_id')
            ->select([
                'pa.id',
                'u.id as candidate_id',
                'u.name as candidate_name',
                'u.email as candidate_email',
                'up.photo as candidate_photo',
                'p.post_title',
                'pa.status',
                'pa.created_at as applied_at',
                'pa.updated_at',
            ])
            ->where('p.company_id', $companyId)
            // Handle Search
            ->when($search, function($query, $value) {
                $searchTerm = '%' . strtolower($value) . '%';
                
                $query->where(function($q) use ($searchTerm) {
                    $q->where(DB::raw('LOWER(u.name)'), 'like', $searchTerm)
                    ->orWhere(DB::raw('LOWER(p.post_title)'), 'like', $searchTerm);
                });
            })
            ->orderBy($sortColumn, $sortOrder)
            ->paginate(perPage: $perPage, page: $page);
    }

    public function findPostApplyByID(User $company, string $postApplyId)
    {
        return PostApply::with([
            'user.userProfile', 
            'post'
        ])
        ->where('id', $postApplyId)
        // Opsional: Pastikan hanya company pemilik post yang bisa melihat
        ->whereHas('post', function($q) use ($company) {
            $q->where('company_id', $company->id);
        })
        ->firstOrFail();
    }
}
