<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/10 0010
 * Time: 下午 5:11
 */

namespace App\Http\Controllers\Api\Index;


use App\Http\Controllers\Api\Controller;
use App\Models\Footprint;
use App\Models\User;
use App\Models\UserArticle;
use App\Services\VisitorService;
use Carbon\Carbon;

class VisitorsController extends Controller
{
    /**
     * 访客记录
     * @param VisitorService $service
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index( VisitorService $service )
    {
        $user = $this->user();

        return $service->index($user->id);
    }

    /**
     * 记录详情
     * @param VisitorService $service
     * @param UserArticle $user_article
     * @return mixed
     */
    public function userArticleShow( VisitorService $service, UserArticle $user_article )
    {
        if($check = $this->checkMemberState($this->user())) {
            return $check;
        }

        return $service->userArticleShow($user_article);
    }

    /**
     * 访客阅读列表
     * @param VisitorService $service
     * @param $user_id
     * @return array
     */
    public function alsoRead( VisitorService $service, $user_id )
    {
        if($check = $this->checkMemberState($this->user())) {
            return $check;
        }

        return $service->alsoRead($user_id);
    }


    /**
     * 准客户列表
     * @param VisitorService $service
     * @return VisitorService|\Illuminate\Support\Collection
     */
    public function prospect( VisitorService $service )
    {
        $user = $this->user();
        if($check = $this->checkMemberState($user)) {
            return $check;
        }

        return $service->prospect($user->id);
    }

    /**
     * 查看是否有新的访客
     * @param VisitorService $service
     * @return mixed
     */
    public function ifHasVisitor( VisitorService $service )
    {
        $user = $this->user();
        $res = $service->ifNewVisitor($user->id);

        return $this->response->array($res);
    }

    /**
     * 更新访客足迹状态，页面红点去掉
     * @param VisitorService $service
     * @return mixed
     */
    public function updateNewState( VisitorService $service )
    {
        $user = $this->user();
        $service->updateNewState($user->id);

        return $this->response->array(['message' => '更新完成']);
    }

    /**
     * 检查是否已开通了会员
     * @param $user
     * @return mixed
     */
    public function checkMemberState( $user )
    {
        if($user->type == 1) {
            return false;
        }
        if(!Carbon::parse($user->member_lock_at)->gt(now())) {
            return $this->response->error('开通会员后即可查看', 402);
        }

        return false;
    }
}
