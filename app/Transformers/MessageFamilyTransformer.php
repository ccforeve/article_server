<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/29 0029
 * Time: 上午 2:19
 */

namespace App\Transformers;

use App\Models\MessageFamily;
use League\Fractal\TransformerAbstract;

class MessageFamilyTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user'];

    public function transform( MessageFamily $message )
    {
        return [
            'user_id' => $message->user_id,
            'type' => $message->type,
            'name' => $message->name,
            'age' => $message->age,
            'gender' => MessageFamily::$family_gender[$message->gender],
            'phone' => $message->phone,
            'region' => $message->region,
            'message' => $message->message,
            'created_at' => $message->created_at->toDateTimeString()
        ];
    }

    public function includeUser( MessageFamily $message )
    {
        return $this->primitive(['id' => $message->user->id, 'qrcode' => $message->user->qrcode]);
    }
}
