<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/7 0007
 * Time: 下午 5:02
 */

namespace App\Services;

use App\Models\Article;
use App\Models\UserArticle;
use App\Repositories\UserArticleRepository;

class ArticleService
{
    protected $article;

    protected $user_article_repository;

    public function __construct( Article $article, UserArticleRepository $user_article_repository )
    {
        $this->article = $article;

        $this->user_article_repository = $user_article_repository;
    }

    public function list( $category, $search_key )
    {
        $articles = $this->article->query()
            ->when($category, function ($query) use ($category) {
                $query->where('category_id', $category);
            })
            ->when($search_key, function ($query) use ($search_key) {
                $query->where('title', 'like', "%$search_key%");
            })
            ->where('product_id', 0)
            ->select('id', 'category_id', 'title', 'cover', 'covers', 'read_count', 'cover_state', 'created_at')->latest('id')->paginate(6);
        $articles->transform(function ($article) {
            $value = collect($article);
            $value->put('created_at', $article->created_at->toDateString());

            return $value;
        });

        return $articles;
    }

    public function show( $user_id, $article_id )
    {
        $article = Article::query()->where('id', $article_id)->first();
        $article->increment('read_count');
        $user_article = $this->user_article_repository->articleFromUser(['user_id' => $user_id, 'article_id' => $article_id]);
        if(!$user_article) {
            $user_article = UserArticle::create([
                'user_id' => $user_id,
                'article_id' => $article->id,
                'product_id' => $article->product_id
            ]);
            $user_article = $this->user_article_repository->articleFromUser(['id' => $user_article->id]);
        }
        $user_article->load(
            'user:id,nickname,avatar,wechat,phone,qrcode,profession,subscribe,receive_message,member_lock_at',
            'article:id,product_id,title,cover,detail,desc,created_at',
            'product:id,name,cover,price,money,ticket,kind,listed_at,sale_unit,min_unit,unit,multiple'
        );

        return $user_article;
    }
}
