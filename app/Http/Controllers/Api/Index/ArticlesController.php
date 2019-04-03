<?php

namespace App\Http\Controllers\Api\Index;

use App\Http\Controllers\Api\Controller;
use App\Services\ArticleService;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{
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
        ];
    }
}
