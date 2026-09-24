<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleGuide extends Model
{
    protected $primaryKey = 'article_id';
    public $incrementing = false;

    protected $fillable = ['article_id', 'certificate_code', 'applicable_rank'];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
