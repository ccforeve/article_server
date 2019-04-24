<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/29 0029
 * Time: 上午 2:19
 */

namespace App\Transformers;

use App\Models\Message;
use League\Fractal\TransformerAbstract;

class MessageTransformer extends TransformerAbstract
{
    public function transform( Message $message )
    {
        return [
            'type' => $message->type,
            'name' => $message->name,
            'age' => $message->age,
            'gender' => Message::$gender[$message->gender],
            'phone' => $message->phone,
            'created_at' => $message->created_at->toDateTimeString()
        ];
    }
}
