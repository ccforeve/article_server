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
//        $message = "想把事业做成功，无非两点--产品、客户。能看到这条信息的，我相信大家都有一个好的产品，剩下的就是客户≈人脉。/握手\n\n人脉怎么来？每次看到好的文章分享到朋友圈，都会发现每篇文章底部有别人的小广告，这时我就在想，同样是分享，为什么不帮自己做广告，/傲慢 为自己代言，为自己拓展人脉。\n\n有了这个想法，搭建出这么一款引流神器——【事业分享】/啤酒\n\n①所有分享自带名片，无论你分享还是他分享，都是在为你做广告——微名片；\n②每天更新和你事业产品相关的文章+海报，避免信息爆炸——文章+海报库；\n③通过手机就能查看谁浏览你转发分享的发文章，只要手机在手，随时完成客户沟通——访客记录；\n\n点击下方《事业分享》→《分享事业》\n↓↓↓↓↓↓↓↓";
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
    }
}
