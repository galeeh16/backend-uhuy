<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;

class PostApplySeeder extends Seeder
{
    public function run()
    {
        // 1. Ambil semua Talent (User dengan role TALENT)
        $talents = User::where('role', 'TALENT')->get();
        
        // 2. Ambil semua Lowongan (Post)
        $posts = Post::take(5)->get();

        // 3. Loop melalui setiap Post dan berikan beberapa pelamar acak
        foreach ($posts as $post) {
            // Ambil 2-5 talent secara acak untuk melamar di post ini
            $randomTalents = $talents->random(rand(1,2));

            foreach ($randomTalents as $talent) {
                $post->applicants()->attach($talent->id, [
                    'id' => (string) Str::ulid(), // Tambahkan ini agar ID primary key terisi
                    'status' => fake()->randomElement(['PENDING', 'ON_REVIEW', 'ACCEPTED', 'REJECTED']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
