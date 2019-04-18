<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserArticle extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function footprint()
    {
        return $this->hasMany(Footprint::class, 'user_article_id');
    }
}
