<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/18 0018
 * Time: 下午 2:01
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $guarded = [];

    public function products()
    {
        return $this->hasMany(Product::class, 'parent_category_id', 'online_id');
    }
}
