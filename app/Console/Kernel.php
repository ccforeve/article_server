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
                ->whereBetween('subscribed_at', [$begin_time, $end_time])
                ->get(['id', 'openid', 'nickname', 'subscribe', 'subscribed_at']);
            foreach ($users as $user) {
                message($user->openid, 'text', array_random(config('app.wechat_service_message')));
            }
        })->hourly();
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
