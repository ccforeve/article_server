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
        $collections = Collection::with('product')
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
            $collector_count = Collection::query()->where('user_id', $user_id)->count();
            if ($collector_count >= 3) {
                return $this->response->error('非会员一个收藏夹最多能收藏5个产品', 403);
            }
        }
        Collection::query()->create([
            'user_id' => $user_id,
            'collector_id' => $request->collector_id,
            'product_id' => $request->product_id
        ]);

        return $this->response->array([
            'error' => 0,
            'message' => '收藏成功'
        ]);
    }

    /**
     * 取消收藏
     * @param  Collection  $collection
     * @return \Dingo\Api\Http\Response
     * @throws \Exception
     */
    public function cancelCollection(Collection $collection)
    {
        $collection->delete();

        return $this->response->noContent();
    }
}
