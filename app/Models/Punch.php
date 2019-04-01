<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/22 0022
 * Time: 下午 3:51
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Punch extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comment()
    {
        return $this->belongsTo(PunchComment::class, 'comment_id');
    }

    public function scopePunch( $query )
    {
        $query->whereDate('created_at', now()->toDateString());
    }
}
