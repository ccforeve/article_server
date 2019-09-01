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
    public function list(Request $request)
    {
        $user_id = $this->user()->id;
        $collectors = Collector::query()
            ->withCount('collections')
            ->where('user_id', $user_id)
            ->latest('updated_at');
        if ($request->has('list')) {
            return $collectors->get();
        }
        return $collectors->paginate(12);

    }

    /**
     * 收藏夹详情
     * @param Collector $collector
     * @return Collector
     */
    public function show(Collector $collector)
    {
//        $collector->with(
//            'collections:id,collector_id,product_id,quantity',
//            'collections.product:id,name,cover,price,money,ticket,min_unit'
//        );

        return $collector;
    }

    /**
     * 判断收藏夹
     * @param BaseService $service
     * @return \Dingo\Api\Http\Response|void
     */
    public function checkStore(BaseService $service)
    {
        $user_id = $this->user()->id;
        if (!$service->checkMember($this->user()->member_lock_at)) {
            $collector_count = Collector::query()->where('user_id', $user_id)->count();
            if ($collector_count >= 3) {
                return $this->response->error('非会员最多能创建三个收藏夹', 403);
            }
        }
        return $this->response->noContent();
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
        $collector = Collector::query()->create([
            'user_id' => $user_id,
            'title' => $request->title,
            'desc' => $request->desc,
            'show_member' => $request->show_member
        ]);
        // 收藏
        $collection = Collection::query()->create([
            'user_id' => $user_id,
            'collector_id' => $collector->id,
            'product_id' => $request->product_id
        ]);

        return $this->response->array([
            'message' => '新建收藏夹成功',
            'data' => [
                'collector' => $collector,
                'collection' => $collection
            ]
        ]);
    }

    /**
     * 修改收藏夹
     * @param CollectorRequest $request
     * @param Collector $collector
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(CollectorRequest $request, Collector $collector)
    {
        $this->authorize('update', $collector);
        $collector->update([
            'title' => $request->title,
            'desc' => $request->desc,
            'show_member' => $request->show_member
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
        $this->authorize('delete', $collector);
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
            'collector_id' => 'exists:collectors,id'
        ],[
            'collector_id.exists' => '没有复制的收藏夹'
        ])->validate();
        $user_id = $this->user()->id;
        if (!$service->checkMember($this->user()->member_lock_at)) {
            $collector_count = Collector::query()->where('user_id', $user_id)->count();
            if ($collector_count >= 3) {
                return $this->response->error('非会员最多能创建三个收藏夹！', 403);
            }
            $collection_count = Collection::query()->where('collector_id', $request->collector_id)->count();
            if($collection_count > 5) {
                return $this->response->error('非会员复制的收藏夹中产品不可超过5个！', 403);
            }
        }
        // 新建复制的收藏夹
        $copy_collector = Collector::query()->where('id', $request->collector_id)->first();
        if ($user_id == $copy_collector->user_id) {
            return $this->response->error('不能复制自己的收藏夹喔~', 403);
        }
        $collector = Collector::query()->create([
            'user_id' => $user_id,
            'title' => $copy_collector->title,
            'desc' => $copy_collector->desc,
            'show_member' => $copy_collector->show_member
        ]);
        // 复制收藏夹下的收藏记录
        $collections = Collection::query()->where('collector_id', $request->collector_id)->get();
        $collection_data = [];
        foreach ($collections as $collection) {
            array_push($collection_data, [
                'user_id' => $user_id,
                'quantity' => $collection->quantity,
                'collector_id' => $collector->id,
                'product_id' => $collection->product_id,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString()
            ]);
        }
        \DB::table('collections')->insert($collection_data);

        return $this->response->array([
            'collector_id' => $collector->id,
            'message' => '复制收藏夹成功'
        ]);
    }
}
