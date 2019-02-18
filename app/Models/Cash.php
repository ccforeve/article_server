<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cash extends Model
{
    const UN_FINISH = 0;
    const FINISH = 1;
    const FAIL = 2;

    public static $state = [
        self::UN_FINISH => '申请中',
        self::FINISH => '已完成',
        self::FAIL => '提现失败'
    ];

    protected $guarded = ['state', 'over_at'];

    public function user(  )
    {
        return $this->belongsTo(User::class);
    }
}
