<?php
namespace App\Http\Traits;

use App\Models\User;
use Carbon\Carbon;

trait CreateOrCheckUserNotifications
{
    /**
     * 检查用户账户情况
     * @param $FromUserName
     * @param $eventkey
     * @return mixed
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function checkUser( $FromUserName, $eventkey = 0 )
    {
        $user = User::where('openid', $FromUserName)->first();
        if($user) {
            if($eventkey) {
                $this->relation($user, $eventkey);
            }
            else {
                $user->subscribe = 1;
                $user->subscribe_at = Carbon::now()->toDateTimeString();
                $user->save();
            }
        } else {    //创建用户
            $user = $this->register($FromUserName, $eventkey);
        }
        return $user;
    }

    /**
     * 创建账户
     * @param $FromUserName     openid
     * @param $eventkey         上级推荐id
     * @return mixed
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function register($FromUserName, $eventkey = 0)
    {
        $user = $this->app->user->get($FromUserName);
        $data = [
            'openid' => $FromUserName,
            'nickname' => $user[ 'nickname' ],
            'avatar' => $user[ 'headimgurl' ],
            'subscribe' => 1,
            'subscribe_at' => now()->toDateTimeString(),
            'sex' => $user[ 'sex' ]
        ];
        $user = User::create($data);
        if($eventkey) {
            $puser = User::query()->where('id', $eventkey)->first(['id', 'superior']);
            $user->superior = $puser->id;
            $user->superior_up = $puser->superior;
            $user->extension_at = now()->toDateTimeString();
            $user->extension_type = '推广二维码';
            $user->save();
        }
        //保存用户
        return $user;
    }

    public function relation( $user, $eventkey )
    {
        if($user->id !== $eventkey && $user->extension_id == 0 && $user->type == 0) {
            $pinfo = User::find($eventkey);
            //当用户本来没有推广用户和经销商的时候
            $user->subscribe = 1;
            $user->subscribe_at = now()->toDateTimeString();
            $user->superior = $pinfo->id;
            $user->superior_up = $pinfo->superior;
            $user->extension_at = now()->toDateTimeString();
            $user->extension_type = '推广二维码';
            $user->save();
        }
    }
}
