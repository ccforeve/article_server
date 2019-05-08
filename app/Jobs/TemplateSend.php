<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TemplateSend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $openid;
    protected $message;
    protected $template_id;
    protected $url;

    /**
     * Create a new job instance.
     *
     * @param $openid
     * @param $message
     * @param $template_id
     * @param $url
     */
    public function __construct($openid, $message, $template_id, $url)
    {
        $this->openid = $openid;
        $this->message = $message;
        $this->template_id = $template_id;
        $this->url = $url;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function handle()
    {
        template_message($this->openid, $this->message, $this->template_id, $this->url);
    }
}
