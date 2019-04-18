<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/7 0007
 * Time: 下午 5:02
 */

namespace App\Services;

use App\Models\Footprint;
use App\Models\UserArticle;

class UserService
{
    /**
     * 个人中心
     * @param $user_id
     * @return array
     */
    public function center( $user_id )
    {
        //文章数
        $article_count = UserArticle::query()->where(['user_id' => $user_id, 'product_id' => 0])->count();

        //谁查看我的头条数
        $footprint_count = Footprint::query()->where('user_id', $user_id)->count();

        return [
            'article_count' => $article_count,
            'footprint_count' => $footprint_count
        ];
    }
}
