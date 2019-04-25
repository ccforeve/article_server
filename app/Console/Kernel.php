<?php

namespace App\Console;

use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $begin_time = now()->subHours(33);
            $end_time = now()->subHours(32);
            $users = User::query()
                ->where('subscribe', 1)
                ->whereBetween('subscribe_at', [$begin_time, $end_time])
                ->get(['id', 'openid', 'nickname', 'subscribe', 'subscribe_at']);
            foreach ($users as $user) {
                message($user->openid, 'text', array_random(config('app.wechat_service_message')));
            }
        })->hourly();

        $schedule->call(function () {
            $users = User::query()->where('subscribe', 1)->get();
            foreach ($users as $key => $user) {
                $message = "当你想要放弃的时候，想想当初为什么要开始。晚安/月亮\n晚间爆文/玫瑰\n\n🔥<a href='http://btl.yxcxin.com/article_detail/17/public'>“绿叶搭配表，收藏好了”</a>\n\n🔥<a href='http://btl.yxcxin.com/article_detail/19/public'>““磷虾油”告诉您：血管是如何一天天堵塞的，看完吓一跳！血管“天敌”黑名单你有几个呢？”</a>\n\n🔥<a href='http://btl.yxcxin.com/article_detail/33/public'>“为什么美国癌症死亡率惊人下降，而我们发病率却在稳步上升！其中原因，我们真该学一学”</a>\n\n🔥<a href='http://btl.yxcxin.com/article_detail/52/public'>“家庭和睦，再穷都能发家”</a>\n\n点击下方《事业分享》→《分享事业》\n↓↓↓↓↓↓↓↓";
                message($user->openid, 'text', $message);
            }
        })->dailyAt('19:30');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
