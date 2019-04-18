<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/8 0008
 * Time: 上午 10:40
 */

namespace App\Services;


use App\Models\Footprint;
use App\Models\User;
use App\Models\UserArticle;
use App\Repositories\UserArticleRepository;
use Carbon\Carbon;

class UserArticleService
{
    protected $user_article_repository;

    public function __construct( UserArticleRepository $repository )
    {
        $this->user_article_repository = $repository;
    }

    public function list( $user_id )
    {
        $articles = UserArticle::with('article:id,title,cover,read_count,share_count,product_id,created_at')
            ->where(['user_id' =>  $user_id, 'product_id' => 0])->latest('id')->paginate(8);
        $articles->transform(function ($article) {
            $article_value = collect($article->article);
            $article_value->put('article', $article->article);
            $article_value->put('user_article_id', $article->id);

            return $article_value;
        });

        return $articles;
    }

    public function show( $user_id, $id, $from, $share_id )
    {
        $user_article = $this->user_article_repository->articleFromUser(['id' => $id]);
        $user_article->load(
            'user:id,nickname,avatar,wechat,phone,qrcode,profession,subscribe,receive_message,member_lock_at',
            'article:id,product_id,title,cover,detail,desc,created_at',
            'product:id,name,cover,price,money,ticket,kind,listed_at,sale_unit,min_unit,unit,multiple'
        );
        if ( $user_article->user_id != $user_id ) {
            //用户文章第一次阅读则推送文本消息给该文章拥有者
//            $openid = User::query()->where('id', $user_id)->value('openid');
//            $cache_name = $user_article->id . '_' . $openid;
//            if ( !\Cache::has($cache_name) && $user_article->user->subscribe ) {
//                //推送消息
//                $context = "有人对你的头条感兴趣！还不赶紧看看是谁~\n\n头条标题：《{$user_article->article->title}》\n\n<a href='http://btl.yxcxin.com/visitor'>【点击这里】</a>查看谁对我的头条感兴趣>></a>";
//                message($user_article->user->openid, 'text', $context);
//                \Cache::put($cache_name, 1, 60);
//            }
            //用户文章浏览数+1
            $user_article->increment('read_count', 1);
            //记录访客足迹(停留时间处理)
            $footprint = Footprint::Create([
                'user_id' => $user_article->user_id,
                'article_id' => $user_article->article_id,
                'see_user_id' => $user_id,
                'user_article_id' => $user_article->id,
                'share_id' => $share_id,
                'type' => 1,
                'from' => $from
            ]);
        }

        return [
            'article' => $user_article->article,
            'user' => $user_article->user,
            'footprint' => isset($footprint) ? $footprint->id : null,
            'user_article_id' => $user_article->id,
            'product' => $user_article->product
        ];
    }

    public function becomeMyArticle( $user_id, $article_id )
    {
        $user_article = UserArticle::query()->where(['user_id' => $user_id, 'article_id' => $article_id])->first();
        if(!$user_article) {
            $user_article = UserArticle::create([
                'user_id' => $user_id,
                'article_id' => $article_id
            ]);

            return ['code' => 201, 'message' => '创建文章成功', 'user_article_id' => $user_article->id];
        }

        return ['code' => 201, 'message' => '已有文章', 'user_article_id' => $user_article->id];
    }
}
