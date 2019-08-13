<?php

namespace App\Listeners;

use App\Events\PaySuccess;
use App\Models\Activity;
use App\Models\Presale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaySuccessNotification
{
    /**
     * Handle the event.
     *
     * @param  PaySuccess  $event
     * @return void
     */
    public function handle(PaySuccess $event)
    {
        $order = $event->order;
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
        //判断是否是活动
        if(config('app.activity.enable')) {
            $activity = Activity::query()->latest('id')->first();
            if ( now()->gt(Carbon::parse($activity->begin_at)) && now()->lt(Carbon::parse($activity->end_at)) ) {
                $pay_user->luck_draw = $pay_user->luck_draw + Activity::$type[$order->month];
                $pay_user->save();
            }
        }
        $pay_user->save();

        //判断是否是售前开通的
        $presale = Presale::query()->where('user_id', $order->user_id)->first();
        if($presale) {
            $presale->order_id = $order->id;
            $presale->save();
        }
    }
}
