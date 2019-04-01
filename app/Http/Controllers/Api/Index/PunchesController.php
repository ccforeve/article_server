<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/22 0022
 * Time: 下午 3:47
 */

namespace App\Http\Controllers\Api\Index;


use App\Http\Controllers\Api\Controller;
use App\Models\Punch;
use App\Models\PunchComment;
use App\Services\PunchService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class PunchesController extends Controller
{
    /**
     * 打卡首页
     * @param PunchService $punchService
     * @return mixed
     */
    public function index( PunchService $punchService )
    {
        $user = $this->user();
        // 总打卡人数
        $total = Punch::query()->punch()->count();
        // 打卡状态
        $state = $punchService->getState($user);
        // 最早打卡的13位用户
        Cache::forget('punch_top_13');
        if (Cache::has('punch_top_13')) {
            $punch_users = \Cache::get('punch_top_13');
        } else {
            $showList = Punch::with('user:id,avatar')->punch()->take(13)->orderBy('id')->get(['user_id', 'created_at']);
            $punch_users = $showList->map(function ($list) {
                $new = collect($list->user);
                return $new;
            });
            // 打卡人数超过 13 则写入缓存
            if ($showList->count() >= 13) {
                Cache::put('punch_top_13', $showList, 60);
            }
        }

        return $this->response->array([
            'code' => 0,
            'punch_total' => $total,
            'punch_state' => $state,
            'punch_users' => $punch_users
        ]);
    }

    /**
     * 打卡操作
     * @return mixed
     */
    public function punchCard()
    {
        $user = $this->user();
        $comment_id = PunchComment::query()->inRandomOrder()->take(1)->value('id');
        $punch = Punch::query()->create(['user_id' => $user->id, 'province' => $user->province, 'comment_id' => $comment_id]);
        if($punch->id) {
            return $this->response->array([
                'user' => ['id' => $user->id, 'avatar' => $user->avatar],
                'message' => '打卡成功'
            ]);
        }
    }

    /**
     * 获取省份排名
     * @return mixed
     */
    public function getProvincePunch(  )
    {
        $collections = new Collection();
        if (Cache::has('area_rank_data')) {
            $collections = Cache::get('area_rank_data');
        } else {
            $provinces = ['北京市', '天津市', '河北省', '山西省', '内蒙古自治区', '辽宁省', '吉林省', '黑龙江省', '上海市', '江苏省', '浙江省', '安徽省', '福建省', '江西省', '山东省', '河南省', '湖北省', '湖南省', '广东省', '广西壮族自治区', '海南省', '重庆市', '四川省', '贵州省', '云南省', '西藏自治区', '陕西省', '甘肃省', '青海省', '宁夏回族自治区', '新疆维吾尔自治区', '台湾省', '香港特别行政区', '澳门特别行政区'];
            // 查找每个省份最早打卡记录
            collect($provinces)->each(function ($item) use ($collections) {
                $record = Punch::with('user:id,nickname,avatar')
                    ->where('province', $item)
                    ->oldest('id')
                    ->punch()
                    ->first(['id', 'user_id', 'province', 'created_at']);

                // 记录存在添加到集合
                if ($record) {
                    $collections->put($item, $record);
                }
            });

            $collections = $collections->sortBy('id')->values();

            // 全部省、直辖市、自治区、特别行政区都打过卡了则写入缓存
            if ($collections->count() >= 34) {
                Cache::put('area_rank_data', $collections, 60);
            }
        }

        return $this->response->array(['data' => $collections]);
    }

    /**
     * 获取全国前100名打卡记录
     * @return mixed
     */
    public function getTotal()
    {
        if (Cache::has('total_rank_data')) {
            $records = Cache::get('total_rank_data');
        } else {
            $records = Punch::with('comment:id,content', 'user:id,nickname,avatar')
                ->punch()
                ->take(100)
                ->get(['created_at', 'user_id', 'comment_id']);
            // 打卡记录超过 100 条则写入缓存
            if ($records->count() >= 100) {
                Cache::put('total_rank_data', $records, 60);
            }
        }

        return $this->response->array(['records' => $records]);
    }

    public function getCurrentUserPunch()
    {
        $user = $this->user();
        // 当前用户打卡记录
        $currentUserRecord = Punch::with('comment:id,content', 'user:id,nickname,avatar')
            ->where('user_id', $user->id)
            ->punch()
            ->first(['created_at', 'user_id', 'comment_id']);
        if($currentUserRecord) {
            $rank = Punch::query()->punch()->where('created_at', '<=', $currentUserRecord->created_at)->count();
            $total_punch = Punch::query()->where('user_id', $user->id)->count();
            $currentUserRecord = collect($currentUserRecord)->put('rank', $rank);
            $currentUserRecord = collect($currentUserRecord)->put('total_punch', $total_punch);
        }

        return $this->response->array(['code' => 0, 'current_user' => $currentUserRecord]);
    }
}
