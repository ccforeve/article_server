<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/9 0009
 * Time: 上午 11:43
 */

namespace App\Services;


use App\Models\Message;
use App\Models\MessageFamily;
use App\Models\User;
use Carbon\Carbon;

class MessageService
{
    public function list( $user_id, $type )
    {
        switch ($type) {
            case 'normal':
                $messages = Message::with('submitUser:id,avatar,nickname,phone')
                    ->where('user_id', $user_id)
                    ->latest('id')
                    ->paginate(5);
                $messages->transform(function ($message) {
                    $value = collect($message->submitUser);
                    $value->put('message_id', $message->id);
                    $value->put('created_at', $message->created_at->toDateTimeString());

                    return $value;
                });
                break;
            case 'family':
                $messages = MessageFamily::with('submitUser:id,avatar,nickname,phone')
                    ->where('user_id', $user_id)
                    ->latest('id')
                    ->paginate(5);
                $messages->transform(function ($message) {
                    $value = collect($message->submitUser);
                    $value->put('message_id', $message->id);
                    $value->put('created_at', $message->created_at->toDateTimeString());

                    return $value;
                });
                break;
        }

        return $messages;
    }

    public function story( $submit_user_id, $request )
    {
        $data = $request->except('cate');
        $data['submit_user_id'] = $submit_user_id;
        switch ($request->cate) {
            case 'normal':
                $add_message = Message::create($data);
                $url = "http://btl.yxcxin.com/message/{$add_message->id}/normal";
                break;
            case 'family':
                $add_message = MessageFamily::create($data);
                $url = "http://btl.yxcxin.com/message/{$add_message->id}/family";
                break;
        }
        $user = User::query()->where('id', $request->user_id)->first(['openid', 'message_send']);
        if($user->message_send && now()->lt(Carbon::parse($user->member_lock_at))) {
            $message = [
                "first" => "您收到了新的咨询",
                "keyword1" => $request->name,
                "keyword2" => now()->format('Y年m月d日'),
                "keyword3" => $request->type,
                "remark" => "请及时处理！"
            ];
            template_message($user->openid, $message, config('wechat.template.message'), $url);
        }
        return $add_message;
    }

}
