<?php

declare(strict_types=1);

namespace Modules\Category\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Post\Entities\Post;
use Nevadskiy\Tree\AsTree;
use Xalaida\LaravelTree\Traits\HasTree;

class Category extends Model
{
    use HasFactory, AsTree;

    protected $fillable = [
        'name',
        'parent_id',
    ];

    protected $casts = [
        'parent_id' => 'integer',
    ];

    /**
     * Get all posts in this category
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get factory instance
     */
    protected static function newFactory()
    {
        return \Modules\Category\Database\Factories\CategoryFactory::new();
    }
}
