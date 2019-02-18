<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Footprint extends Model
{
    const GROUPMESSAGE='groupmessage';
    const TIMELINE='timeline';
    const SINGLEMESSAGE='singlemessage';

    public static $relationship = [
        self::GROUPMESSAGE => '群友',
        self::TIMELINE => '好友',
        self::SINGLEMESSAGE => '好友',
    ];

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seeUser()
    {
        return $this->belongsTo(User::class, 'see_user_id');
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function userArticle()
    {
        return $this->belongsTo(UserArticle::class);
    }
}
