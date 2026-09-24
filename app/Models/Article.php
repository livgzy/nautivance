<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'category_id', 'user_id', 'title', 'slug',
        'excerpt', 'content', 'thumbnail', 'status', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function career()
    {
        return $this->hasOne(ArticleCareer::class);
    }

    public function guide()
    {
        return $this->hasOne(ArticleGuide::class);
    }

    public function education()
    {
        return $this->hasOne(ArticleEducation::class);
    }

    public function job()
    {
        return $this->hasOne(ArticleJob::class);
    }

    public function resource()
    {
        return $this->hasOne(ArticleResource::class);
    }

    public function detail(): ?Model
    {
        return match ($this->category?->slug) {
            'career' => $this->career,
            'guide' => $this->guide,
            'education' => $this->education,
            'jobs' => $this->job,
            'resources' => $this->resource,
            default => null,
        };
    }
}
