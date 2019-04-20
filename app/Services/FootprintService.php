<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/10 0010
 * Time: 上午 9:12
 */

namespace App\Services;


use App\Models\Footprint;
use App\Models\User;
use Carbon\Carbon;

class FootprintService
{
    public function a()
    {
        return 'a';
    }

    public function ReadOrShare( $user_id, $type )
    {
        $messages = Footprint::with('seeUser:id,nickname,avatar', 'userArticle.article:id,title,cover')
            ->where(['user_id' => $user_id, 'type' => $type])->latest('id')->groupBy('see_user_id')->paginate(5);
        $messages->transform(function ($message) use ($user_id) {
            $value = collect($message->seeUser);
            $value->put('user_article_id', $message->user_article_id);
            $value->put('article', $message->userArticle->article);
            $value->put('created_at', $message->created_at->toDateString());
            $residence_time = Footprint::query()->where(['user_id' => $user_id, 'see_user_id' => $message->seeUser->id])->sum('residence_time');
            $time = now()->subSecond($residence_time)->diffForHumans(null, true);
            $value->put('residence_time', $time);

            return $value;
        });

        return $messages;
    }

    public function findVisitor( $user_id )
    {
        $read_count = 0;
        $share_count = 0;
        $user = User::query()->where('id', $user_id)->first(['nickname', 'avatar']);
        $footprints = Footprint::query()->where('see_user_id', $user_id)->latest('id')->get();
        foreach ($footprints as $footprint) {
            if($footprint->type === 1) {
                $read_count++;
            } elseif($footprint->type === 2) {
                $share_count++;
            }
        }
        $last_visit = $footprints->first();

        return [
            'user' => $user,
            'read_count' => $read_count,
            'share_count' => $share_count,
            'last_visit' => $last_visit->created_at->diffForHumans(),
            'relationship' => $last_visit->from ? Footprint::$relationship[$last_visit->from] : '默默关注',
        ];
    }

    public function readAndCustom($user_id)
    {
        $footprints = Footprint::query()->where('user_id', $user_id)->get();

        $today_footprint = 0;   //今日浏览
        foreach ($footprints as $footprint) {
            if($this->isToday($footprint->created_at)) {
                $today_footprint ++;
            }
        }
        //准客户
        $customer = $footprints->unique('see_user_id')->count();

        return [
            'today_footprint' => $today_footprint,
            'customer' => $customer
        ];
    }

    public function isToday( $field )
    {
        return Carbon::parse($field)->gt(today()) && Carbon::parse($field)->lt(now()->addDay()->startOfDay());
    }
}

