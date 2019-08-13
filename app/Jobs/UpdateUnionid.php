<?php

namespace App\Jobs;

use App\Models\User;
use EasyWeChat\OfficialAccount\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateUnionid implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $openid;

    /**
     * Create a new job instance.
     *
     */
    public function __construct($openid)
    {
        $this->openid = $openid;
    }

    /**
     * @param Application $app
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function handle(Application $app)
    {
        $info = $app->user->get($this->openid);
        User::query()->where('openid', $this->openid)->update(['unionid' => $info['unionid']]);
    }
}
