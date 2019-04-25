<?php


namespace App\Http\Controllers\Api\Index;

use App\Http\Controllers\Api\Controller;
use App\Models\Article;
use App\Models\Cash;
use App\Models\Footprint;
use App\Models\Message;
use App\Models\Order;
use App\Models\Poster;
use App\Models\Product;
use App\Models\Punch;
use App\Models\User;
use App\Models\UserArticle;
use Carbon\Carbon;

class TestsController extends Controller
{
    public function test()
    {
//        $message = "当你想要放弃的时候，想想当初为什么要开始。晚安/月亮\n晚间爆文/玫瑰\n\n🔥<a href='http://btl.yxcxin.com/article_detail/17/public'>“绿叶搭配表，收藏好了”</a>\n\n🔥<a href='http://btl.yxcxin.com/article_detail/19/public'>““磷虾油”告诉您：血管是如何一天天堵塞的，看完吓一跳！血管“天敌”黑名单你有几个呢？”</a>\n\n🔥<a href='http://btl.yxcxin.com/article_detail/33/public'>“为什么美国癌症死亡率惊人下降，而我们发病率却在稳步上升！其中原因，我们真该学一学”</a>\n\n🔥<a href='http://btl.yxcxin.com/article_detail/52/public'>“家庭和睦，再穷都能发家”</a>\n\n点击下方《事业分享》→《分享事业》\n↓↓↓↓↓↓↓↓";
//        message('oWwjo1VVjsqiLicjmHtOBZR72xgY', 'text', $message);
//        $articles = Article::query()->where('product_id', 0)->get();
//        foreach ($articles as $key => $article) {
//            $article->show_at = now()->toDateTimeString();
//            $article->save();
//        }

//        $user_articles = UserArticle::with('article')->where('user_id', 20)->get();
//        foreach ($user_articles as $key => $article) {
//            $article->read_count = rand(50, 100);
//            $article->save();
//            Footprint::query()->where('user_article_id', 16)->delete();
//            $i = 0;
//            while($i < rand(50, 100)) {
//                Footprint::query()->create([
//                    'user_id' => 20,
//                    'article_id' => $article->article_id,
//                    'user_article_id' => $article->id,
//                    'see_user_id' => rand(1, 100),
//                    'type' => 1,
//                ]);
//                $i++;
//            }
//        }
//        $posters = Poster::all();
//        foreach ($posters as $poster) {
//            imgChangeBase64($poster->image_url, "{$poster->id}_{$poster->title}");
//        }
//        UserArticle::query()->truncate();
//        User::query()->truncate();
//        Footprint::query()->truncate();
//        Punch::query()->truncate();
//        Order::query()->truncate();
//        Message::query()->truncate();
//        Cash::query()->truncate();
    }
}
