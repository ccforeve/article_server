<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/19 0019
 * Time: 上午 10:18
 */

namespace App\Http\Controllers\Api\Index;


use App\Http\Controllers\Api\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use EasyWeChat\Payment\Application;
use Illuminate\Http\Request;

class PayController extends Controller
{
    /**
     * @param Application $app
     * @param Request $request
     * @param Payment $payment
     * @return mixed
     */
    public function addOrder( Application $app, Request $request, Payment $payment )
    {
        $user = $this->user();

        $order = [
            'order_id'  =>  date('YmdHis').str_random(12).rand(1000, 9999),
            'user_id'   =>  $user->id,
            'pay_type'  =>  $request->pay_type,
            'price'     =>  $payment->price,
            'title'     =>  $payment->title,
            'month'     =>  $payment->month,
        ];

        if($user->superior) {
            $order['superior'] = $user->extension_id;
            $order['superior_rate'] = floor($user->extension->integral_scale * $payment->price);
        }

        if($user->superior_up && optional($user->superior_up)->type == 2) {
            $order[ 'superior_up' ] = $user->extension_up;
            $order[ 'superior_up_scale' ] = floor($user->superior_up->integral_scale_second * $payment->price);
        }

        $add_order = Order::create($order);

        //微信支付
        return $this->wechat($app, $add_order);
    }

    /**
     * 调用easywechatSDK返回支付配置
     * @param $app
     * @param $order
     * @return mixed
     */
    public function wechat($app, $order)
    {
        $user = User::query()->where('id', $order['user_id'])->select('openid')->first();
        $attributes = [
            'trade_type'     => 'JSAPI', // JSAPI，NATIVE，APP...
            'body'           => $order['title'],
            'detail'         => $order['title'],
            'out_trade_no'   => $order['order_id'],
            'total_fee'      => $order['price'] * 100, // 单位：分
            'notify_url'     => 'http://stl.yxcxin.com/api/wechat_out_trade', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'openid'         => $user->openid, // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
        ];
        $result = $app->order->unify($attributes);
        if(isset($result['err_code'])) {
            return $this->response->array([
                'code' => 513,
                'err_code' => $result['err_code'],
                'err_msg' => $result['err_code_des']
            ]);
        }
        $config = $app->jssdk->sdkConfig($result['prepay_id']); // 返回数组
        return $this->response->array([
            'code' => 200,
            'msg' => '请求支付成功',
            'config' => $config,
            'member_month' => $order->month
        ]);
    }

    /**
     *
     * @param Application $app
     * @return mixed
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function outTradeNo(Application $app)
    {
        //待测试
        $response = $app->handlePaidNotify(function( $notify, $fail) {
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = Order::query()->where('order_id', $notify[ 'out_trade_no' ])->first();
            info("订单支付成功，支付平台：，订单号:{$notify[ 'out_trade_no' ]}");
            if ( !$order ) { // 如果订单不存在
                return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            // 如果订单存在
            // 检查订单是否已经更新过支付状态
            if ( $order->pay_at ) { // 假设订单字段“支付时间”不为空代表已经支付
                return true; // 已经支付成功了就不再更新了
            }
            // 用户是否支付成功
            if ($notify['return_code'] === 'SUCCESS') {
                // 用户是否支付成功
                if (array_get($notify, 'result_code') === 'SUCCESS') {
                    //只要用户支付成功，先把支付时间更新，以防后面发消息出错再次回调
                    $order->pay_at = now();
                    // 订单表修改为已经支付状态
                    $order->state = 1;
                    $order->save();
                    //用户会员加时间
                    $pay_user = User::query()->where('id',$order->user_id)->first();
                    //判断用户的会员时间是否过期
                    if (Carbon::parse($pay_user->member_lock_at)->gt(now())){
                        $time = Carbon::parse($pay_user->member_lock_at);
                    } else {
                        $time = now();
                    }
                    $pay_user->member_up_at = now();
                    $pay_user->member_lock_at = $time->addMonth($order->month);
                    $pay_user->save();
                } else { // 用户支付失败
                    $order->state = 2;
                }
                $order->save();
                return true; // 返回处理完成
            }
        });
        return $response;
    }
}
