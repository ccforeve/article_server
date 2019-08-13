<?php


namespace App\Http\Controllers\Api\Index;


use App\Http\Controllers\Api\Controller;
use App\Http\Requests\CollectorRequest;
use App\Models\Collection;
use App\Models\Collector;
use App\Services\BaseService;
use Illuminate\Http\Request;

class CollectorsController extends Controller
{
    /**
     * 收藏夹列表
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function list()
    {
        $user_id = $this->user()->id;
        $collectors = Collector::query()
            ->withCount('collections')
            ->where('user_id', $user_id)
            ->latest('id')
            ->paginate();

        return $collectors;
    }

    /**
     * 收藏夹详情
     * @param Collector $collector
     * @return Collector
     */
    public function show(Collector $collector)
    {
        $collector->with(
            'collections:id,collector_id,product_id,quantity',
            'collections.product:id,name,cover,price,money,ticket,min_unit'
        );

        return $collector;
    }

    /**
     * 新建收藏夹
     * @param CollectorRequest $request
     * @param BaseService $service
     * @return mixed
     */
    public function store(CollectorRequest $request, BaseService $service)
    {
        $user_id = $this->user()->id;
        if (!$service->checkMember($this->user()->member_lock_at)) {
            $collector_count = Collector::query()->where('user_id', $user_id)->count();
            if ($collector_count >= 3) {
                return $this->response->error('非会员最多能创建三个收藏夹', 403);
            }
        }
        Collector::query()->create([
            'user_id' => $user_id,
            'title' => $request->title,
            'desc' => $request->desc,
            'show_member_price' => $request->show_member_price
        ]);

        return $this->response->array([
            'error' => 0,
            'message' => '新建收藏夹成功'
        ]);
    }

    /**
     * 修改收藏夹
     * @param  CollectorRequest  $request
     * @return mixed
     */
    public function update(CollectorRequest $request)
    {
        $user_id = $this->user()->id;
        Collector::query()->where('user_id', $user_id)
            ->update([
                'title' => $request->title,
                'desc' => $request->desc,
                'show_member_price' => $request->show_member_price
            ]);

        return $this->response->array([
            'error' => 0,
            'message' => '修改收藏夹成功'
        ]);
    }

    /**
     * 删除收藏夹
     * @param  Collector  $collector
     * @return \Dingo\Api\Http\Response
     * @throws \Exception
     */
    public function destroy(Collector $collector)
    {
        $collector->delete();

        return $this->response->noContent();
    }

    /**
     * 复制收藏夹
     * @param Request $request
     * @param BaseService $service
     */
    public function copy(Request $request, BaseService $service)
    {
        \Validator::make($request->all(), [
            'collector_id' => 'exists:collector,id'
        ],[
            'collector_id.exists' => '没有复制的收藏夹'
        ])->validate();

        $user_id = $this->user()->id;
        if (!$service->checkMember($this->user()->member_lock_at)) {
            $collector_count = Collector::query()->where('user_id', $user_id)->count();
            if ($collector_count >= 3) {
                return $this->response->error('非会员最多能创建三个收藏夹', 403);
            }
            $collection_count = Collection::query()->where('collector_id', $request->collector_id)->count();
            if($collection_count > 5) {
                return $this->response->error('非会员复制的收藏夹中产品不可超过5个', 403);
            }
        }
        // 新建复制的收藏夹
        $copy_collector = Collector::query()->where('id', $request->collector_id)->first();
        Collector::query()->create([
            'user_id' => $user_id,
            'title' => $copy_collector->title,
            'desc' => $copy_collector->desc,
            'show_member_price' => $copy_collector->show_member_price
        ]);
        // 复制收藏夹下的收藏记录
        $collections = Collection::query()->where('collector_id', $request->collector_id)->get();
        $collection_data = [];
        foreach ($collections as $collection) {
            array_push($collection_data, [
                'user_id' => $user_id,
                'collector_id' => $collection->id,
                'product_id' => $collection->product_id,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString()
            ]);
        }
        \DB::table('collections')->insert($collection_data);

        return $this->response->array([
            'error' => 0,
            'message' => '复制收藏夹成功'
        ]);
    }
}
