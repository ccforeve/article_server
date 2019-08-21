<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/18 0018
 * Time: 下午 2:00
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function article()
    {
        return $this->hasOne(Article::class);
    }

    public function collection()
    {
        return $this->hasOne(Collection::class);
    }
}
