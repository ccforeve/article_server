<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/19 0019
 * Time: 上午 10:18
 */

namespace App\Http\Controllers\Api\Index;

use App\Events\PaySuccess;
use App\Http\Controllers\Api\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use EasyWeChat\Payment\Application;
use Illuminate\Http\Request;
use Pay;

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
            $order['superior'] = $user->superior;
            $order['superior_rate'] = floor(($user->superiorUser->integral_scale / 100) * $payment->price);
        }

        $add_order = Order::create($order);

        if($order['pay_type'] == 1) {
            //微信支付
            return $this->wechat($app, $add_order);
        } elseif($order['pay_type'] == 2) {
            //支付宝支付
            return $this->response->array([
                'code' => 200,
                'order_id' => $add_order->id,
                'pay_type' => $order['pay_type'],
                'message' => '支付宝支付生成订单成功'
            ]);
        }
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
            'pay_type' => $order['pay_type'],
            'msg' => '微信支付请求成功',
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
                    event(new PaySuccess($order));
                } else { // 用户支付失败
                    $order->state = 2;
                    $order->save();
                }
                return true; // 返回处理完成
            }
        });
        return $response;
    }

    /**
     * 支付宝支付
     * @param Order $order
     * @return mixed
     */
    public function alipay(Order $order)
    {
        $order = [
            'out_trade_no' => $order->order_id,
            'total_amount' => $order->price,
            'subject' => $order->title,
        ];

        $alipay = Pay::alipay()->wap($order);

        return $alipay;
    }

    public function alipayNotify()
    {
        $alipay = Pay::alipay();
        $data = $alipay->verify(); // 是的，验签就这么简单！
        // 请自行对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
        $data = json_decode($data, true);
        if ( $data[ 'trade_status' ] == 'TRADE_SUCCESS' || $data[ 'trade_status' ] == 'TRADE_FINISHED' ) {
            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
            $order = Order::query()->where('order_id', $data['out_trade_no'])->first();
            if(!$order->pay_at || !$order->state == 1) {
                // 2、验证app_id是否为该商户本身。
                if ( $data[ 'app_id' ] == config('pay.alipay.app_id') ) {
                    // 3、其它业务逻辑情况
                    event(new PaySuccess($order));
                }
            }
        }

        return $alipay->success();
    }

    /**
     * 滚动信息
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function orders()
    {
        $users = User::query()->inRandomOrder()->take(20)->get(['id', 'nickname']);

        return $users;
    }
}
