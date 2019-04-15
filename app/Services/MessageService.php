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

    public function story( $user_id, $request )
    {
        $data = $request->except('cate');
        $data['submit_user_id'] = $user_id;
        switch ($request->cate) {
            case 'normal':
                $add = Message::create($data);
                break;
            case 'family':
                $data['age'] = now()->year - Carbon::parse($request->age)->year;
                $add = MessageFamily::create($data);
                break;
        }
        return $add;
    }

}
