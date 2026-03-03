<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PostApplySeeder extends Seeder
{
    public function run()
    {
        $posts = Post::pluck('id');
        $talents = User::where('role', 'TALENT')->pluck('id');

        if ($posts->isEmpty() || $talents->isEmpty()) {
            $this->command->warn('PostApplySeeder skipped: post or talent empty');
            return;
        }

        foreach ($posts as $postId) {
            // jumlah pelamar per post
            $applyCount = rand(1, min(10, $talents->count()));

            $applicants = $talents->random($applyCount);

            foreach ($applicants as $userId) {
                DB::table('post_applies')->updateOrInsert(
                    [
                        'post_id' => $postId,
                        'user_id' => $userId,
                    ],
                    [
                        'status' => collect([
                            'PENDING',
                            'ON_REVIEW',
                            'ACCEPTED',
                            'REJECTED',
                        ])->random(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            // update counter cache
            DB::table('posts')
                ->where('id', $postId)
                ->update([
                    'total_applied' => $applyCount,
                ]);
        }
    }
}
