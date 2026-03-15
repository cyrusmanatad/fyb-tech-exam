<?php

namespace App\Services;

use App\Models\ForumsComment;

class ForumCommentService
{
    public function create(array $data): ForumsComment
    {
        $comment = ForumsComment::create([
            'user_id' => $data['user_id'],
            'forum_id' => $data['forum_id'],
            'comment' => $data['comment'],
        ]);

        return $comment->load('user');
    }
}