<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ActivityDraw extends Model
{
    protected $table = 'activity_draw';

    public static $type = [
        1 => "5元",
        2 => "10元",
        3 => "20元",
        4 => "50元"
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
