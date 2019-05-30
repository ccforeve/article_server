<?php


namespace App\Http\Controllers\Api\Index;

use App\Http\Controllers\Api\Controller;
use App\Models\User;
use Carbon\Carbon;
use EasyWeChat\OfficialAccount\Application;

class TestsController extends Controller
{
    public function test(Application $app)
    {
        $first_time = Carbon::parse('2019-05-30 19:00:00')->subSeconds(10)->toDateTimeString();
        $second_time = Carbon::parse('2019-05-30 19:00:00')->addSeconds(10)->toDateTimeString();

        $sh = \App\Models\Schedule::query()->whereBetween('send_at', [$first_time, $second_time])->first();
        dd($sh);
    }
}
