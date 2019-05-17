<?php


namespace App\Admin\Controllers;

use App\Http\Controllers\Api\Controller;
use App\Jobs\DeleteMessage;
use App\Models\Footprint;
use App\Models\MessageFamily;
use App\Models\Order;
use App\Models\User;
use App\Models\UserArticle;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    public function orderSendMessage( Request $request, Order $order )
    {
        $order->update(['message' => 1]);
        $data = $request->all();
        $data['user_id'] = $order->user_id;
        $data['show'] = 1;
        $this->store($data, $order->user_id);
    }

    public function userSendMessage( Request $request, User $user )
    {
        $user->increment('message');
        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['show'] = 1;
        $this->store($data, $user->id);
    }

    public function store( $data, $user_id )
    {
        $add_message = MessageFamily::query()->create($data);
        $url = "http://btl.yxcxin.com/message/{$add_message->id}";
        $user = User::query()->where('id', $user_id)->first(['openid', 'message_send']);
        $user_article = UserArticle::query()->where('user_id', $user_id)->inRandomOrder()->first();
        Footprint::query()->create([
            'user_id' => $user_id,
            'article_id' => $user_article->article_id,
            'user_article_id' => $user_article->id,
            'see_user_id' => $add_message->submit_user_id,
            'residence_time' => rand(10, 200),
            'type' => 1,
            'from' => 'groupmessage',
            'created_at' => now()->subMinutes(5)->toDateTimeString()
        ]);
        if($user->message_send) {
            $message = [
                "first"    => "您收到了新的咨询",
                "keyword1" => $add_message->name,
                "keyword2" => now()->format('Y年m月d日'),
                "keyword3" => $add_message->message,
                "remark"   => "请及时处理！"
            ];
            template_message($user->openid, $message, config('wechat.template.message'), $url);
            dispatch(new DeleteMessage($add_message->id))->delay(now()->addDay());
        }
    }
}
