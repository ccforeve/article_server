<?php

namespace App\Http\Controllers\Api\Index;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\ExtensionArticleRequest;
use App\Models\Article;
use App\Models\ExtensionArticle;
use App\Models\User;
use App\Models\UserArticle;
use App\Services\ArticleService;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{
    /**
     * 文章列表
     * @param ArticleService $service
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list( ArticleService $service, Request $request )
    {
        $articles = $service->list($request->category_id, $request->search_key);

        return $articles;
    }

    /**
     * 单单文章详情
     * @param Request $request
     * @param $article_id
     * @return Article
     */
    public function detail( Request $request, $article_id )
    {
        if (isset($request->user_id) && $request->user_id) {
            $product_id = Article::query()->where('id', $article_id)->value('product_id');
            $user_article = UserArticle::with('article:id,title')->firstOrCreate(['article_id' => $article_id, 'user_id' => $request->user_id, 'product_id' => $product_id]);
            return $this->response->array([
                'type' => 2,
                'title' => $user_article->article->title,
                'user_article_id' => $user_article->id
            ]);
        }

        $article = Article::query()->where('id', $article_id)->first(['title']);
        return $this->response->array([
            'type' => 1,
            'title' => $article->title
        ]);
    }

    /**
     * 公共文章详情
     * @param ArticleService $service
     * @param $article_id
     * @return mixed
     */
    public function show( ArticleService $service, $article_id )
    {
        $user = $this->user();
        $user_article = $service->show($user->id, $article_id);

        return [
            'article' => $user_article->article,
            'user' => $user_article->user,
            'user_article_id' => $user_article->id,
            'product' => $user_article->article->product
        ];
    }

    /**
     * 更新文章分享数
     * @param $article_id
     * @return \Dingo\Api\Http\Response
     */
    public function updateShareCount( $article_id )
    {
        Article::query()->where('id', $article_id)->increment('share_count');

        return $this->response->noContent();
    }

    /**
     * 推荐好文章链接
     * @param ExtensionArticleRequest $request
     * @return mixed
     */
    public function extension( ExtensionArticleRequest $request )
    {
        $find = ExtensionArticle::query()->where('url', $request->url)->value('id');
        if(!$find) {
            $data = $request->all();
            $data[ 'user_id' ] = $this->user()->id;
            ExtensionArticle::query()->create($data);

            return $this->response->array([ 'message' => '提交成功' ]);
        }

        return $this->response->array([ 'message' => '该链接已提交过' ]);
    }
}
