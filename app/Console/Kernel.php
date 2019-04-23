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
                $message = "晚间爆文/玫瑰\n\n🔥“子宫好不好看脸,脸上没这3个症状,那你的子宫很年轻！”\n\n🔥“今天，我开超市了，进来看看吧...”\n\n🔥“从脂肪肝到肝癌，只要4步！”\n\n🔥“为人厚道，终有福报”\n\n点击下方《事业分享》→《分享事业》\n↓↓↓↓↓↓↓↓";
                message($user->openid, 'text', $message);
            }
        })->dailyAt('19:00');
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
