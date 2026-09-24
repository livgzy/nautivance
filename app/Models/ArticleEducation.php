<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleEducation extends Model
{
    protected $primaryKey = 'article_id';
    public $incrementing = false;

    protected $fillable = ['article_id', 'provider', 'deadline', 'funding_amount', 'application_url'];

    protected $casts = [
        'deadline' => 'date',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
