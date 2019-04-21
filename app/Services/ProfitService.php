<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/10 0010
 * Time: 下午 4:17
 */

namespace App\Services;


use App\Models\Cash;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;

class ProfitService
{
    public function index( $user_id, $user_type )
    {
        $superior_orders = Order::query()->pay()->where('superior', $user_id)->get();
        $superior_up_orders = Order::query()->pay()->where('superior_up', $user_id)->get();

        $today_profit = 0;  //今日收益
        $total_profit = 0;  //累计收益

        foreach ($superior_orders as $order) {
            if($this->isToday($order->pay_at)) {
                $today_profit += $order->superior_rate;
            }
            $total_profit += $order->superior_rate;
        }
        if($user_type === 1) {
            foreach ($superior_up_orders as $order) {
                if($this->isToday($order->pay_at)) {
                    $today_profit += $order->superior_up_rate;
                }
                $total_profit += $order->superior_up_rate;
            }
        }
        //已提现金额
        $used_profit = Cash::query()->where(['user_id' => $user_id])->sum('price');
        //可提现金额
        $surplus_profit = $total_profit - $used_profit;

        return [
            'today_profit' => $today_profit,
            'total_profit' => $total_profit,
            'used_profit' => number_format($used_profit) * 1,
            'surplus_profit' => $surplus_profit
        ];
    }

    public function normal( $user_id )
    {
        $superior_orders = Order::query()->pay()->where('superior', $user_id)->get();
        $users = User::query()->where('superior', $user_id)->latest('id')->get();

        return $this->detail($superior_orders, $users, 1);
    }

//    public function dealer( $user_id )
//    {
//        $superior_up_orders = Order::query()->pay()->where('superior_up', $user_id)->get();
//        $users_up = User::query()->where('superior_up', $user_id)->latest('id')->get();
//
//        return $this->detail($superior_up_orders, $users_up, 2);
//    }

    public function detail($orders, $users, $type)
    {
        $today_profit = 0;      //今日推广金
        $total_profit = 0;      //累计推广金
        $ex_user_today = 0;     //今日推广用户
        $ex_user_total = 0;     //累计今日推广用户
        $pay_user_today = 0;    //付费用户
        $pay_user_total = 0;    //累计付费用户
        $pay_money_today = 0;   //今日订单付费金额
        $pay_money_total = 0;   //累计订单付费金额

        foreach ($orders as $order) {
            if($this->isToday($order->pay_at)) {
                $today_profit += $type === 1 ? $order->superior_rate : $order->superior_up_rate;
                $pay_money_today += $order->price;
            }
            $total_profit += $type === 1 ? $order->superior_rate : $order->superior_up_rate;
            $pay_money_total += $order->price;
        }

        foreach ($users as $key => $user) {
            if($this->isToday($user->created_at)) {
                $ex_user_today++;
                if(Carbon::parse($user->member_lock_at)->gt(now())) {
                    $pay_user_today++;
                }
            }
            $ex_user_total++;
            if($user->member_lock_at) {
                $pay_user_total++;
            }
        }

        return [
            'today_profit' => $today_profit,
            'total_profit' => $total_profit,
            'ex_user_today' => $ex_user_today,
            'ex_user_total' => $ex_user_total,
            'pay_user_today' => $pay_user_today,
            'pay_user_total' => $pay_user_total,
            'pay_money_today' => $pay_money_today,
            'pay_money_total' => $pay_money_total,
        ];
    }

    public function isToday( $field )
    {
        return Carbon::parse($field)->gt(today()) && Carbon::parse($field)->lt(now()->addDay()->startOfDay());
    }

    public function withdrawCashList( $user_id )
    {
        $cashs = Cash::query()->where('user_id', $user_id)->latest('id')->paginate(10);
        $cashs->transform(function ($cash) {
            $new = collect($cash);
            $new->put('state_cn', Cash::$state[$cash->state]);

            return $new;
        });

        return $cashs;
    }

    public function extensionUsers( $user_id )
    {
        $users = User::query()
            ->select('id', 'nickname', 'avatar', 'created_at')
            ->where('superior', $user_id)
            ->latest('id')
            ->paginate(10);

        return $users;
    }

    public function extensionOrder( $user_id )
    {
        $orders = Order::with('user:id,nickname,avatar')
            ->select('id', 'user_id', 'price', 'pay_at', 'created_at')
            ->where(['superior' => $user_id, 'state' => 1])
            ->latest('id')
            ->paginate(10);

        return $orders;
    }
}
