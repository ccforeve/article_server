<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/10 0010
 * Time: 下午 4:08
 */

namespace App\Http\Controllers\Api\Index;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\CashRequest;
use App\Models\Cash;
use App\Services\ProfitService;
use EasyWeChat\Payment\Application;
use Illuminate\Http\Request;
use Pay;

class ProfitsController extends Controller
{
    public function index( ProfitService $service )
    {
        $user = $this->user();

        return $service->index($user->id);
    }

    /**
     * 普通用户推广记录
     * @param ProfitService $service
     * @return mixed
     */
    public function normal( ProfitService $service )
    {
        $user = $this->user();

        return $service->normal($user->id);
    }

    /**
     * 经销商推广记录
     * @param ProfitService $service
     * @return mixed
     */
//    public function dealer( ProfitService $service )
//    {
//        $user = $this->user();
//
//        return $service->dealer($user->id);
//    }

    /**
     * 提现记录
     * @param ProfitService $service
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function withdrawCashList( ProfitService $service )
    {
        $user = $this->user();

        return $service->withdrawCashList($user->id);
    }

    /**
     * 推广的用户列表
     * @param ProfitService $service
     * @return mixed
     */
    public function extensionUsers( ProfitService $service )
    {
        $user = $this->user();
        return $service->extensionUsers($user->id);
    }

    /**
     * 推广成功的订单列表
     * @param ProfitService $service
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function extensionOrder( ProfitService $service )
    {
        $user = $this->user();
        return $service->extensionOrder($user->id);
    }

    /**
     * 申请提现
     * @param CashRequest $request
     * @param ProfitService $service
     * @return mixed
     */
//    public function withdrawCash( CashRequest $request, ProfitService $service )
//    {
//        $user = $this->user();
//        $profit = $service->index($user->id);
//        if($profit['surplus_profit'] < $request->price) {
//            return $this->response->error('提现余额不足', 409);
//        }
//        $data = $request->all();
//        $data['user_id'] = $user->id;
//        $data['type'] = 1;
//        Cash::create($data);
//
//        return $this->response->array([
//            'code' => 201,
//            'message' => '申请提现完成'
//        ]);
//    }

    /**
     * 提现
     * @param Application $app
     * @param CashRequest $request
     * @param ProfitService $service
     */
    public function WithdrawCash(Application $app, CashRequest $request, ProfitService $service)
    {
        $fee = $request->price;
        $user = $this->user();
        $profit = $service->index($user->id);
        if($profit['surplus_profit'] < $fee) {
            return $this->response->error('提现余额不足', 409);
        }
        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['type'] = 1;
        $cash = Cash::query()->create($data);
        $billno = date('YmdHis').str_random(12);
        $redpackData = [
            'mch_billno'   => $billno,
            'send_name'    => '事业分享提现',
            're_openid'    => $user->openid,
            'total_amount' => $fee * 100,  //单位为分，不小于100
            'wishing'      => '努力就有回报',
            'act_name'     => '推广佣金活动',
            'remark'       => "给{$user->openid}提现",
        ];
        try {
            $result = $app->redpack->sendNormal($redpackData);
            if ( $result[ 'return_code' ] === 'SUCCESS' ) {
                if ( $result[ 'result_code' ] === 'SUCCESS' && $result[ 'err_code' ] == 'SUCCESS' ) {      //发送红包成功
                    $cash->mch_billno = $billno;
                    $cash->state = 1;
                    $cash->over_at = now()->toDateTimeString();
                    $cash->save();
                    return $this->response->array([
                        'message' => '申请提现完成'
                    ]);
                }
                //发送红包失败
                $cash->state = 2;
                $cash->remark = $result[ 'return_msg' ];
                $cash->save();
                return $this->response->error($result[ 'return_msg' ], 409);
            }
        } catch (\Exception $e) {   //发送红包失败
            $cash->state = 2;
            $cash->remark = $e->getMessage();
            $cash->save();
            return $this->response->error($result[ 'return_msg' ], 409);
        }
    }
}
