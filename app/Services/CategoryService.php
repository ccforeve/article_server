<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/7 0007
 * Time: 下午 5:02
 */

namespace App\Services;

use App\Models\ArticleCategory;

class CategoryService
{
    protected $category;

    public function __construct( ArticleCategory $category )
    {
        $this->category = $category;
    }
}
