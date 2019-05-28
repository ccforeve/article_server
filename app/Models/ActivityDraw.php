<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ActivityDraw extends Model
{
    protected $table = 'activity_draw';

    protected $guarded = [];

    public static $type = [
        1 => "5元现金红包",
        2 => "10元现金红包",
        3 => "20元现金红包",
        4 => "999元现金红包",
        5 => "50元现金红包",
        6 => "华为M5平板电脑",
        7 => "5元现金红包",
        8 => "10元现金红包",
        9 => "华为P30 Pro"
    ];

    public static $prize = [
        1 => 5,
        2 => 10,
        3 => 20,
        5 => 50,
        7 => 5,
        8 => 10,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
