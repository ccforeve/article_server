<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/7 0007
 * Time: 下午 2:46
 */

namespace App\Repositories;


use App\Models\ArticleCategory;

class CategoryRepository
{
    protected $category;

    public function __construct( ArticleCategory $category )
    {
        $this->category = $category;
    }

    public function list()
    {
        $categories = $this->category->latest('sort')->get();

        return $categories;
    }
}
