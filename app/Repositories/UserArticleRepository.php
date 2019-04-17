<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/7 0007
 * Time: 下午 5:09
 */

namespace App\Repositories;


use App\Models\UserArticle;

class UserArticleRepository
{
    protected $user_article;

    public function __construct( UserArticle $article )
    {
        $this->user_article = $article;
    }

    public function articleFromUser( $where )
    {
        $user_article = $this->user_article->with(
            'user:id,nickname,avatar,wechat,phone,qrcode,profession,subscribe,receive_message,member_lock_at',
            'article:id,product_id,title,cover,detail,desc,created_at',
            'article.product:id,name,cover,price,money,ticket,kind,listed_at,sale_unit,min_unit,unit,multiple'
        )
            ->where($where)
            ->select('id', 'user_id', 'article_id')
            ->first();

        return $user_article;
    }
}
