<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/9 0009
 * Time: 上午 11:26
 */

namespace App\Http\Controllers\Api\Index;


use App\Http\Controllers\Api\Controller;
use App\Models\Message;
use App\Models\MessageFamily;
use App\Services\MessageService;
use App\Transformers\MessageFamilyTransformer;
use App\Transformers\MessageTransformer;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    public function list( MessageService $service )
    {
        $user = $this->user();
        $messages = $service->list($user->id);

        return $messages;
    }

    public function show( $message )
    {
        $message = MessageFamily::find($message);
        return $this->response->item($message, new MessageFamilyTransformer());
    }

    public function story( Request $request, MessageService $service )
    {
        $user = $this->user();
        $add = $service->story($user->id, $request);

        return $this->response->array([
            'code' => 0,
            'message' => '添加成功',
            'data' => $add
        ]);
    }

    /**
     * 家庭留言总数
     * @return mixed
     */
    public function familyMessageCount()
    {
        $count = MessageFamily::all()->count();

        return $this->response->array([
            'code' => 0,
            'count' => $count
        ]);
    }
}
