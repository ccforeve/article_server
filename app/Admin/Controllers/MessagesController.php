<?php


namespace App\Admin\Controllers;

use App\Http\Controllers\Api\Controller;
use App\Models\Footprint;
use App\Models\MessageFamily;
use App\Models\Order;
use App\Models\User;
use App\Models\UserArticle;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    public function store( Request $request, $user_id )
    {
        Order::query()->where('id', $request->order_id)->update(['message' => 1]);
        $data = $request->except('order_id');
        $data['user_id'] = $user_id;
        $add_message = MessageFamily::query()->create($data);
        $url = "http://btl.yxcxin.com/message/{$add_message->id}";
        $user = User::query()->where('id', $user_id)->first(['openid', 'message_send']);
        $user_article = UserArticle::query()->where('user_id', $user_id)->inRandomOrder()->first();
        Footprint::query()->create([
            'user_id' => $user_id,
            'article_id' => $user_article->article_id,
            'user_article_id' => $user_article->id,
            'see_user_id' => $request->submit_user_id,
            'residence_time' => rand(10, 200),
            'type' => 1,
            'from' => 'groupmessage',
            'created_at' => now()->subMinutes(5)->toDateTimeString()
        ]);
        if($user->message_send) {
            $message = [
                "first"    => "您收到了新的咨询",
                "keyword1" => '***',
                "keyword2" => now()->format('Y年m月d日'),
                "keyword3" => '***',
                "remark"   => "请及时处理！"
            ];
            template_message($user->openid, $message, config('wechat.template.message'), $url);
        }
    }
}
