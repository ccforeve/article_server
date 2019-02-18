<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    const TYPEONE = 1;
    const TYPETWO = 2;
    const MAN = 1;
    const WOMAN = 2;

    public static $type = [
        self::TYPEONE => '类型1',
        self::TYPETWO => '类型2',
    ];

    public static $gender = [
        self::MAN => '先生',
        self::WOMAN => '女士',
    ];

    protected $guarded = [];

    public function submitUser()
    {
        return $this->belongsTo(User::class, 'submit_user_id');
    }
}
