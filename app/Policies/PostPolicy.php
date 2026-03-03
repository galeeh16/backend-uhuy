<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * Hanya company pemilik post yang boleh update
     */
    public function update(User $user, Post $post): Response
    {
        return $user->role === 'COMPANY' && $post->company_id === $user->id
            ? Response::allow()
            : Response::deny('You do not own this post.');
    }

    /**
     * Hanya company pemilik post yang boleh delete
     */
    public function delete(User $user, Post $post): Response
    {
        return $user->role === 'COMPANY' && $post->company_id === $user->id
            ? Response::allow()
            : Response::deny('You do not own this post.');
    }
}
