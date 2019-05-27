<?php

namespace App\Http\Controllers\Api\Index;

use App\Http\Controllers\Api\Controller;
use App\Models\ActivityDraw;
use Illuminate\Http\Request;

class ActivityDrawController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function store( Request $request )
    {
        $user = $this->user();
        $activity = ActivityDraw::query()->create([
            'user_id' => $user->id,
            'prize' => $request->prize,
            'name' => $user->nickname,
            'phone' => $user->phone,
            'activity_id' => 1
        ]);

        return $activity;
    }

    /**
     * 最新100条记录
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function list()
    {
        $draws = ActivityDraw::query()->where('activity_id', 1)->latest('id')->take(100)->get();

        return $draws;
    }
}
