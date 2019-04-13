<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/7 0007
 * Time: 下午 5:29
 */

namespace App\Services;


use App\Models\Brand;
use App\Models\Poster;
use App\Models\PosterCategory;

class PosterService
{
    public function __construct(  )
    {

    }

    public function catePoster( $category_id )
    {
        $category = PosterCategory::query()->where('id', $category_id)->first();
        $posters = $category->posters()->paginate(12);

        return $posters;
    }

    public function brandPoster( $brand_id )
    {
        $brand = Brand::query()->where('id', $brand_id)->first();
        $posters = $brand->posters()->paginate(12);

        return $posters;
    }
}
