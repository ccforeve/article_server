<?php

namespace App\Console;

use App\Jobs\TemplateSend;
use App\Models\Article;
use App\Models\User;
use App\Models\WechatTemplate;
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
                $users = User::query()->where('subscribe', 1)->cursor();
                switch ($sh->type){
                    case 1:
                        foreach ($users as $key => $user) {
                            message($user->openid, 'text', $sh->content);
                        }
                        break;
                    case 2:
                        $article = Article::query()->where('id', $sh->content)->first(['id', 'title', 'cover', 'desc', 'product_id']);
                        $item = [
                            'title' => $article->title,
                            'description' => $article->desc,
                            'url' => "http://btl.yxcxin.com/article_detail/{$article->id}/public",
                            'image' => $article->cover
                        ];
                        if($article->product_id) {
                            $item['image'] = "http:" . str_replace('/p/', '/pxs/', $article->cover);
                        }
                        foreach ($users as $key => $user) {
                            message($user->openid, 'new_item', $item);
                        }
                        break;
                    case '3':
                        $template = WechatTemplate::query()->find($sh->template_id);
                        $message = [
                            "first" => [$template->first['message'], $template->first['color']],
                            "remark" => [$template->remark['message'], $template->remark['color']]
                        ];
                        foreach ($template->keyword as $key => $item) {
                            if($item['message'] == 'date') {
                                $item['message'] = now()->toDateString();
                            }
                            $keyword = 'keyword' . ($key + 1);
                            $message[$keyword] = [$item['message'], isset($item['color']) ? $item['color'] : ""];
                        }
                        foreach ($users as $key => $user) {
                            dispatch(new TemplateSend($user->openid, $message, $template->template_id, $template->url))->onQueue('article');
//                            template_message($user->openid, $message, $template->template_id, $template->url);
                        }
                        break;
                }
            }
        })->everyMinute();
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
