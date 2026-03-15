<?php

namespace App\Services;

use App\Models\Forum;
use Illuminate\Support\Facades\Auth;

class ForumService
{
    public function create(array $data): Forum
    {
        $forum = Forum::create([
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'content' => $data['content'],
        ]);

        return $forum->load('user');
    }
}