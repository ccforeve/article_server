<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageFamily extends Model
{
    const MAN = 1;
    const WOMAN = 2;

    public static $gender = [
        self::MAN => '先生',
        self::WOMAN => '女士',
    ];

    public static $family_gender = [
        self::MAN => '男',
        self::WOMAN => '女',
    ];

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function submitUser()
    {
        return $this->belongsTo(User::class, 'submit_user_id');
    }
}
