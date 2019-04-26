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
        dump(now()->toDateTimeString());
        $sh = \App\Models\Schedule::query()->where('send_at', now()->toDateTimeString())->first();
        dd($sh);
//        $message = "把平凡的事做好，就是不平凡，把简单的事做好，就是不简单。晚安/月亮\n晚间爆文/玫瑰\n\n🔥<a href='http://btl.yxcxin.com/article_detail/2715/public'>“4月如果你的护肤品用完了，5月份诚邀你来试试绿叶的，安全放心用！”</a>\n\n🔥<a href='http://btl.yxcxin.com/article_detail/2716/public'>“洗脸时加点绿叶牙膏，每天1次，7天之后.....”</a>\n\n🔥<a href='http://btl.yxcxin.com/article_detail/2720/public'>“家里这些隐形毒药，会让孩子变呆变傻！危害孩子健康的都是爸妈想不到的细节！”</a>\n\n🔥<a href='http://btl.yxcxin.com/article_detail/2721/public'>“人性最大的恶，是不懂感恩”</a>\n\n点击下方《事业分享》→《分享事业》\n↓↓↓↓↓↓↓↓";
//        message('oWwjo1VVjsqiLicjmHtOBZR72xgY', 'text', $message);
//        message('oWwjo1QUYuZiH6eRqvi-DImSs440', 'text', $message);
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
