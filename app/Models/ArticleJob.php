<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleJob extends Model
{
    protected $primaryKey = 'article_id';
    public $incrementing = false;

    protected $fillable = [
        'article_id', 'company_name', 'location', 'apply_url',
        'salary_range', 'job_type', 'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'date',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
