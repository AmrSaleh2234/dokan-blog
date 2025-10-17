<?php

declare(strict_types=1);

namespace Modules\Category\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Post\Entities\Post;
use Nevadskiy\Tree\AsTree;
use Nevadskiy\Tree\Relations\HasManyDeep;
use OwenIt\Auditing\Contracts\Auditable;
use Xalaida\LaravelTree\Traits\HasTree;

class Category extends Model implements Auditable
{
    use HasFactory, AsTree, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'parent_id',
    ];

    protected $casts = [
        'parent_id' => 'integer',
    ];

    /**
     * Get all posts in this category (direct children only)
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get all posts in this category and its descendants
     */
    public function postsWithDescendants(): HasManyDeep
    {
        return HasManyDeep::between($this, Post::class);
    }

    /**
     * Get factory instance
     */
    protected static function newFactory()
    {
        return \Modules\Category\Database\Factories\CategoryFactory::new();
    }
}
