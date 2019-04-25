<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/29 0029
 * Time: 上午 2:19
 */

namespace App\Transformers;

use App\Models\User;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform( User $user )
    {
        $user_data = [
            'id' => $user->id,
            'openid' => $user->openid,                               //微信openid
            'is_subscribe' => $user->subscribe,
            'nickname' => $user->nickname,
            'sex' => $user->sex,
            'avatar' => $user->avatar,
            'phone' => $user->phone,
            'wechat' => $user->wechat,
            'message_send' => $user->message_send,
            'wechat_qrcode' => $user->qrcode,
            'province' => $user->province,
            'employed_area' => $user->employed_area,
            'profession' => $user->profession,
            'type' => $user->type,
            'ali_account' => $user->ali_account,
            'ali_name' => $user->ali_name,
            'member_lock_at' => Carbon::parse($user->member_lock_at)->toDateString(),
            'is_member' => (Carbon::parse($user->member_lock_at)->gt(now()) ? 1 : 0) ,
            'integral_scale' => $user->integral_scale,
        ];
        return $user_data;
    }

}
