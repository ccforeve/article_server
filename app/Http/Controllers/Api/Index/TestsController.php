<?php


namespace App\Http\Controllers\Api\Index;

use App\Http\Controllers\Api\Controller;
use App\Models\User;
use EasyWeChat\OfficialAccount\Application;

class TestsController extends Controller
{
    public function test(Application $app)
    {
        $users = User::query()->where(['unionid' => '', 'subscribe' => 1])->get();
        dd($users->count());
        $user = $app->user->get('oWwjo1VVjsqiLicjmHtOBZR72xgY');
        dd($user);
    }
}
