<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/8 0008
 * Time: 上午 8:52
 */

namespace App\Services;


use App\Models\PosterCategory;

class PosterCategoryService
{
    public function list()
    {
        $cates = PosterCategory::all();

        return $cates;
    }
}
