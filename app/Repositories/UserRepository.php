<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/7 0007
 * Time: 下午 4:01
 */

namespace App\Repositories;


use App\Models\Footprint;
use App\Models\UserArticle;

class UserRepository
{
    public function center( $user_id )
    {
        //文章数
        $article_count = UserArticle::query()->where('user_id', $user_id)->count();

        //谁查看我的头条数
        $footprint_count = Footprint::query()->where('user_id', $user_id)->count();

        return [
            'article_count' => $article_count,
            'footprint_count' => $footprint_count
        ];
    }
}
