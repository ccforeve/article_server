<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function userArticle()
    {
        return $this->hasMany(UserArticle::class);
    }

    public function footprint()
    {
        return $this->hasMany(Footprint::class);
    }

    public function setCoversAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['covers'] = json_encode($pictures);
        }
    }

    public function getCoversAttribute($pictures)
    {
        return json_decode($pictures, true);
    }

    public function getCoverAttribute( $value )
    {
        if(!str_contains($value, 'img.lvye100.com')) {
            return \Storage::disk('admin')->url($value);
        }
        return $value;
    }
}
