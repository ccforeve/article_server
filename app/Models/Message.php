<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    const MAN = 1;
    const WOMAN = 2;

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
