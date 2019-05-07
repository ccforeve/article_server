<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class WechatTemplate extends Model
{
    protected $casts = [
        'first' => 'json',
        'keyword' => 'json',
        'remark' => 'json'
    ];

    public function getKeyWordAttribute($keyword)
    {
        return array_values(json_decode($keyword, true) ?: []);
    }

    public function setKeyWordAttribute($keyword)
    {
        $this->attributes['keyword'] = json_encode(array_values($keyword));
    }
}
