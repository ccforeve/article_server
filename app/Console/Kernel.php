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
            $first_time = now()->subSeconds(10)->toDateTimeString();
            $second_time = now()->addSeconds(10)->toDateTimeString();
            $sh = \App\Models\Schedule::query()->whereBetween('send_at', [$first_time, $second_time])->first();
            if($sh) {
                $users = User::query()->where('subscribe', 1)->get();
                foreach ($users as $key => $user) {
                    message($user->openid, 'text', $sh->content);
                }
            }
        })->everyMinute();

//        $schedule->call(function () {
//            $users = User::query()->where('subscribe', 1)->get();
//            foreach ($users as $key => $user) {
//                $message = "只要不放弃努力和追求，小草也有点缀春天的价值。早安！/太阳\n早间爆文/奋斗/奋斗\n\n🔥<a href='http://btl.yxcxin.com/article_detail/47/public'>“41岁的林心如和70岁的婆婆站在一起，惊呆了~”</a>\n\n🔥<a href='http://btl.yxcxin.com/article_detail/2711/public'>“牙膏、卫生巾、洗衣液、保健品...没了都可以找我，我一直都在，给您送去健康快乐的生活方式”</a>\n\n🔥<a href='http://btl.yxcxin.com/article_detail/2713/public'>“早餐喝这个后身亡，家人痛哭悔不当初！你也常喝（人民日报紧急提醒）”</a>\n\n🔥<a href='http://btl.yxcxin.com/article_detail/2714/public'>“ 曾国藩六戒：做不到这六点，你将一事无成！ ”</a>\n\n点击下方《事业分享》→《分享事业》\n↓↓↓↓↓↓↓↓";
//                message($user->openid, 'text', $message);
//            }
//        })->dailyAt('08:00');

//        $schedule->call(function () {
//            $users = User::query()->where('subscribe', 1)->get();
//            foreach ($users as $key => $user) {
//                $message = "把平凡的事做好，就是不平凡，把简单的事做好，就是不简单。晚安/月亮\n晚间爆文/玫瑰\n\n🔥<a href='http://btl.yxcxin.com/article_detail/2715/public'>“4月如果你的护肤品用完了，5月份诚邀你来试试绿叶的，安全放心用！”</a>\n\n🔥<a href='http://btl.yxcxin.com/article_detail/2716/public'>“洗脸时加点绿叶牙膏，每天1次，7天之后.....”</a>\n\n🔥<a href='http://btl.yxcxin.com/article_detail/2720/public'>“家里这些隐形毒药，会让孩子变呆变傻！危害孩子健康的都是爸妈想不到的细节！”</a>\n\n🔥<a href='http://btl.yxcxin.com/article_detail/2721/public'>“人性最大的恶，是不懂感恩”</a>\n\n点击下方《事业分享》→《分享事业》\n↓↓↓↓↓↓↓↓";
//                message($user->openid, 'text', $message);
//            }
//        })->dailyAt('19:30');

//        $schedule->call(function () {
//            $users = User::query()->where('subscribe', 1)->get();
//            foreach ($users as $key => $user) {
//                $message = "这是一款为您精心打造的专属《移动工作室》\n\n一、事业分享是什么❓\n\n事业分享，不只是品牌文库，它更是营销海报＋品牌主页＋微信名片＋品牌相册＋… 等各大功能为一体的事业推广工具！\n\n二、事业分享能为我带来什么❓\n\n品牌事业助理每天为您发布全新的文章、海报，您的每一次分享都自带微名片，让引流拓客变得简单；\n\n专属的个人主页、产品资料，无需您上传企业介绍和上架产品内容，事业助理早已为您准备。告别繁琐的操作，让客流变现更高效；\n\n基于微信10.4亿海量用户无线传播，轻轻松松把您的事业和产品传播到世界的每一个角落; \n\n三、怎么进入事业分享❓\n\n<a href='http://btl.yxcxin.com/open_member'>点击此处通过事业分享立即获客</a>\n\n↓↓↓↓↓↓↓↓";
//                message($user->openid, 'text', $message);
//            }
//        })->dailyAt('12:20');
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
