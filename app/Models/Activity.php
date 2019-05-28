<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    public static $type = [
        12 => 1,
        24 => 2,
        60 => 3
    ];
}
