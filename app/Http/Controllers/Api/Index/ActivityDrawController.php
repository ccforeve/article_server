<?php

namespace App\Http\Controllers\Api\Index;

use App\Http\Controllers\Api\Controller;
use App\Models\ActivityDraw;
use App\Services\ProfitService;
use EasyWeChat\Payment\Application;
use Illuminate\Http\Request;

class ActivityDrawController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function store( Request $request, Application $app, ProfitService $service )
    {
        $billno = date('YmdHis') . str_random(12);
        $user = $this->user();
        $activity = ActivityDraw::query()->create([
            'user_id' => $user->id,
            'prize' => $request->prize,
            'name' => $user->nickname,
            'phone' => $user->phone,
            'activity_id' => 1,
            'mch_billno' => $billno
        ]);
        // 次数减一
        $user->decrement('luck_draw');
//        return $this->response->error('测试错误时', 409);
        return $activity;
        //发送红包
        $fee = ActivityDraw::$prize[$request->prize];
        if(is_int($fee) && $fee > 0 && $fee <= 200) {
            $redpackData = [
                'mch_billno'   => $billno,
                'send_name'    => '盛夏会员大抽奖',
                're_openid'    => $user->openid,
                'total_amount' => $fee * 100,  //单位为分，不小于100
                'wishing'      => '恭喜发财',
                'act_name'     => '盛夏会员大抽奖活动',
                'remark'       => "给{$user->openid}提现",
            ];

            return $service->withDrawCash($app, $activity, $redpackData);
        } else {
            return $this->response->error('请求错误', 409);
        }
    }

    /**
     * 最新100条记录
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function list()
    {
        $draws = ActivityDraw::query()->where('activity_id', 1)->latest('id')->take(100)->get();
        $draws->transform(function ($draw) {
            $new = collect($draw);
            $new->put('prize', ActivityDraw::$type[$draw->prize]);

            return $new;
        });

        return $draws;
    }
}
