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
                $message = "人生最精彩的不是实现梦想的瞬间，而是坚持梦想的过程，晚安/月亮\n晚间爆文/玫瑰\n\n🔥<a href='http://btl.yxcxin.com/article_detail/2657/public'>“1875，刷牙，洗头，洗脸，洗手，洗澡，洗碗，洗锅，洗菜，洗水果，洗衣服，我的绿叶大超市全包了！”</a>\n\n🔥<a href='http://btl.yxcxin.com/article_detail/2617/public'>“它让你上瘾，危害毁全身！专家发出毒品一般的警告！你还天天吃！”</a>\n\n🔥<a href='http://btl.yxcxin.com/article_detail/2659/public'>“致女人！（句句感人）”</a>\n\n点击下方《事业分享》→《分享事业》\n↓↓↓↓↓↓↓↓";
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
