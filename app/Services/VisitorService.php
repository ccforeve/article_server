<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/10 0010
 * Time: 下午 5:12
 */

namespace App\Services;

use App\Models\Footprint;
use App\Models\User;
use App\Models\UserArticle;
use Carbon\Carbon;

class VisitorService
{
    public function index( $user_id )
    {
        //记录列表
        $user_articles = UserArticle::with(
            'footprint:id,user_id,see_user_id,user_article_id',
            'footprint.seeUser:id,avatar',
            'article:id,cover,title'
        )
            ->has('footprint')
            ->where('user_id', $user_id)
            ->latest('id')
            ->paginate(5);
        $user_articles->transform(function ($user_article) {
            $value = collect($user_article->article);
            $value->put('id', $user_article->id);
            $value->put('read_count', $user_article->read_count);
            $value->put('created_at', $user_article->created_at->format('Y-m-d'));
            $users = $user_article->footprint->unique('see_user_id');
            $custom = count($users);
            if($custom >= 5) {
                $users = $users->random(5)->map(function ($footprint_user){
                    return $footprint_user->seeUser;
                });
            } else {
                $users = $users->map(function ($footprint_user){
                    return $footprint_user->seeUser;
                });
            }
            $value->put('user', $users);
            $value->put('custom', $custom);

            return $value;
        });

        return $user_articles;
    }

    public function userArticleShow( $user_article )
    {
        $user_article = $user_article->load('article:id,title,cover,read_count,created_at');
        $footprints = Footprint::with('user:id,nickname,avatar', 'seeUser:id,nickname,avatar')
            ->where(['user_article_id' => $user_article->id, 'type' => 1])
            ->select('id', 'user_article_id', 'user_id', 'see_user_id', 'share_id', 'residence_time', 'created_at')
            ->paginate(5);
        $footprints->transform(function ($footprint) {
            if($footprint->share_id) {
                $child = $this->extension_user($footprint);
            }
            $value = collect($footprint);
            $value->put('child', $child ?? '');

            return $value;
        });

        return [
            'article' => [
                'title' => $user_article->article->title,
                'cover' => $user_article->article->cover,
                'read_count' => $user_article->article->read_count,
                'created_at' => $user_article->article->created_at->diffForhumans(),
            ],
            'footprint' => $footprints
        ];
    }

    public function extension_user($footprint, &$result = [], $deep = 0)
    {
        $deep += 1;
        $share_user = Footprint::with('seeUser:id,nickname,avatar')
            ->select('user_id', 'see_user_id', 'share_id')
            ->where('id', $footprint['share_id'])
            ->first();
        if(isset($share_user->share_id)) {
            if($share_user->share_id != $share_user->user_id) {
                $result[ $deep ] = $share_user->toarray();
                $this->extension_user($share_user, $result, $deep);
            }
        }

        return array_reverse($result);
    }

    public function alsoRead( $user_id )
    {
        $user = User::query()->where('id', $user_id)->first(['nickname', 'avatar', 'phone', 'wechat']);
        $footprints = Footprint::with(
            'userArticle:id,article_id',
            'userArticle.article:id,title,cover,created_at'
        )
            ->select('id', 'user_article_id', 'residence_time', 'created_at')
            ->where(['see_user_id' => $user_id, 'type' => 1])
            ->latest('id')
            ->paginate(5);

        $footprints->transform(function ($footprint) {
            $article = $footprint->userArticle->article;
            $value = collect($article);
            $value->put('user_article_id', $footprint->user_article_id);
            $value->put('created_at', $footprint->created_at->toDateString());
            $value->put('residence_time', Carbon::now()->subSecond($footprint->residence_time)->diffForHumans(null, true));

            return $value;
        });

        return [
            'user' => $user,
            'footprint' => $footprints
        ];
    }

    /**
     * 访客记录
     * @param $user_id
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function prospect( $user_id )
    {
        $prospects = Footprint::with('seeUser:id,avatar,nickname')
            ->where('user_id', $user_id)
            ->groupBy('see_user_id')
            ->latest('id')
            ->paginate(5);
        $prospects->transform(function ($prospect) use ($user_id) {
            $new = collect($prospect);
            $last_read = Footprint::with('article:id,title,cover,read_count')
                ->where(['user_id' => $user_id, 'see_user_id' => $prospect->see_user_id])
                ->select('id', 'user_id', 'see_user_id', 'article_id', 'created_at')
                ->latest('id')->first();
            $new->put('article', $last_read->article);
            $new->put('last_read_at', $last_read->created_at->toDateTimeString());

            return $new;
        });

        return $prospects;
    }

    /**
     * 查看是否有新的访客
     * @param $user_id
     * @return array
     */
    public function ifNewVisitor( $user_id )
    {
        $if_new_visitor = Footprint::query()->where(['user_id' => $user_id, 'type' => 1])->latest('id')->first();
        if($if_new_visitor) {
            if($if_new_visitor->new === 0) {
                return ['code' => 200, 'data' => true, 'message' => '有新的访客'];
            }
        }

        return ['code' => 200, 'data' => false, 'message' => '没有新的访客'];
    }

    /**
     * 更新访客查看状态
     * @param $user_id
     */
    public function updateNewState( $user_id )
    {
        $if_new_visitor = Footprint::query()->where(['user_id' => $user_id, 'type' => 1])->latest('id')->first();
        $if_new_visitor->new = 1;
        $if_new_visitor->save();
    }
}
