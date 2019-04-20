<?php

namespace App\Http\Controllers\Api\Index;

use App\Models\Footprint;
use App\Models\UserArticle;
use App\Services\UserArticleService;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

class UserArticlesController extends Controller
{
    /**
     * 我的文章列表
     * @param UserArticleService $service
     * @param $user_id
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list(UserArticleService $service, $user_id)
    {
        $articles = $service->list($user_id);

        return $articles;
    }

    /**
     * 我的文章详情
     * @param Request $request
     * @param UserArticleService $service
     * @param $article_id
     * @param int $share_id
     * @return array
     */
    public function show( Request $request, UserArticleService $service, $article_id, $share_id = 0 )
    {
        $user = $this->user();
        $user_article = $service->show($user->id, $article_id, $request->from, $share_id);

        return $user_article;
    }

    /**
     * 成为我的文章
     * @param Request $request
     * @param UserArticleService $service
     * @return array
     */
    public function becomeMyArticle( Request $request, UserArticleService $service )
    {
        $user_id = $this->user()->id;
        return $service->becomeMyArticle($user_id, $request->article_id);
    }

    /**
     * 分享文章
     * @param Request $request
     * @param UserArticle $user_article
     * @return mixed
     */
    public function shareSuccess( Request $request, UserArticle $user_article )
    {
        $footprint = Footprint::query()->create([
            'user_id' => $user_article->user_id,
            'article_id' => $user_article->article_id,
            'see_user_id' => $request->user_id,
            'user_article_id' => $user_article->id,
            'type' => 2,
            'from' => $request->from
        ]);
        if($footprint->id) {
            return $this->response->array([ 'message' => '分享成功' ]);
        }

        return $this->response->array([ 'message' => '分享失败' ]);
    }
}
