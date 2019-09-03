<?php


namespace App\Http\Controllers\Api\Index;


use App\Http\Controllers\Api\Controller;
use App\Http\Requests\CollectionRequest;
use App\Models\Collection;
use Illuminate\Http\Request;
use App\Services\BaseService;

class CollectionsController extends Controller
{
    /**
     * 用户收藏列表
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function list(Request $request)
    {
        $user_id = $this->user()->id;
        if ($request->has('user_id')) {
            $user_id = $request->user_id;
        }
        $collections = Collection::with('product:id,name,cover,price,money,ticket,state,min_unit', 'product.article:id,product_id')
            ->where(['user_id' => $user_id, 'collector_id' => $request->collector_id])
            ->get();

        return $collections;
    }

    /**
     * 收藏操作
     * @param CollectionRequest $request
     * @param BaseService $service
     */
    public function collection(CollectionRequest $request, BaseService $service)
    {
        $user_id = $this->user()->id;
        if (!$service->checkMember($this->user()->member_lock_at)) {
            $collector_count = Collection::query()
                ->where(['user_id' => $user_id, 'collector_id' => $request->collector_id])
                ->count();
            if ($collector_count >= 5) {
                return $this->response->error('非会员一个收藏夹最多能收藏5个产品', 403);
            }
        }
        $collection = Collection::query()->create([
            'user_id' => $user_id,
            'collector_id' => $request->collector_id,
            'product_id' => $request->product_id
        ]);

        return $this->response->array([
            'message' => '收藏成功',
            'collected_id' => $collection->id
        ]);
    }

    /**
     * 取消收藏
     * @param  $product_id
     * @return \Dingo\Api\Http\Response
     * @throws \Exception
     */
    public function cancelCollection($product_id)
    {
        $user_id = $this->user()->id;
        $collection = Collection::query()->where(['user_id' => $user_id, 'product_id' => $product_id])->first();
        $collection_id = $collection->id;
        $collection->delete();

        return $collection_id;
    }

    public function updateList(Request $request)
    {
        if(!$request->has('list')) {
            return $this->response->error('提交数据错误', 403);
        }
        $user_id = $this->user()->id;
        foreach ($request->list as $key => $value) {
            if ($value < 1) {
                return $this->response->error('提交数量不能为空', 403);
            }
            Collection::query()->where(['user_id' => $user_id, 'id' => $key])->update(['quantity' => $value]);
        }

        return $this->response->array([
            'message' => '操作成功'
        ]);
    }
}
