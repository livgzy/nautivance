<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleCareer extends Model
{
    protected $primaryKey = 'article_id';
    public $incrementing = false;

    protected $fillable = ['article_id', 'career_stage', 'topic'];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
