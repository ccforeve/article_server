<?php

namespace App\Http\Controllers\Api\Index;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\ExtensionArticleRequest;
use App\Models\ExtensionArticle;
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
     * 推荐好文章链接
     * @param ExtensionArticleRequest $request
     * @return mixed
     */
    public function extension( ExtensionArticleRequest $request )
    {
        $data = $request->all;
        $data['user_id'] = $this->user()->id;
        ExtensionArticle::query()->create($data);

        return $this->response->array(['message' => '提交成功']);
    }
}
