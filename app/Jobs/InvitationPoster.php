<?php

namespace App\Jobs;

use EasyWeChat\OfficialAccount\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use EasyWeChat\Factory;

class InvitationPoster implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $openid;

    protected $image_url;

    /**
     * Create a new job instance.
     *
     * @param $openid
     * @param $image_url
     */
    public function __construct($openid, $image_url)
    {
        $this->openid = $openid;

        $this->image_url = $image_url;
    }

    /**
     * 推送推广消息和图片
     * @param Application $app
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function handle(Application $app)
    {
        // 上传临时图片素材
        $file_path = public_path('uploads/') . $this->image_url;
        $ret = $app->media->uploadImage($file_path);
        unlink($file_path);

        //推送文本消息
        message($this->openid, 'text', "分享下图邀请您的朋友同事也来使用事业头条，推荐好友成为正式会员，您将获得丰厚佣金！\n\n赶紧长按保存并分享下方图片吧\n\n↓↓↓↓↓");
        //推送推广图片
        message($this->openid, 'image', $ret['media_id']);
    }
}
