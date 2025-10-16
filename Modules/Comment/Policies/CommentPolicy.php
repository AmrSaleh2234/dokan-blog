<?php

declare(strict_types=1);

namespace Modules\Comment\Policies;

use Modules\Auth\Entities\User;
use Modules\Comment\Entities\Comment;

class CommentPolicy
{
    public function update(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id;
    }
}
