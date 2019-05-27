<?php


namespace App\Http\Controllers\Api\Index;


use App\Http\Controllers\Api\Controller;
use App\Models\Activity;
use Carbon\Carbon;

class ActivityController extends Controller
{
    public function activity()
    {
        $activity = Activity::query()->latest('id')->first();
        $is_activity = false;
        if (now()->gt(Carbon::parse($activity->begin_at)) && now()->lt(Carbon::parse($activity->end_at))) {
            $is_activity = true;
        }
        return $this->response->array([
            'state' => 'ok',
            'data' => $is_activity
        ]);
    }
}
