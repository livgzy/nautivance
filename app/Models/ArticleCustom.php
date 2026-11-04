<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleCustom extends Model
{
    protected $fillable = [
        'article_id', 'name', 'slug', 'custom_fields',
    ];

    protected $casts = [
        'custom_fields' => 'array',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
