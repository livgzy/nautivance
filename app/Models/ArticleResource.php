<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleResource extends Model
{
    protected $primaryKey = 'article_id';
    public $incrementing = false;

    protected $fillable = ['article_id', 'file_path', 'file_type', 'file_size', 'download_count'];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
