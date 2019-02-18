<?php

namespace App\Http\Controllers\Api\Index;

use App\Models\Footprint;
use App\Services\FootprintService;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

class FootprintsController extends Controller
{
    public function read( FootprintService $service )
    {
        $user = $this->user();

        return $service->ReadOrShare($user->id, 1);
    }

    public function share( FootprintService $service )
    {
        $user = $this->user();

        return $service->ReadOrShare($user->id, 2);
    }

    public function findVisitor( FootprintService $service, $user_id )
    {
        return $service->findVisitor($user_id);
    }

    /**
     * 今日浏览和准客户
     * @param FootprintService $service
     * @return mixed
     */
    public function readAndCustom( FootprintService $service )
    {
        $user = $this->user();

        return $service->readAndCustom($user->id);
    }

    /**
     * 实时更新阅读文章时间
     * @param Request $request
     * @param Footprint $footprint
     * @return mixed
     */
    public function updateReadTime( Request $request, Footprint $footprint )
    {
        $footprint->residence_time = $request->read_time;
        $footprint->save();

        return $this->response->array([
            'code' => '201',
            'message' => '已更新'
        ]);
    }
}
