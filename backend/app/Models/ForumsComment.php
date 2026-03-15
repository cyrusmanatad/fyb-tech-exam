<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumsComment extends Model
{

    protected $fillable = ['user_id', 'forum_id', 'comment'];

    protected $appends = ['humanize_datetime'];

    public function getHumanizeDatetimeAttribute(){
        return $this->created_at ? $this->created_at->diffForHumans() : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function forum(): BelongsTo
    {
        return $this->belongsTo(Forum::class);
    }
}
