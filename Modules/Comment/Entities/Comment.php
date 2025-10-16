<?php

declare(strict_types=1);

namespace Modules\Comment\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Entities\User;
use Modules\Post\Entities\Post;
use OwenIt\Auditing\Contracts\Auditable;

class Comment extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'content',
        'post_id',
        'user_id',
    ];

    protected $casts = [
        'post_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory()
    {
        return \Modules\Comment\Database\Factories\CommentFactory::new();
    }
}
