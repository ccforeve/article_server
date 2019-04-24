<?php


namespace App\Http\Controllers\Api\Index;

use App\Http\Controllers\Api\Controller;
use App\Models\Article;
use App\Models\Cash;
use App\Models\Footprint;
use App\Models\Message;
use App\Models\Order;
use App\Models\Poster;
use App\Models\Punch;
use App\Models\User;
use App\Models\UserArticle;
use Carbon\Carbon;

class TestsController extends Controller
{
    public function test()
    {
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
