<?php
namespace App\Http\Traits;

use App\Jobs\UploadAvatar;
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
                if($user->id == $eventkey) {
                    $content = '扫自己的推广二维码是没用的喔';
                    message($user->openid, 'text', $content);
                    return false;
                }
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
        //把微信头像保存到本地
        dispatch(new UploadAvatar($user->id, $user->avatar));
        if($eventkey) {
            $puser_id = User::query()->where('id', $eventkey)->value('id');
            $user->superior = $puser_id;
            $user->extension_at = now()->toDateTimeString();
            $user->extension_type = '推广二维码';
            $user->save();
        }
        //保存用户
        return $user;
    }

    public function relation( $user, $eventkey )
    {
        if($user->id !== $eventkey && $user->superior == 0 && $user->type == 0) {
            $puser_id = User::query()->where('id', $eventkey)->value('id');
            //当用户本来没有推广用户和经销商的时候
            $user->subscribe = 1;
            $user->subscribe_at = now()->toDateTimeString();
            $user->superior = $puser_id;
            $user->extension_at = now()->toDateTimeString();
            $user->extension_type = '推广二维码';
            $user->save();
        }
    }
}
